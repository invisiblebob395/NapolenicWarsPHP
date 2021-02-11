<?php
//roulette system -- place bet --> echo return (1 or 0) --> add to table --> evaluate results --> execute statements
require_once "utils.php";
function placeBet($guid, $amount) {
    if (getPlayerMoney($guid) >= $amount) {
        addDenars($guid, -$amount);
        return TRUE;
    }
    return FALSE;
}
