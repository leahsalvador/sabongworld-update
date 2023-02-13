DROP EVENT if EXISTS flip_coin;
CREATE EVENT flip_coin
    ON SCHEDULE EVERY 5 SECOND
    DO
    BEGIN
        IF TIME(now()) >= str_to_date("6", '%h%i%s') AND TIME(now()) <= str_to_date("8", '%h%i%s') THEN
            SELECT 'No game set yet';
        ELSE
            SET @curr_game_id = (select id from game_rounds ORDER BY id DESC limit 1);
            IF (select status from game_rounds where id = @curr_game_id) = 'done' OR
               (select status from game_rounds where id = @curr_game_id) = 'cancelled' THEN
                IF (select DATEDIFF(now(), created_at) from game_rounds where id = @curr_game_id) = 0 THEN
                    INSERT INTO game_rounds (`round`, status, created_at)
                    select (`round` + 1), 'open', NOW()
                    from game_rounds
                    where id = @curr_game_id;
                ELSE
                    INSERT INTO game_rounds (`round`, status, created_at)
                    select 1, 'open', NOW()
                    from game_rounds
                    where id = @curr_game_id;
                END IF;
            END IF;

            IF (select winner from game_rounds where id = @curr_game_id) = 'none' THEN

                IF (select status from game_rounds where id = @curr_game_id) = 'open' OR
                   (select status from game_rounds where id = @curr_game_id) = 'final-bet' THEN
                    UPDATE game_rounds
                    SET status = (CASE
                                      WHEN TIMEDIFF(now(), created_at) >= '00:01:30' &&
                                           TIMEDIFF(now(), created_at) <= '00:02:00' THEN 'final-bet'
                                      WHEN TIMEDIFF(now(), created_at) >= '00:02:01' THEN 'closed'
                                      ELSE 'open'
                        END)
                    where id = @curr_game_id;
                END IF;

                IF (select status from game_rounds where id = @curr_game_id) = 'closed' THEN
                    IF (select head_payout from game_rounds where id = @curr_game_id) < 22 OR
                       (select tails_payout from game_rounds where id = @curr_game_id) < 22 THEN
                        UPDATE game_rounds SET winner = 'none', status = 'cancelled' where id = @curr_game_id;
                    ELSE
                        UPDATE game_rounds
                        SET coin1 = (FLOOR(RAND() * (3 - 1) + 1)),
                            coin2 = (FLOOR(RAND() * (3 - 1) + 1))
                        where id = @curr_game_id;
                    END IF;
                END IF;

                IF (select coin1 from game_rounds where id = @curr_game_id) = 1 AND
                   (select coin2 from game_rounds where id = @curr_game_id) = 1 THEN
                    UPDATE game_rounds SET winner = 'heads', status = 'done' where id = @curr_game_id;
                    update betting_histories
                    set status = 'loose'
                    where game_rounds_id = @curr_game_id and side != 'heads';
                    call game_win(@curr_game_id);

                END IF;

                IF (select coin1 from game_rounds where id = @curr_game_id) = 2 AND
                   (select coin2 from game_rounds where id = @curr_game_id) = 2 THEN
                    UPDATE game_rounds SET winner = 'tails', status = 'done' where id = @curr_game_id;
                    update betting_histories
                    set status = 'loose'
                    where game_rounds_id = @curr_game_id and side != 'tails';
                    call game_win(@curr_game_id);
                END IF;
                if (select total_bet_tails from game_rounds where id = @curr_game_id != 0) and
                   (select total_bet_heads from game_rounds where id = @curr_game_id != 0) THEN
                    update game_rounds
                    set head_payout  = (((total_bet_tails / total_bet_heads) * .90) * 20) + 20,
                        tails_payout = (((total_bet_heads / total_bet_tails) * .90) * 20) + 20
                    where id = @curr_game_id;
                end if;
            END IF;
        END IF;

    END;
