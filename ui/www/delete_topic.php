<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

// XXX add checksum in session to avoid CSRF

$request_path = substr($_SERVER[ 'REDIRECT_URL' ], strlen(TOPICBANK_BASE_URL));

list(, $topic_id) = explode('/', $request_path);

$topic = $topicmap->newTopic();

$ok = $topic->load($topic_id);

if ($ok >= 0)
    $ok = $topic->delete();
    
if ($ok >=0)
{
    header(sprintf('Location: %stopics', TOPICBANK_BASE_URL));
}
else
{
    printf("Error deleting %s (%s)\n", htmlspecialchars($topic_id), htmlspecialchars($ok));
}

?>
