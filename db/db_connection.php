<?php
$config = include(__DIR__ . '/config.php');

$conn = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['db']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>