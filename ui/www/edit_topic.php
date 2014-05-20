<?php

define('XDDB_BASE_DIR', dirname(dirname(__DIR__)));
define('XDDB_BASE_URL', '/xddb/');
define('XDDB_STATIC_BASE_URL', '/xddb_static/');

require_once XDDB_BASE_DIR . '/include/init.php';
require_once XDDB_BASE_DIR . '/include/config.php';

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

$topic = $services->topicmap->newTopic();
$topic->load($topic_id);

// Update

$tpl[ 'error_html' ] = '';

if (($_SERVER[ 'REQUEST_METHOD' ] === 'POST') && isset($_REQUEST[ 'unscoped_basenames' ]))
{
    // When creating a new topic
    
    if (! $topic->isLoaded())
        $topic->setId($topic_id);
    
    // Names: Unscoped base name
    
    $topic->setNames([ ]);
    
    foreach ($_REQUEST[ 'unscoped_basenames' ] as $name_value)
    {
        $name_value = trim($name_value);
        
        if ($name_value === '')
            continue;
            
        $name = $topic->newName();
        $name->setType('basename');
        $name->setValue($name_value);
    }

    // Other names
    
    foreach ($_REQUEST[ 'other_names' ] as $name_arr)
    {
        $name_arr[ 'value' ] = trim($name_arr[ 'value' ]);
        
        if ($name_arr[ 'value' ] === '')
            continue;
            
        $name = $topic->newName();
        
        $name->setType($name_arr[ 'type' ]);
        $name->setValue($name_arr[ 'value' ]);
        
        $scopes = [ ];
        
        foreach ($name_arr[ 'scope' ] as $scope)
        {
            $scope = trim($scope);
            
            if ($scope === '')
                continue;
            
            $scopes[ ] = $scope;
        }
        
        if (count($scopes) > 0)
            $name->setScope($scopes);
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
    
    $topic->setTypes($type_ids);
    
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
    
    $topic->setOccurrences([ ]);
    
    foreach ($_REQUEST[ 'occurrences' ] as $occ_arr)
    {
        $occ_arr[ 'value' ] = trim($occ_arr[ 'value' ]);
        
        if ($occ_arr[ 'value' ] === '')
            continue;
            
        $occurrence = $topic->newOccurrence();
        
        $occurrence->setType($occ_arr[ 'type' ]);
        $occurrence->setValue($occ_arr[ 'value' ]);
        $occurrence->setDatatype($occ_arr[ 'datatype' ]);
        
        $scopes = [ ];
        
        foreach ($occ_arr[ 'scope' ] as $scope)
        {
            $scope = trim($scope);
            
            if ($scope === '')
                continue;
            
            $scopes[ ] = $scope;
        }
        
        if (count($scopes) > 0)
            $occurrence->setScope($scopes);
    }

    
    // Save
    
    $ok = $topic->save();
    
    if ($ok >= 0)
    {
        header(sprintf('Location: %stopic/%s', XDDB_BASE_URL, $topic_id));
        exit;
    }
    
    $tpl[ 'error_html' ] = htmlspecialchars(sprintf('Could not save topic. Error code: %s', $ok));
}

$tpl[ 'topic' ] = $topic->getAll();

// Fill "unscoped_basenames"

$tpl[ 'topic' ][ 'unscoped_basenames' ] = [ ];
$tpl[ 'topic' ][ 'other_names' ] = [ ];

foreach ($tpl[ 'topic' ][ 'names' ] as $name)
{
    $key = 'other_names';
    
    if (($name[ 'type' ] === 'basename') && (count($name[ 'scope' ]) === 0))
        $key = 'unscoped_basenames';

    $tpl[ 'topic' ][ $key ][ ] = $name;
}

include XDDB_BASE_DIR . '/ui/templates/edit_topic.tpl.php';
