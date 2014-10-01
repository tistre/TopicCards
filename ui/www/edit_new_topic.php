<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$edit_topic_url = sprintf
(
    '%sedit_topic/%s',
    TOPICBANK_BASE_URL,
    $services->topicmap->createId()
);

header('Location: ' . $edit_topic_url);
