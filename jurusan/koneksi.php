<?php
$host = "localhost";
$connInfo = array("Database"=>"bebastanggungangg", "UID"=>"", "PWD"=>"");
$conn = sqlsrv_connect($host, $connInfo);

if( !$conn ) {
    die( print_r( sqlsrv_errors(), true));
}
?>