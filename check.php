<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require 'application/config/database.php';
$db = new PDO("mysql:host=".$db["default"]["hostname"].";dbname=".$db["default"]["database"], $db["default"]["username"], $db["default"]["password"]);
$stmt = $db->query("DESCRIBE users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
