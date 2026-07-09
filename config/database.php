<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'infinitia_website';

$conn = @mysqli_connect($db_host, $db_user, $db_password, $db_name);

if ($conn) {
    mysqli_set_charset($conn, "utf8mb4");
}
