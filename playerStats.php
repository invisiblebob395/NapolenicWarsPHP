<?php
    require_once "database.php";
    $serverName = "127.0.0.1";
    $username = "root";
    $password = "shuyun88";
    $database = "napwars";
    define("DEFAULT_MONEY", 10000);
    function getPlayerKills($guid) {
        $result = getFromDatabase("players", "kills", array("guid"), array($guid), 0);
        if ($row = $result->fetch_row()) return $row[0];
        else return -1;
    }
    function getPlayerDeaths($guid) {
        $result = getFromDatabase("players", "deaths", array("guid"), array($guid), 0);
        if ($row = $result->fetch_row()) return $row[0];
        else return -1;
    }
    function getPlayerMoney($guid) {
        $result = getFromDatabase("players", "money", array("guid"), array($guid), 0);
        if ($row = $result->fetch_row()) return $row[0];
        else return -1;
    }
    function isRegistered($guid) {
        return getPlayerMoney($guid) > -1;
    }
    function getPlayerRank($guid, $rankBy) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "SELECT * FROM players ORDER BY $rankBy DESC";
        $result = $conn->query($query);
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            if ($rankBy == 'kills' && $row['kills'] < 30) return 0;
            elseif ($rankBy == 'money' && $row['money'] < 10001) return 0;
            elseif ($rankBy == 'deaths' && $row['deaths'] < 30) return 0;
            if ($row['guid'] == $guid) return $i;
            $i++;
        }
        return 0;
    }
    function getTopRanking($rankby) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "SELECT * FROM players ORDER BY $rankby DESC";
        $result = $conn->query($query);
        return $result;
    }
    function getPlayerInfo($guid) {
        return getFromDatabase("players", "*", array("guid"), array($guid), 0);
    }
    function addKills($guid, $kills) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE players SET kills = kills + $kills WHERE guid = $guid";
        return $conn->query($query);
    }
    function addDeaths($guid, $deaths) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE players SET deaths = deaths + $deaths WHERE guid = $guid";
        return $conn->query($query);
    }
    function killTransferDenar($from, $to){
        global $serverName, $username, $password, $database;
        $moneyFrom = getPlayerMoney($from);
        $moneyFrom = min(abs($moneyFrom/20), 500);
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE players SET money = money + $moneyFrom + 250 WHERE guid = $to";
        $query2 = "UPDATE players SET money = money - $moneyFrom WHERE guid = $from";
        $conn->query($query);
        $conn->query($query2);
    }
    function registerPlayer($guid, $username) {
        return insertToDatabase("players", array("guid", "username", "kills", "deaths", "money"), array($guid, $username, 0, 0, DEFAULT_MONEY));
    }
    function addDenars($guid, $amount) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE players SET money = money + $amount WHERE guid = $guid";
        $conn->query($query);
    }
    function setUsername($guid, $username2) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $username2 = $username2 . "";
        $query = $conn->prepare("UPDATE players SET username = ? WHERE guid = ?");
        $query->bind_param("si", $username2, $guid);
        $query->execute();
    }
    function setNickname($guid, $nickname) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = $conn->prepare("UPDATE players SET nickname = ? WHERE guid = ?");
        $query->bind_param("si", $nickname, $guid);
        $query->execute();
        return $conn->affected_rows > 0;
    }
    function setTitle($guid, $title) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = $conn->prepare("UPDATE players SET title = ? WHERE guid = ?");
        $query->bind_param("si", $title, $guid);
        $query->execute();
        return $conn->affected_rows > 0;
    }
    function setColor($guid, $color) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = $conn->prepare("UPDATE players SET color = ? WHERE guid = ?");
        $query->bind_param("si", $color, $guid);
        $query->execute();
        return $conn->affected_rows > 0;
    }
    function getSkin($guid, $skinNumber) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "SELECT * FROM skins WHERE guid=$guid AND skinIndex=$skinNumber";
        $result = $conn->query($query);
        return $result;
    }
    function setSkin($guid, $skinNumber, $head, $body, $leg, $hand) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE skins SET head = $head, body = $body, leg = $leg, hand = $hand WHERE guid = $guid AND skinIndex = $skinNumber";
        $conn->query($query);
        return $conn->affected_rows > 0;
    }
    function addSkin($guid, $skinNumber) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "INSERT INTO skins (skinIndex, guid) VALUES ($skinNumber, $guid)";
        $conn->query($query);
        $query2 = "UPDATE players SET skins = skins + 1 WHERE guid = $guid";
        $conn->query($query2);
    }
    function setVipLevel($guid, $level) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        $query = "UPDATE players SET VIP = $level WHERE guid = $guid";
        $conn->query($query);
        return $conn->affected_rows > 0;
    }
?>