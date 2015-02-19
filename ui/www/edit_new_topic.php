<?php

require_once dirname(dirname(__DIR__)) . '/include/init.php';

$edit_topic_url = sprintf
(
    '%sedit_topic/%s',
    TOPICBANK_BASE_URL,
    $topicmap->createId()
);

header('Location: ' . $edit_topic_url);
