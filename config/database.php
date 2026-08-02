<?php
$db_host = 'localhost';
$db_user = 'infiniti_francky_sabiti';
$db_password = '0994699173Francky';
$db_name = 'infiniti_infinitia_website';

$conn = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

if ($conn) {
    mysqli_set_charset($conn, "utf8mb4");
}
