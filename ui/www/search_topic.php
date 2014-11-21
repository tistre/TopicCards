<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$name_like = '%' . $_REQUEST[ 'name' ] . '%';
$type = $_REQUEST[ 'type' ];

$results = $topicmap->getTopics([ 'name_like' => $name_like, 'type' => $type ]);

$tpl[ 'results' ] = [ ];

foreach ($results as $id)
{
    $tpl[ 'results' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'results' ], 'label');


include TOPICBANK_BASE_DIR . '/ui/templates/search_topic.tpl.php';
