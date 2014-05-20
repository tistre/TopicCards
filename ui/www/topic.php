<?php

define('XDDB_BASE_DIR', dirname(dirname(__DIR__)));
define('XDDB_BASE_URL', '/xddb/');
define('XDDB_STATIC_BASE_URL', '/xddb_static/');

require_once XDDB_BASE_DIR . '/include/init.php';
require_once XDDB_BASE_DIR . '/include/config.php';


function getDisplayName($topic)
{
    $result = $topic->getId();

    foreach ($topic->getNames([ ]) as $name)
    {
        if ($name->getType() !== 'basename')
            continue;
    
        if (count($name->getScope()) > 0)
            continue;
    
        $result = $name->getValue();
        break;
    }
    
    return $result;
}


function splitTopicNames(array &$topic_data)
{
    $topic_data[ 'display_name' ] = false;
    $topic_data[ 'additional_names' ] = [ ];
    
    foreach ($topic_data[ 'names' ] as $name)
    {
        if ($topic_data[ 'display_name' ] === false)
        {
            if (($name[ 'type' ] === 'basename') && (count($name[ 'scope'  ]) === 0))
            {
                $topic_data[ 'display_name' ] = $name;
                continue;
            }
        }
        
        $topic_data[ 'additional_names' ][ ] = $name;
    }
    
    return 0;
}


$services = new \Xddb\Backends\Db\Services();
$services->setDbParams($db_params);

$system = new \Xddb\Backends\Db\TopicMapSystem($services);

$services->topicmap = $system->newTopicMap();
$services->topicmap->setUrl('xddb');

$tpl = [ ];

$tpl[ 'xddb_base_url' ] = XDDB_BASE_URL;
$tpl[ 'xddb_static_base_url' ] = XDDB_STATIC_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'display_name' ] = 'My first topic map';

$request_path = substr($_SERVER[ 'REDIRECT_URL' ], strlen(XDDB_BASE_URL));

list(, $topic_id) = explode('/', $request_path);

$tpl[ 'edit_url' ] = sprintf('%sedit_topic/%s', XDDB_BASE_URL, $topic_id);

$topic = $services->topicmap->newTopic();
$topic->load($topic_id);

$tpl[ 'topic' ] = $topic->getAll();

splitTopicNames($tpl[ 'topic' ]);

// Collect related topic IDs

$tpl[ 'topic_names' ] = [ ];

foreach ($tpl[ 'topic' ][ 'types' ] as $type_topic_id)
    $tpl[ 'topic_names' ][ $type_topic_id ] = false;
    
foreach ($tpl[ 'topic' ][ 'names' ] as $name)
{
    $tpl[ 'topic_names' ][ $name[ 'type' ] ] = false;
    
    foreach ($name[ 'scope' ] as $name_scope_id)
        $tpl[ 'topic_names' ][ $name_scope_id ] = false;
}

foreach ($tpl[ 'topic' ][ 'occurrences' ] as $occurrence)
{
    $tpl[ 'topic_names' ][ $occurrence[ 'type' ] ] = false;
    
    foreach ($occurrence[ 'scope' ] as $occurrence_scope_id)
        $tpl[ 'topic_names' ][ $occurrence_scope_id ] = false;
}

// Fill occurrence_type_index

$tpl[ 'occurrence_type_index' ] = [ ];

foreach ($tpl[ 'topic' ][ 'occurrences' ] as $key => $occurrence)
{
    $occurrence_type = $occurrence[ 'type' ];
    
    if (! isset($tpl[ 'occurrence_type_index' ][ $occurrence_type ]))
        $tpl[ 'occurrence_type_index' ][ $occurrence_type ] = [ ];
        
    $tpl[ 'occurrence_type_index' ][ $occurrence_type ][ ] = $key;
    
    $tpl[ 'topic_names' ][ $occurrence_type ] = false;
}

// Fill associations and associations_type_index, group by type and role

$association_ids = $services->topicmap->getAssociations([ 'role_player' => $topic_id ]);

$tpl[ 'associations' ] = [ ];

foreach ($association_ids as $association_id)
{
    $association = $services->topicmap->newAssociation();
    $association->load($association_id);

    $tpl[ 'associations' ][ ] = $association->getAll();
}

// Fill association_type_index

$tpl[ 'association_type_index' ] = [ ];

foreach ($tpl[ 'associations' ] as $key => $association)
{
    $association_type = $association[ 'type' ];
    
    if (! isset($tpl[ 'association_type_index' ][ $association_type ]))
        $tpl[ 'association_type_index' ][ $association_type ] = [ ];
    
    $my_role_type = false;
    
    foreach ($association[ 'roles' ] as $role)
    {
        $tpl[ 'topic_names' ][ $role[ 'type' ] ] = false;
        $tpl[ 'topic_names' ][ $role[ 'player' ] ] = false;

        if ($role[ 'player' ] !== $topic_id)
            continue;
            
        $my_role_type = $role[ 'type' ];
    }
    
    if (! isset($tpl[ 'association_type_index' ][ $association_type ][ $my_role_type ]))
        $tpl[ 'association_type_index' ][ $association_type ][ $my_role_type ] = [ ];

    $tpl[ 'association_type_index' ][ $association_type ][ $my_role_type ][ ] = $key;
    
    $tpl[ 'topic_names' ][ $association_type ] = false;    
}

// Fill topic_names array (names of all related topics needed for display)

$helper_topic = $services->topicmap->newTopic();

foreach (array_keys($tpl[ 'topic_names' ]) as $helper_topic_id)
{
    $helper_topic->load($helper_topic_id);
    
    $helper_display_name = getDisplayName($helper_topic);
    
    if (strlen($helper_display_name) === 0)
        $helper_display_name = $helper_topic_id;
        
    $tpl[ 'topic_names' ][ $helper_topic_id ] = $helper_display_name;
}


include XDDB_BASE_DIR . '/ui/templates/topic.tpl.php';
