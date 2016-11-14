<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

/** @var \TopicCards\Interfaces\TopicMapInterface $topicmap */


function getTopicVars($topic_id, &$result, &$topic_names)
{
    global $topicmap;
    
    $result = [ ];
    
    if (! is_array($topic_names))
        $topic_names = [ ];
    
    $topic = $topicmap->newTopic();
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
            getTopicVars($name[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'topic' ][ 'names' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    }

    $result[ 'topic' ][ 'label' ] = $topic->getLabel();

    foreach ($result[ 'topic' ][ 'occurrences' ] as $key => $occurrence)
    {
        $topic_names[ $occurrence[ 'type' ] ] = false;
    
        foreach ($occurrence[ 'scope' ] as $occurrence_scope_id)
            $topic_names[ $occurrence_scope_id ] = false;

        if (strlen($occurrence[ 'reifier' ]) > 0)
        {
            getTopicVars($occurrence[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'topic' ][ 'occurrences' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    }

    // Prepend local IDs with topic details URL

    foreach ($result[ 'topic' ][ 'subject_identifiers' ] as $i => $subject_identifier)
    {
        if (strpbrk($subject_identifier, '/:') !== false)
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

    $association_ids = $topicmap->getAssociationIds([ 'role_player_id' => $topic_id ]);

    $result[ 'associations' ] = [ ];

    foreach ($association_ids as $association_id)
    {
        $association = $topicmap->newAssociation();
        $association->load($association_id);

        $association_arr = $association->getAll();
        
        foreach ($association_arr[ 'roles' ] as $key => $role)
            $association_arr[ 'roles' ][ $key ][ 'type_label' ] = $topicmap->getTopicLabel($role[ 'type' ]);
        
        TopicCards\Utils\StringUtils::usortByKey($association_arr[ 'roles' ], 'type_label');

        $result[ 'associations' ][ ] = $association_arr;
    }

    // Fill association_type_index

    $result[ 'association_type_index' ] = [ ];

    foreach ($result[ 'associations' ] as $key => $association)
    {
        $association_type = $association[ 'type' ];

        if (strlen($association[ 'reifier' ]) > 0)
        {
            getTopicVars($association[ 'reifier' ], $reifier_vars, $topic_names);
            $result[ 'associations' ][ $key ][ 'reifier' ] = $reifier_vars;
        }
    
        if (! isset($result[ 'association_type_index' ][ $association_type ]))
            $result[ 'association_type_index' ][ $association_type ] = [ ];
    
        $my_role_type = false;
    
        foreach ($association[ 'roles' ] as $subkey => $role)
        {
            if (strlen($role[ 'reifier' ]) > 0)
            {
                getTopicVars($role[ 'reifier' ], $reifier_vars, $topic_names);
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

    $result[ 'topic' ][ 'reifies_summary_html' ] = \TopicBankUi\Utils::getReifiesSummary($topic);
    
    return $result;
}


$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifierId());

$request_path = substr($_SERVER[ 'REDIRECT_URL' ], strlen(TOPICBANK_BASE_URL));

list(, $topic_identifier_or_id) = explode('/', $request_path);

$topic_id = $topicmap->getTopicIdBySubject($topic_identifier_or_id);

if (strlen($topic_id) === 0)
    $topic_id = $topic_identifier_or_id;

$tpl[ 'edit_url' ] = sprintf('%sedit_topic/%s', TOPICBANK_BASE_URL, $topic_id);
$tpl[ 'delete_url' ] = sprintf('%sdelete_topic/%s', TOPICBANK_BASE_URL, $topic_id);

$tpl[ 'id_text' ] = $topicmap->getTopicIdBySubject('http://schema.org/text', true);
$tpl[ 'id_xhtml' ] = $topicmap->getTopicIdBySubject('http://www.w3.org/1999/xhtml', true);
$tpl[ 'id_anyuri' ] = $topicmap->getTopicIdBySubject('http://www.w3.org/2001/XMLSchema#anyURI', true);

getTopicVars($topic_id, $topic_vars, $tpl[ 'topic_names' ]);

$tpl = array_merge($tpl, $topic_vars);

// Fill topic_names array (names of all related topics needed for display)

foreach (array_keys($tpl[ 'topic_names' ]) as $helper_topic_id)
{
    $label = $topicmap->getTopicLabel($helper_topic_id);
    
    if (strlen($label) === 0)
        $label = $helper_topic_id;
        
    $tpl[ 'topic_names' ][ $helper_topic_id ] = $label;
}


include TOPICBANK_BASE_DIR . '/ui/templates/topic.tpl.php';

// Add to "recent" list
// XXX work in progress

/*
if ($services->getTopicMapSystem()->hasTopicMap('config'))
{
    $recent_entry = $services->getTopicMapSystem()->getTopicMap('config')->newTopic();

    $recent_entry->setId($recent_entry->getTopicMap()->createId());

    $recent_entry->save();
}
*/
