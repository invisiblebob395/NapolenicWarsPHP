<?php
    require_once "playerStats.php";
    require_once "utils.php";
    require_once "economy.php";
    define("PLAYER_JOINED_EVENT", 1);
    define("PLAYER_GET_STATS", 2);
    define("PLAYER_GET_OWN_RANKING", 3);
    define("PLAYER_INCREMENT_KILLS", 4);
    define("PLAYER_INCREMENT_DEATHS", 5);
    define("PLAYER_GET_LEADERBOARD", 6);
    define("PLAYER_CHANGE_USERNAME", 7);
    define("PLAYER_KILLED_DENAR", 8);
    define("ADD_DENARS", 9);
    define("PLAYER_PLACE_BET", 10);
    define("PLAYER_ADD_SKIN", 11);
    define("SET_VIP", 12);
    define("SET_NICKNAME", 13);
    define("SET_COLOR", 14);
    define("SET_SKIN", 15);
    define("GET_SKIN", 16);
    define("SET_TITLE", 18);
    define("TRANSFER_DENAR", 19);
    header_remove();
    //GET request should have 'type' as well as 'id' and 'guid' if needed
    if (($_SERVER['REMOTE_ADDR'] == "127.0.0.1" OR $_SERVER['REMOTE_ADDR'] == 'localhost') and isset($_GET['type']) and isset($_GET["id"])) {
        //proper request should be -- 127.0.0.1/api.php?type=1&guid=guid&id=id&username=username
        if ($_GET['type'] == PLAYER_JOINED_EVENT) {
            if (!isRegistered($_GET["guid"])) registerPlayer($_GET["guid"], $_GET["username"]);
            $playerStats = getPlayerInfo($_GET["guid"]);
            $id = $_GET["id"];
            if ($row = $playerStats->fetch_assoc()) {
                $guid = $row['guid'];
                $kills = $row['kills'];
                $deaths = $row['deaths'];
                $money = $row['money'];
                $rank = getPlayerRank($guid, "kills");
                $moneyRank = getPlayerRank($guid, "money");
                $username2 = $_GET['username'];
                $vip = $row['VIP'];
                $nickname = $row['nickname'];
                $color = $row['color'];
                $title = $row['title'];
                $skins = $row['skins'];
                $welcome = $row['welcome'];
                if ($row['username'] != $_GET['username']) setUsername($guid, $username2);
                $returnString =  "1|$id|$guid|$kills|$deaths|$money|$username2|$rank|$moneyRank|$vip|$color|$title|$skins|$nickname|$welcome";
                echo $returnString;
            }
        } elseif ($_GET['type'] == PLAYER_GET_STATS) {
            //proper request should be -- 127.0.0.1/api.php?type=2&guid=guid&id=id
            $playerStats = getPlayerInfo($_GET["guid"]);
            $id = $_GET["id"];
            if ($row = $playerStats->fetch_assoc()) {
                $guid = $row['guid'];
                $kills = $row['kills'];
                $deaths = $row['deaths'];
                $money = $row['money'];
                $rank = getPlayerRank($guid, "kills");
                $moneyRank = getPlayerRank($guid, "money");
                $username = $row['username'];
                echo "2|$id|$guid|$kills|$deaths|$money|$username|$rank|$moneyRank";
            } else echo "2|$id|0|0|0|0";
        } elseif ($_GET['type'] == PLAYER_GET_OWN_RANKING) {
            //proper request should be -- 127.0.0.1/api.php?type=3&guid=guid&id=id&rankBy=INT
            $guid = $_GET['guid'];
            $id = $_GET['id'];
            $rankBy = getRankBy($_GET['rankBy']);
            $rank = getPlayerRank($guid, $rankBy);
            echo "3|$rankBy|$id|$rank|";
        } elseif ($_GET['type'] == PLAYER_INCREMENT_KILLS) {
            //proper request should be -- 127.0.0.1/api.php?type=3&guid=guid&id=id&kills=kills
            $guid = $_GET['guid'];
            $killsAdded = $_GET['kills'];
            addKills($guid, $killsAdded);
        } elseif ($_GET['type'] == PLAYER_INCREMENT_DEATHS) {
            $guid = $_GET['guid'];
            addDeaths($guid, 1);
        } elseif ($_GET['type'] == PLAYER_GET_LEADERBOARD) {
            //127.0.0.1/api.php?type=6&guid=gukd&id=id&rankBy=INT
            $guid = $_GET['guid'];
            $id = $_GET['id'];
            $rankBy = getRankBy($_GET['rankBy']);
            $rank = getPlayerRank($guid, $rankBy);
            $topLeaderboard = getTopRanking($rankBy);
            $i = 1;
            $returnString = "6|$id|$rank|";
            while($row = $topLeaderboard->fetch_assoc() and $i <= 10) {
                $returnString = $returnString . $row['username'] . "|" . $row["$rankBy"] . "|";
                $i++;
            }
            echo $returnString;
        } elseif($_GET['type'] == PLAYER_CHANGE_USERNAME){
            //127.0.0.1/api.php?type=7&guid=guid&username=username
            setUsername($_GET['guid'], $_GET['username']);
        }elseif($_GET['type'] == PLAYER_KILLED_DENAR) {
            //127.0.0.1/api.php?type=8&killerid=killerid&deadid=deadid
            $guidKiller = $_GET['killerid'];
            $guidDead = $_GET['deadid'];
            killTransferDenar($guidDead, $guidKiller);
        } elseif ($_GET['type'] == ADD_DENARS) {
            addDenars($_GET['guid'], $_GET['amount']);
        } elseif ($_GET['type'] == PLAYER_PLACE_BET) {
            //127.0.0.1/api.php?type=10&guid=guid&id=id&amount=amount&squares=squares
            $amount = $_GET['amount'];
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $squares = $_GET['squares'];
            if (placeBet($guid, $amount)) {
                echo "10|$guid|$id|$amount|1|$squares";
            } else {
                echo "10|$guid|$id|$amount|0|$squares"; //not enough money, returns 0 value
            }
        } elseif ($_GET['type'] == PLAYER_ADD_SKIN) {
            //127.0.0.1/api.php?type=11&guid=guid&id=id
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $playerStats = getPlayerInfo($guid);
            if ($row = $playerStats->fetch_assoc()) {
                if (($row['VIP'] > 0 && $row['skins'] < 1) || ($row['VIP'] == 2 && $row['skins'] < 5) ||($row['VIP'] >= 3)) {
                    $skinNumber = $row['skins'] + 1;
                    addSkin($guid, $skinNumber);
                    echo "11|$id|$guid|$skinNumber|1";
                } else echo "11|$id|$guid|0|0";
            }
            else echo "11|$id|$guid|0|0";
        } elseif ($_GET['type'] == SET_VIP) {
            $guid = $_GET['guid'];
            $id = $_GET['id'];
            $vipLevel = $_GET['vip'];
            $returnString = "12|$id|$vipLevel|$guid|";
            if (!setVipLevel($guid, $vipLevel)) echo $returnString . "0";
            else echo $returnString . "1";
        } else if ($_GET['type'] == SET_NICKNAME) {
            //127.0.0.1/api.php?type=13&guid=guid&id=id&nickname=nickname
            $guid = $_GET['guid'];
            $id = $_GET['id'];
            $nickname = $_GET['nickname'];
            $returnString = "13|$id|$guid|$nickname|";
            if (setNickname($guid, $nickname)) echo $returnString . "1";
            else echo $returnString . "0";
        } else if ($_GET['type'] == SET_COLOR) {
            //127.0.0.1/api.php?type=14&guid=guid&id=id&color=nickname
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $color = $_GET['color'];
            $returnString = "14|$id|$guid|$color|";
            if (setColor($guid, $color)) echo $returnString . "1";
            else echo $returnString . "0";
        } else if ($_GET['type'] == SET_SKIN) {
            //127.0.0.1/api.php?type=15&guid=guid&id=id&skinNum=skinNum&head=head&body=body&leg=leg&hand=hand
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $skinNum = $_GET['skinNum'];
            $returnString = "15|$id|$guid|$skinNum|";
            if (setSkin($guid, $skinNum, $_GET['head'], $_GET['body'], $_GET['leg'], $_GET['hand'])) echo $returnString . "1";
            else echo $returnString . "0";
        } else if ($_GET['type'] == GET_SKIN) {
            //127.0.0.1/api.php?type=16&guid=guid&id=id&skinNum=skinNum&equip=equip
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $skinResult = getSkin($guid, $_GET['skinNum']);
            $returnString = $_GET['type'] + $_GET['equip'] . "|$id|$guid|";
            if ($skinRow = $skinResult->fetch_assoc()) {
                $returnString = $returnString . "1|" . $skinRow['head'] . "|" . $skinRow['body'] . "|" . $skinRow['leg'] . "|" . $skinRow['hand'] . "|" . $skinRow['weapon1'] . "|" . $skinRow['weapon2'] . "|" . $skinRow['weapon3'] . "|" . $skinRow['weapon4'] . "|" . $skinRow['horse'];
                echo $returnString;
            }else echo $returnString . "0";
        } else if($_GET['type'] == SET_TITLE) {
            //127.0.0.1/api.php?type=18&guid=guid&id=id&title=title
            $id = $_GET['id'];
            $guid = $_GET['guid'];
            $returnString = "18|$id|$guid|";
            if (setTitle($guid, $_GET['title'])) echo $returnString . "1";
            else echo $returnString . "0";

        } else if ($_GET['type'] == TRANSFER_DENAR) {
            //127.0.0.1/api.php?type=19&guid=guid&secondguid=secondguid&title=title&amount=amount&id=id
            $id = $_GET['id'];
            $secondguid = $_GET['secondguid'];
            if (isRegistered($secondguid) && getPlayerMoney($_GET['guid']) >= $_GET['amount']) {
                addDenars($_GET['secondguid'], $_GET['amount']);
                addDenars($_GET['guid'], -1 * $_GET['amount']);
                echo "19|$id|$secondguid|1";
            } else echo "19|$id|$secondguid|0";
        }
    }else {
        echo("-1");
    }
?>