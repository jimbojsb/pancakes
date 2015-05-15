<?php
require_once __DIR__ . '/vendor/autoload.php';
$productionConnection = new PDO("mysql:dbname=orca;host=mysql-master.orca.ocean", "readonly", "couponscouponscouponscoupons");
$localConnection = new PDO("mysql:host=localhost;dbname=test");
$logger = new Monolog\Logger("PANCAKES");

$dataCopy = new \Pancakes\DataTransfer\CopyTransfer();
$dataCopy->setDestinationConnection($localConnection);
$dataCopy->setSourceConnection($productionConnection);
$dataCopy->setLogger($logger);

$dataCopy->addTable('offers');
$dataCopy->addTable('email_subscriptions', ['limit' => 5000]);

$dataCopy->execute();