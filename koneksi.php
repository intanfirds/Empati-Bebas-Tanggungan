<?php
$host = "localhost";
$connInfo = array("Database"=>"bebastanggungan", "UID"=>"", "PWD"=>"");
$conn = sqlsrv_connect($host, $connInfo);

if ($conn) {
    echo "Connected successfully";
} else {
    echo "Connection failed";
    die(print_r(sqlsrv_errors(), true));
}

?>