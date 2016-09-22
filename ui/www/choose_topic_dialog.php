<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$what = $_REQUEST[ 'what' ];

if (! isset($_SESSION[ 'choose_topic_history' ]))
    $_SESSION[ 'choose_topic_history' ] = [ ];

if (! isset($_SESSION[ 'choose_topic_history' ][ $what ]))
    $_SESSION[ 'choose_topic_history' ][ $what ] = [ ];
    
$recent = $_SESSION[ 'choose_topic_history' ][ $what ];

// Add labels

$tpl[ 'recent' ] = [ ];

foreach ($recent as $id)
{
    $topic = $topicmap->newTopic();
    $topic->load($id);
    
    $types = [ ];
    
    foreach ($topic->getTypeIds() as $type)
        $types[ ] = $topicmap->getTopicLabel($type);

    $tpl[ 'recent' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id),
        'type' => implode(', ', $types)
    ];
}

TopicCards\Utils\StringUtils::usortByKey($tpl[ 'recent' ], 'label');


$tpl[ 'topic_types' ] = [ ];

// XXX slow
foreach ($topicmap->getTopicTypeIds([ 'get_mode' => 'all' ]) as $id)
{
    $tpl[ 'topic_types' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id)
    ];
}

TopicCards\Utils\StringUtils::usortByKey($tpl[ 'topic_types' ], 'label');


include TOPICBANK_BASE_DIR . '/ui/templates/choose_topic_dialog.tpl.php';
