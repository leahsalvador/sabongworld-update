DELIMITER $$
drop TRIGGER if EXISTS calculate_bet_insert;
create trigger calculate_bet_insert
    after insert
    on betting_histories
    for each row
begin
    if (new.side = 'heads') THEN
        update game_rounds
        set total_bet_heads = (select sum(amount)
                               from betting_histories
                               where side = 'heads' and game_rounds_id = new.game_rounds_id)
        where id = new.game_rounds_id;
    elseif (new.side = 'tails') THEN
        update game_rounds
        set total_bet_tails = (select sum(amount)
                               from betting_histories
                               where side = 'tails' and game_rounds_id = new.game_rounds_id)
        where id = new.game_rounds_id;
    end if;

end;

DELIMITER $$;
