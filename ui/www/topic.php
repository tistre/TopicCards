<?php

use TopicBank\Interfaces\iTopic;

define('TOPICBANK_BASE_DIR', dirname(dirname(__DIR__)));
define('TOPICBANK_BASE_URL', '/topicbank/');
define('TOPICBANK_STATIC_BASE_URL', '/topicbank_static/');

require_once TOPICBANK_BASE_DIR . '/include/init.php';
require_once TOPICBANK_BASE_DIR . '/include/config.php';


function getTopicVars($services, $topic_id, &$result, &$topic_names)
{
    $result = [ ];
    
    if (! is_array($topic_names))
        $topic_names = [ ];
    
    $topic = $services->topicmap->newTopic();
    $topic->load($topic_id);
    
    $result[ 'topic' ] = $topic->getAll();

    // Collect related topic IDs

    foreach ($result[ 'topic' ][ 'types' ] as $type_topic_id)
        $topic_names[ $type_topic_id ] = false;
    
    foreach ($result[ 'topic' ][ 'names' ] as $key => $name)
    {
        $topic_names[ $name[ 'type' ] ] = false;
    
        foreach ($name[ 'scope' ] as $name_scope_id)
            $topic_names[ $name_scope_id ] = false;
            
        if (strlen($name[ 'reifier' ]) > 0)
        {
            getTopicVars($services, $name[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'topic' ][ 'names' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    }

    splitTopicNames($result[ 'topic' ]);

    foreach ($result[ 'topic' ][ 'occurrences' ] as $key => $occurrence)
    {
        $topic_names[ $occurrence[ 'type' ] ] = false;
    
        foreach ($occurrence[ 'scope' ] as $occurrence_scope_id)
            $topic_names[ $occurrence_scope_id ] = false;

        if (strlen($occurrence[ 'reifier' ]) > 0)
        {
            getTopicVars($services, $occurrence[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'topic' ][ 'occurrences' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    }

    // Prepend local IDs with topic details URL

    foreach ($result[ 'topic' ][ 'subject_identifiers' ] as $i => $subject_identifier)
    {
        if (strpos($subject_identifier, '/') !== false)
            continue;
        
        $result[ 'topic' ][ 'subject_identifiers' ][ $i ] = sprintf
        (
            '%stopic/%s', 
            TOPICBANK_BASE_URL, 
            $subject_identifier
        );
    }
    
    // Fill occurrence_type_index

    $result[ 'occurrence_type_index' ] = [ ];

    foreach ($result[ 'topic' ][ 'occurrences' ] as $key => $occurrence)
    {
        $occurrence_type = $occurrence[ 'type' ];
    
        if (! isset($result[ 'occurrence_type_index' ][ $occurrence_type ]))
            $result[ 'occurrence_type_index' ][ $occurrence_type ] = [ ];
        
        $result[ 'occurrence_type_index' ][ $occurrence_type ][ ] = $key;
    
        $topic_names[ $occurrence_type ] = false;
    }

    // Fill associations and associations_type_index, group by type and role

    $association_ids = $services->topicmap->getAssociations([ 'role_player' => $topic_id ]);

    $result[ 'associations' ] = [ ];

    foreach ($association_ids as $association_id)
    {
        $association = $services->topicmap->newAssociation();
        $association->load($association_id);

        $result[ 'associations' ][ ] = $association->getAll();
    }

    // Fill association_type_index

    $result[ 'association_type_index' ] = [ ];

    foreach ($result[ 'associations' ] as $key => $association)
    {
        $association_type = $association[ 'type' ];

        if (strlen($association[ 'reifier' ]) > 0)
        {
            getTopicVars($services, $association[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'associations' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    
        if (! isset($result[ 'association_type_index' ][ $association_type ]))
            $result[ 'association_type_index' ][ $association_type ] = [ ];
    
        $my_role_type = false;
    
        foreach ($association[ 'roles' ] as $subkey => $role)
        {
            if (strlen($role[ 'reifier' ]) > 0)
            {
                getTopicVars($services, $role[ 'reifier' ], $reifier_vars, $topic_names);
                $result[ 'associations' ][ $key ][ 'roles' ][ $subkey ][ 'reifier' ] = $reifier_vars;
            }

            $topic_names[ $role[ 'type' ] ] = false;
            $topic_names[ $role[ 'player' ] ] = false;

            if ($role[ 'player' ] !== $topic_id)
                continue;
            
            $my_role_type = $role[ 'type' ];
        }
    
        if (! isset($result[ 'association_type_index' ][ $association_type ][ $my_role_type ]))
            $result[ 'association_type_index' ][ $association_type ][ $my_role_type ] = [ ];

        $result[ 'association_type_index' ][ $association_type ][ $my_role_type ][ ] = $key;
    
        $topic_names[ $association_type ] = false;    
    
        foreach ($association[ 'scope' ] as $scope)
            $topic_names[ $scope ] = false;    
    }
    
    // Topic is a reifier?

    addReifiesSummary($topic, $result[ 'topic' ]);
    
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


function addReifiesSummary(iTopic $topic, array &$topic_data)
{
    global $services;
    
    $topic_data[ 'reifies_summary_html' ] = '';

    $objects = $topic->getReifiedObject();
    
    if ($objects === false)
        return;
    
    if ($topic_data[ 'isreifier' ] === iTopic::REIFIES_NAME)
    {
        $topic_data[ 'reifies_summary_html' ] = sprintf
        (
            'Name “%s” of <a href="%stopic/%s">%s</a>',
            htmlspecialchars($objects[ 'name' ]->getValue()),
            TOPICBANK_BASE_URL,
            htmlspecialchars(urlencode($objects[ 'topic' ]->getId())),
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'topic' ]->getId()))
        );
    }
    elseif ($topic_data[ 'isreifier' ] === iTopic::REIFIES_OCCURRENCE)
    {
        $topic_data[ 'reifies_summary_html' ] = sprintf
        (
            'Property “%s: %s” of <a href="%stopic/%s">%s</a>',
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'occurrence' ]->getType())),
            htmlspecialchars($objects[ 'occurrence' ]->getValue()),
            TOPICBANK_BASE_URL,
            htmlspecialchars(urlencode($objects[ 'topic' ]->getId())),
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'topic' ]->getId()))
        );
    }
    elseif ($topic_data[ 'isreifier' ] === iTopic::REIFIES_ASSOCIATION)
    {
        $topic_data[ 'reifies_summary_html' ] = sprintf
        (
            '<a href="#">A “%s” association</a>',
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'association' ]->getType()))
        );
    }
    elseif ($topic_data[ 'isreifier' ] === iTopic::REIFIES_ROLE)
    {
        $topic_data[ 'reifies_summary_html' ] = sprintf
        (
            'Role “%s: %s” in <a href="#">a “%s” association</a>',
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'role' ]->getType())),
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'role' ]->getPlayer())),
            htmlspecialchars($services->topicmap->getTopicLabel($objects[ 'association' ]->getType()))
        );
    }
}

$services = new \TopicBank\Backends\Db\Services();
$services->setDbParams($db_params);

$system = new \TopicBank\Backends\Db\TopicMapSystem($services);

$services->topicmap = $system->newTopicMap();
$services->topicmap->setUrl('xddb');

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'display_name' ] = 'My first topic map';

$request_path = substr($_SERVER[ 'REDIRECT_URL' ], strlen(TOPICBANK_BASE_URL));

list(, $topic_identifier_or_id) = explode('/', $request_path);

$topic_id = $services->topicmap->getTopicBySubjectIdentifier($topic_identifier_or_id);

if (strlen($topic_id) === 0)
    $topic_id = $topic_identifier_or_id;

$tpl[ 'edit_url' ] = sprintf('%sedit_topic/%s', TOPICBANK_BASE_URL, $topic_id);

getTopicVars($services, $topic_id, $topic_vars, $tpl[ 'topic_names' ]);

$tpl = array_merge($tpl, $topic_vars);

// Fill topic_names array (names of all related topics needed for display)

foreach (array_keys($tpl[ 'topic_names' ]) as $helper_topic_id)
    $tpl[ 'topic_names' ][ $helper_topic_id ] = $services->topicmap->getTopicLabel($helper_topic_id);


include TOPICBANK_BASE_DIR . '/ui/templates/topic.tpl.php';
