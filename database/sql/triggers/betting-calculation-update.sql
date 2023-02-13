DROP TRIGGER if EXISTS calculate_bet_update;

CREATE TRIGGER calculate_bet_update
    AFTER UPDATE
    ON betting_histories
    FOR EACH ROW
BEGIN
    IF (new.side = 'heads') THEN
        UPDATE game_rounds
        SET total_bet_heads = (SELECT SUM(amount)
                               FROM betting_histories
                               WHERE side = 'heads'
                                 AND game_rounds_id = new.game_rounds_id)
        where id = new.game_rounds_id;
    ELSEIF (new.side = 'tails') THEN
        UPDATE game_rounds
        SET total_bet_tails = (SELECT sum(amount)
                               FROM betting_histories
                               WHERE side = 'tails'
                                 AND game_rounds_id = new.game_rounds_id)
        WHERE id = new.game_rounds_id;
    END IF;
END;

