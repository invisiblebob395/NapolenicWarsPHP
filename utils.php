<?php
function getRankBy($id) {
    $rankBy = "";
    switch ($_GET['rankBy']) {
        case 1:
            $rankBy = "kills";
            break;
        case 2:
            $rankBy = "deaths";
            break;
        case 3:
            $rankBy = "money";
            break;
        default:
            $rankBy = "kills";
    }
    return $rankBy;
}
?>