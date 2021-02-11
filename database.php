<?php
    $serverName = "127.0.0.1";
    $username = "root";
    $password = "*LW2zDkKVwZA4nqwuFQG83AW*-oxNUw23QiBDk4c!Hz9PuHvZtDYhyVJd.M47@sq";
    $database = "napwars";
    /*
     * @params table name, parameter names (array), parameter values(array), whether or not sort it, 0 = no, 1 = ascending, 2 = descending
     */
    function getFromDatabase($table, $variableToGet, $key, $values, $sorted) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        if (sizeof($key) != sizeof($values)) return null;
        $query = "SELECT $variableToGet FROM $table WHERE ";
        //SELECT Gold FROM players WHERE Unique_Id = '$unique_id' AND CONDITION2 AND Condition3
        for ($i = 0; $i < sizeof($key); $i++) {
            if ($i > 0) $query = $query . " AND ";
            $query = $query . $key[$i] . "=" . "'" . $values[$i] . "'";
        }
        if ($sorted == 1) $query = $query . " ORDER BY $variableToGet";
        elseif ($sorted == 2) $query = $query . " ORDER BY $variableToGet DESC";
        $result = $conn->query($query);
        return $result;
    }
    function insertToDatabase($table, $key, $value) {
        global $serverName, $username, $password, $database;
        $conn = new mysqli($serverName, $username, $password, $database);
        if (sizeof($key) != sizeof($value)) return false;
        $keys = implode(",", $key);
        $values = implode("','", $value);
        $query = "INSERT INTO $table ($keys) VALUES ('$values')";
        return($conn->query($query));
    }
?>