<?php

error_reporting(E_ALL);
ini_set('error_log', false);
ini_set('display_errors', 'stderr');

require_once dirname(__DIR__) . '/include/config.php';

$services->db_utils->connect();

$topic = $topicmap->newTopic();

foreach ($topicmap->getTopics([ ]) as $topic_id)
{
    $ok = $topic->load($topic_id);
    
    if ($ok >= 0)
        $ok = $topic->index();
    
    printf("%s (%s)\n", $topic->getId(), $ok);
}
