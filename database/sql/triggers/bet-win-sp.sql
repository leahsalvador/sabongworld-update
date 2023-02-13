DELIMITER $$
drop procedure if exists `game_win`;
CREATE PROCEDURE `game_win`(IN `game_id` int)
BEGIN
    DECLARE cursor_ID INT;
    DECLARE cursor_VAL decimal(12, 2);
    DECLARE done INT DEFAULT FALSE;
    DECLARE cursor_i CURSOR FOR select id, amount
                                from betting_histories
                                where game_rounds_id = game_id
                                  and side = (select winner from game_rounds where id = game_id limit 1);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cursor_i;
    read_loop:
    LOOP
        FETCH cursor_i INTO cursor_ID, cursor_VAL;
        IF done THEN
            LEAVE read_loop;
        END IF;
        select cursor_ID, cursor_VAL;
        set @winner = (select winner from game_rounds where id = game_id limit 1);
        set @player_id = (select player_id from betting_histories where id = cursor_ID);
        IF @winner = 'tails' THEN
            set @payout = (select ((((total_bet_heads / total_bet_tails) * .90) * cursor_VAL) + cursor_VAL)
                           from game_rounds
                           where id = game_id);
            set @comission = (select (((total_bet_heads / total_bet_tails) * cursor_VAL) - @payout)
                              from game_rounds
                              where id = game_id);
        ELSE
            set @payout = (select (((total_bet_tails / total_bet_heads) * .90) * cursor_VAL) + cursor_VAL
                           from game_rounds
                           where id = game_id);
            set @comission = (select (((total_bet_tails / total_bet_heads) * cursor_VAL) - - @payout)
                              from game_rounds
                              where id = game_id);
        END IF;

        set @wallet_id = (select id from wallets where user_id = @player_id);
        set @wallet_points = (select points + @payout from wallets where user_id = @player_id);
        set @agent_id = (select id from users where `code` = (select referral_id from users where id = @player_id));
        set @agent_referral_code =
                (select referral_id from users where `code` = (select referral_id from users where id = @agent_id));
        set @admin_comission = (@comission * .80);
        set @admin_comission_total = (select comission + @admin_comission from wallets where user_id = 1);

        update wallets set comission = @admin_comission_total where id = 1;

        insert into transactions (user_from, user_to, details, amount, `type`, transaction_type, transaction_status,
                                  created_at)
        values (@player_id, 1, 'comission', @admin_comission, 'comission', 'deposit', 'success', now());

        if @agent_referral_code IS NOT NULL then
            set @agent_wallet_id = (select id from wallets where user_id = @agent_id);
            set @agent_comission = ((@comission - @admin_comission) * .50);
            set @agent_comission_total = (select comission + @agent_comission from wallets where user_id = @agent_id);

            set @master_agent_comission = ((@comission - @admin_comission) - @agent_comission);
            set @master_agent = (select id from users where `code` = @agent_referral_code);
            set @master_agent_wallet_id = (select id from wallets where user_id = @master_agent);

            set @master_agent_comission_total =
                    (select comission + @master_agent_comission from wallets where user_id = @master_agent);

            update wallets set comission = @agent_comission_total where id = @agent_wallet_id;

            update wallets set comission = @master_agent_comission_total where id = @master_agent_wallet_id;

            insert into transactions (user_from, user_to, details, amount, `type`, transaction_type, transaction_status,
                                      created_at)
            values (@player_id, @agent_id, 'comission', @agent_comission, 'comission', 'deposit', 'success', now());

            insert into transactions (user_from, user_to, details, amount, `type`, transaction_type, transaction_status,
                                      created_at)
            values (@master_agent, @master_agent, 'comission', @master_agent_comission, 'comission', 'deposit',
                    'success', now());
        else
            set @agent_wallet_id = (select id from wallets where user_id = @agent_id);
            set @agent_comission = (@comission - @admin_comission);
            set @agent_comission_total = (select comission + @agent_comission from wallets where user_id = @agent_id);
            set @master_agent_comission = 0;
            set @master_agent = 0;

            insert into transactions (user_from, user_to, details, amount, `type`, transaction_type, transaction_status,
                                      created_at)
            values (@player_id, @agent_id, 'comission', @agent_comission, 'comission', 'deposit', 'success', now());

            update wallets set comission = @agent_comission_total where id = @agent_wallet_id;
        end if;


        update wallets set points = @wallet_points where id = @wallet_id;
        update betting_histories set status = 'win', win_amount = @payout where id = cursor_ID;

        select cursor_ID,
               cursor_VAL,
               @winner,
               @player_id,
               @wallet_id,
               @payout,
               @wallet_points,
               @comission;
    END LOOP;
    CLOSE cursor_i;

END$$

