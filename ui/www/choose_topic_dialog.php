<?php

define('TOPICBANK_BASE_DIR', dirname(dirname(__DIR__)));
define('TOPICBANK_BASE_URL', '/topicbank/');
define('TOPICBANK_STATIC_BASE_URL', '/topicbank_static/');

require_once TOPICBANK_BASE_DIR . '/include/init.php';
require_once TOPICBANK_BASE_DIR . '/include/config.php';

$services = new \TopicBank\Backends\Db\Services();
$services->setDbParams($db_params);

$system = new \TopicBank\Backends\Db\TopicMapSystem($services);

$services->topicmap = $system->newTopicMap();
$services->topicmap->setUrl('xddb');

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$what = $_REQUEST[ 'what' ];

// Topic types

if ($what === 'topic_type')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getTopicTypes([ 'get_mode' => 'recent' ]);
}

// Name types

elseif ($what === 'name_type')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getNameTypes([ 'get_mode' => 'recent' ]);
}

// Occurrence types

elseif ($what === 'occurrence_type')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getOccurrenceTypes([ 'get_mode' => 'recent' ]);
}

// Occurrence datatypes

elseif ($what === 'occurrence_datatype')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getOccurrenceDatatypes([ 'get_mode' => 'recent' ]);
}

// Association types

elseif ($what === 'association_type')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getAssociationTypes([ 'get_mode' => 'recent' ]);
}

// Role types

elseif ($what === 'role_type')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getRoleTypes([ 'get_mode' => 'recent' ]);
}

// Role players

elseif ($what === 'role_player')
{
    // Most recently used
    
    $tpl[ 'recent' ] = $services->topicmap->getRolePlayers([ 'get_mode' => 'recent' ]);
}

$tpl[ 'topic_types' ] = $services->topicmap->getTopicTypes([ 'get_mode' => 'recent' ]);

include TOPICBANK_BASE_DIR . '/ui/templates/choose_topic_dialog.tpl.php';
