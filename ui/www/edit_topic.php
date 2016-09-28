<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifierId());

$request_path = substr($_SERVER[ 'REDIRECT_URL' ], strlen(TOPICBANK_BASE_URL));

list(, $topic_identifier_or_id) = explode('/', $request_path);

$topic_id = $topicmap->getTopicIdBySubject($topic_identifier_or_id);

if (strlen($topic_id) === 0)
    $topic_id = $topic_identifier_or_id;

$tpl[ 'cancel_url' ] = sprintf('%stopic/%s', TOPICBANK_BASE_URL, $topic_id);

$topic = $topicmap->newTopic();
$topic->load($topic_id);

// Update

$tpl[ 'error_html' ] = '';

if (($_SERVER[ 'REQUEST_METHOD' ] === 'POST') && isset($_REQUEST[ 'names' ]))
{
    // When creating a new topic
    
    if (! $topic->isLoaded())
        $topic->setId($topic_id);
    
    // Names
    
    foreach ($_REQUEST[ 'names' ] as $name_arr)
    {
        $name_arr[ 'value' ] = trim($name_arr[ 'value' ]);

        if ($name_arr[ 'delete' ] === '1')
        {
            $name_arr[ 'value' ] = '';
        }

        if (strlen($name_arr[ 'id' ]) === 0)
        {
            $name = $topic->newName();
        }
        else
        {
            $name = $topic->getFirstName([ 'id' => $name_arr[ 'id' ] ]);
        }
        
        $name->setTypeId($name_arr[ 'type' ]);
        $name->setValue($name_arr[ 'value' ]);
        $name->setReifierId($name_arr[ 'reifier' ]);
        
        $scopes = [ ];
        
        if (! isset($name_arr[ 'scope' ]))
            $name_arr[ 'scope' ] = [ ];
        
        foreach ($name_arr[ 'scope' ] as $scope)
        {
            $scope = trim($scope);
            
            if ($scope === '')
                continue;
            
            $scopes[ ] = $scope;
        }
        
        if (count($scopes) > 0)
            $name->setScopeIds($scopes);
    }
    
    // Types
    
    $type_ids = [ ];
    
    foreach ($_REQUEST[ 'types' ] as $type_id)
    {
        $type_id = trim($type_id);
        
        if ($type_id === '')
            continue;
            
        $type_ids[ ] = $type_id;
    }
    
    $topic->setTypeIds($type_ids);
    
    // Subject identifiers
    
    $urls = [ ];
    
    foreach ($_REQUEST[ 'subject_identifiers' ] as $url)
    {
        $url = trim($url);
        
        if ($url === '')
            continue;
            
        $urls[ ] = $url;
    }
    
    $topic->setSubjectIdentifiers($urls);
    
    // Subject locators
    
    $urls = [ ];
    
    foreach ($_REQUEST[ 'subject_locators' ] as $url)
    {
        $url = trim($url);
        
        if ($url === '')
            continue;
            
        $urls[ ] = $url;
    }
    
    $topic->setSubjectLocators($urls);

    // Occurrences
    
    foreach ($_REQUEST[ 'occurrences' ] as $occ_arr)
    {
        $occ_arr[ 'value' ] = trim($occ_arr[ 'value' ]);

        if ($occ_arr[ 'delete' ] === '1')
        {
            $occ_arr[ 'value' ] = '';
        }
        
        if (strlen($occ_arr[ 'id' ]) === 0)
        {
            $occurrence = $topic->newOccurrence();
        }
        else
        {
            $occurrence = $topic->getFirstOccurrence([ 'id' => $occ_arr[ 'id' ] ]);
        }
        
        $occurrence->setTypeId($occ_arr[ 'type' ]);
        $occurrence->setValue($occ_arr[ 'value' ]);
        $occurrence->setDatatypeId($occ_arr[ 'datatype' ]);
        $occurrence->setReifierId($occ_arr[ 'reifier' ]);
        
        $scopes = [ ];
        
        foreach ($occ_arr[ 'scope' ] as $scope)
        {
            $scope = trim($scope);
            
            if ($scope === '')
                continue;
            
            $scopes[ ] = $scope;
        }
        
        if (count($scopes) > 0)
            $occurrence->setScopeIds($scopes);
    }

    // Validate
    
    $ok = $topic->validate($msg_html);
    
    // Save
    
    if ($ok >= 0)
        $ok = $topic->save();

    // Associations

    if ($ok >= 0)
    {
        foreach ($_REQUEST[ 'associations' ] as $assoc_arr)
        {
            $assoc_arr[ 'type' ] = trim($assoc_arr[ 'type' ]);
        
            if ($assoc_arr[ 'type' ] === '')
                continue;
            
            $association = $topicmap->newAssociation();
        
            if (strlen($assoc_arr[ 'id' ]) > 0)
            {
                $association->load($assoc_arr[ 'id' ]);

                if ($association->isLoaded())
                {
                    if ($assoc_arr[ 'delete' ] === '1')
                    {
                        $ok = $association->delete();
        
                        if ($ok < 0)
                            break;

                        continue;
                    }
                }
                else
                {
                    $association->setId($assoc_arr[ 'id' ]);
                }
            }
            else
            {
                $association->setId($topicmap->createId());
            }
    
            $association->setTypeId($assoc_arr[ 'type' ]);
            $association->setReifierId($assoc_arr[ 'reifier' ]);

            $scopes = [ ];
        
            foreach ($assoc_arr[ 'scope' ] as $scope)
            {
                $scope = trim($scope);
            
                if ($scope === '')
                    continue;
            
                $scopes[ ] = $scope;
            }
        
            $association->setScopeIds($scopes);
    
            foreach ($assoc_arr[ 'roles' ] as $role_arr)
            {
                $role_arr[ 'type' ] = trim($role_arr[ 'type' ]);
                $role_arr[ 'player' ] = trim($role_arr[ 'player' ]);

                if (($role_arr[ 'type' ] === '') || ($role_arr[ 'player' ] === ''))
                    continue;
                
                if ($role_arr[ 'player' ] === '{this_topic}')
                    $role_arr[ 'player' ] = $topic_id;

                if ($role_arr[ 'delete' ] === '1')
                {
                    $role_arr[ 'player' ] = '';
                }

                if (strlen($role_arr[ 'id' ]) === 0)
                {
                    $role = $association->newRole();
                }
                else
                {
                    $role = $association->getFirstRole([ 'id' => $role_arr[ 'id' ] ]);
                }
        
                $role->setTypeId($role_arr[ 'type' ]);
                $role->setPlayerId($role_arr[ 'player' ]);
                $role->setReifierId($role_arr[ 'reifier' ]);
            }
        
            $ok = $association->validate($msg_html);
            
            if ($ok >= 0)
                $ok = $association->save();
        
            if ($ok < 0)
                break;
        }
    }

    if ($ok < 0)
    {
        $tpl[ 'error_html' ] = htmlspecialchars(sprintf('Could not save topic. Error code: %s', $ok));
    }
    else
    {
        if (! empty($_REQUEST[ 'close_after_save' ]))
        {
            $topic_url = sprintf
            (
                '%stopic/%s',
                TOPICBANK_BASE_URL,
                $topic->getId()
            );

            header('Location: ' . $topic_url);
            exit;
        }
        
        // Load freshly, to make sure everything we're seeing is in the database
        
        $topic->load($topic_id);
    }
}

$tpl[ 'topic_names' ] = [ ];

$tpl[ 'topic' ] = $topic->getAll();

foreach ($tpl[ 'topic' ][ 'types' ] as $helper_topic_id)
    $tpl[ 'topic_names' ][ $helper_topic_id ] = false;

$tpl[ 'id_text' ] = $topicmap->getTopicIdBySubject('http://schema.org/text');
$tpl[ 'id_xhtml' ] = $topicmap->getTopicIdBySubject('http://www.w3.org/1999/xhtml');

foreach ($tpl[ 'topic' ][ 'occurrences' ] as $i => $occurrence_arr)
{
    $tpl[ 'topic_names' ][ $occurrence_arr[ 'type' ] ] = false;
    $tpl[ 'topic_names' ][ $occurrence_arr[ 'datatype' ] ] = false;
    
    foreach ($occurrence_arr[ 'scope' ] as $scope)
        $tpl[ 'topic_names' ][ $scope ] = false;        
}

// Names

$tpl[ 'topic' ][ 'label' ] = $topic->getLabel();

if (count($tpl[ 'topic' ][ 'names' ]) === 0)
{
    $dummy_name = $topic->newName();
    
    $dummy_name->setType('http://schema.org/name');
    
    $tpl[ 'topic' ][ 'names' ][ ] = $dummy_name->getAll();
}

foreach ($tpl[ 'topic' ][ 'names' ] as $i => $name)
{
    $tpl[ 'topic_names' ][ $name[ 'type' ] ] = false;
    
    foreach ($name[ 'scope' ] as $scope)
        $tpl[ 'topic_names' ][ $scope ] = false;
}

$tpl[ 'topic' ][ 'reifies_summary_html' ] = \TopicBank\Ui\Utils::getReifiesSummary($topic);

// Fill associations

$association_ids = $topicmap->getAssociationIds([ 'role_player_id' => $topic_id ]);

$tpl[ 'associations' ] = [ ];

foreach ($association_ids as $association_id)
{
    $association = $topicmap->newAssociation();
    $association->load($association_id);

    $association_arr = $association->getAll();
    
    $tpl[ 'topic_names' ][ $association_arr[ 'type' ] ] = false;
    
    foreach ($association_arr[ 'scope' ] as $scope)
        $tpl[ 'topic_names' ][ $scope ] = false;

    foreach ($association_arr[ 'roles' ] as $key => $role_arr)
    {
        $tpl[ 'topic_names' ][ $role_arr[ 'type' ] ] = false;
        $tpl[ 'topic_names' ][ $role_arr[ 'player' ] ] = false;
        
        $association_arr[ 'roles' ][ $key ][ 'this_topic' ] = ($role_arr[ 'player' ] === $topic_id);
    }

    usort($association_arr[ 'roles' ], function($a, $b)
    {
        $a = $a[ 'this_topic' ];
        $b = $b[ 'this_topic' ];
        
        return ($a === true ? 0 : 1);
    });
    
    $tpl[ 'associations' ][ ] = $association_arr;    
}

// Fill topic_names array (names of all related topics needed for display)

foreach (array_keys($tpl[ 'topic_names' ]) as $helper_topic_id)
{
    $label = $topicmap->getTopicLabel($helper_topic_id);
    
    if (strlen($label) === 0)
        $label = $helper_topic_id;
        
    $tpl[ 'topic_names' ][ $helper_topic_id ] = $label;
}

include TOPICBANK_BASE_DIR . '/ui/templates/edit_topic.tpl.php';
