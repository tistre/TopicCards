<?php

require_once dirname(dirname(__DIR__)) . '/include/init.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$what = $_REQUEST[ 'what' ];

$recent = [ ];
$params = [ 'get_mode' => 'recent', 'limit' => 5 ];

// Topic types

if ($what === 'topic_type')
{
    // Most recently used
    
    $recent = $topicmap->getTopicTypeIds($params);
}

// Name types

elseif ($what === 'name_type')
{
    // Most recently used
    
    $recent = $topicmap->getNameTypeIds($params);
}

// Name scopes

elseif ($what === 'name_scope')
{
    // Most recently used
    
    $recent = $topicmap->getNameScopeIds($params);
}

// Occurrence types

elseif ($what === 'occurrence_type')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceTypeIds($params);
}

// Occurrence datatypes

elseif ($what === 'occurrence_datatype')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceDatatypeIds($params);
}

// Occurrence scopes

elseif ($what === 'occurrence_scope')
{
    // Most recently used
    
    $recent = $topicmap->getOccurrenceScopeIds($params);
}

// Association types

elseif ($what === 'association_type')
{
    // Most recently used
    
    $recent = $topicmap->getAssociationTypeIds($params);
}

// Association scopes

elseif ($what === 'association_scope')
{
    // Most recently used
    
    $recent = $topicmap->getAssociationScopeIds($params);
}

// Role types

elseif ($what === 'role_type')
{
    // Most recently used
    
    $recent = $topicmap->getRoleTypeIds($params);
}

// Role players

elseif ($what === 'role_player')
{
    // Most recently used
    
    $recent = $topicmap->getRolePlayerIds($params);
}

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

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'recent' ], 'label');


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

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'topic_types' ], 'label');


include TOPICBANK_BASE_DIR . '/ui/templates/choose_topic_dialog.tpl.php';
