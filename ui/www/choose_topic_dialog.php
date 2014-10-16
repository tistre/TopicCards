<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$what = $_REQUEST[ 'what' ];

$recent = [ ];

// Topic types

if ($what === 'topic_type')
{
    // Most recently used
    
    $recent = $topicmap->getTopicTypes([ 'get_mode' => 'recent' ]);
}

// Name types

elseif ($what === 'name_type')
{
    // Most recently used
    
    $recent = $topicmap->getNameTypes([ 'get_mode' => 'recent' ]);
}

// Name scopes

elseif ($what === 'name_scope')
{
    // Most recently used
    
    $recent = $topicmap->getNameScopes([ 'get_mode' => 'recent' ]);
}

// Occurrence types

elseif ($what === 'occurrence_type')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceTypes([ 'get_mode' => 'recent' ]);
}

// Occurrence datatypes

elseif ($what === 'occurrence_datatype')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceDatatypes([ 'get_mode' => 'recent' ]);
}

// Occurrence scopes

elseif ($what === 'occurrence_scope')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceScopes([ 'get_mode' => 'recent' ]);
}

// Association types

elseif ($what === 'association_type')
{
    // Most recently used
    
    $recent = $topicmap->getAssociationTypes([ 'get_mode' => 'recent' ]);
}

// Association scopes

elseif ($what === 'association_scope')
{
    // Most recently used
    
    $recent = $topicmap->getAssociationScopes([ 'get_mode' => 'recent' ]);
}

// Role types

elseif ($what === 'role_type')
{
    // Most recently used
    
    $recent = $topicmap->getRoleTypes([ 'get_mode' => 'recent' ]);
}

// Role players

elseif ($what === 'role_player')
{
    // Most recently used
    
    $recent = $topicmap->getRolePlayers([ 'get_mode' => 'recent' ]);
}

// Add labels

$tpl[ 'recent' ] = [ ];

foreach ($recent as $id)
{
    $topic = $topicmap->newTopic();
    $topic->load($id);
    
    $types = [ ];
    
    foreach ($topic->getTypes() as $type)
        $types[ ] = $topicmap->getTopicLabel($type);

    $tpl[ 'recent' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id),
        'type' => implode(', ', $types)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'recent' ], 'label');


$tpl[ 'topic_types' ] = [ ];

foreach ($topicmap->getTopicTypes([ 'get_mode' => 'recent' ]) as $id)
{
    $tpl[ 'topic_types' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'topic_types' ], 'label');


include TOPICBANK_BASE_DIR . '/ui/templates/choose_topic_dialog.tpl.php';
