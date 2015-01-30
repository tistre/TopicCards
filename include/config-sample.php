<?php

if (PHP_SAPI === 'cli')
{   
    error_reporting(E_ALL);
    ini_set('error_log', false);
    ini_set('display_errors', 'stderr');
}

$db_params =
[
    'dsn' => 'mysql:host=localhost;dbname=topicbank_test;charset=utf8mb4', 
    'username' => 'user', 
    'password' => 'secret',
    'driver_options' => [ \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_ALL_TABLES'" ]
];

define('TOPICBANK_BASE_DIR', dirname(__DIR__));
define('TOPICBANK_BASE_URL', '/topicbank/');

require_once TOPICBANK_BASE_DIR . '/include/init.php';

$services = new \TopicBank\Backends\Db\Services();
$services->setDbParams($db_params);

$topicmap = $services->getTopicMapSystem()->newTopicMap('default');
$topicmap->setUrl('http://example.com/topicmaps/topicbank');
$topicmap->setDbTablePrefix('topicbank_');
$topicmap->setSearchIndex('topicbank');
$topicmap->setUploadPath('/var/opt/topicbank/' . date('Y-m-d'));

$services->setPreferredLabelScopes(
[
    // XXX use a constant for the subject
    [ $topicmap->getTopicIdBySubject('http://en.wikipedia.org/wiki/English_language') ],
    [ ], 
    '*'
]);

$config_topicmap = $services->getTopicMapSystem()->newTopicMap('config');
$config_topicmap->setUrl('http://example.com/topicmaps/topicbank_config');
$config_topicmap->setDbTablePrefix('topicbank_config_');

/*
    How to run your own code on TopicBank events:
    
function helloWorld(\TopicBank\Interfaces\iTopicMap $topicmap, $event, array $params)
{
    error_log($event);
    
    // A negative return code cancels saving, rolls back the database transaction
    return 0;
}

$topicmap->on(\TopicBank\Interfaces\iTopic::EVENT_SAVING, 'helloWorld');

*/
