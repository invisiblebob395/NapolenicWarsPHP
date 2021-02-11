<?php
require_once "utils.php";
require_once "playerStats.php";
$serverName = "127.0.0.1";
$username = "root";
$password = "shuyun88";
$database = "napwars";
function getAllItems() {
    global $serverName, $username, $password, $database;
    $conn = new mysqli($serverName, $username, $password, $database);
    $query = "SELECT * FROM market";
    return $conn->query($query);
}
function getPriceOfItem($index) {
    global $serverName, $username, $password, $database;
    $conn = new mysqli($serverName, $username, $password, $database);
    $query = "SELECT * FROM market WHERE index=$index";
    $result = $conn->query($query);
    if ($row = $result->fetch_assoc()) return $row['price'];
    return null;
}
function purchaseItem($guid, $index) {
    $price = getPriceOfItem($index);
    if (getPlayerMoney($guid) < $index) return FALSE;

}
?>