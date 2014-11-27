<?php

require_once dirname(__DIR__) . '/include/config.php';


function addObject($id)
{
    global $options;
    
    if ($options[ 'topics' ])
    {
        addTopic($id);
    }
    elseif ($options[ 'associations' ])
    {
        addAssociation($id);
    }
}


function addTopic($topic_id)
{
    global $topicmap;
    global $options;
    global $objects;
    
    $topic = $topicmap->newTopic();
    $topic->load($topic_id);

    $objects[ ] = $topic;
    
    if ($options[ 'with-associations' ])
    {
        foreach ($topicmap->getAssociations([ 'role_player' => $topic_id ]) as $association_id)
            addAssociation($association_id);
    }    
}


function addAssociation($association_id)
{
    global $topicmap;
    global $options;
    global $objects;
    
    $association = $topicmap->newAssociation();
    $reifier = $topicmap->newTopic();
    
    $association->load($association_id);
        
    $reifier_id = $association->getReifier();
 
    if ($options[ 'with-reifiers' ] && (strlen($reifier_id) > 0))
    {
        $reifier->load($reifier_id);

        $objects[ ] = $reifier;
    }
    
    $objects[ ] = $association;
}


// XXX use proper CLI argument parser

$ids = [ ];

$options = 
[
    'topics' => false,
    'associations' => false,
    'with-associations' => false,
    'with-reifiers' => false
];

foreach ($argv as $i => $arg)
{
    if ($i === 0)
        continue;

    if (substr($arg, 0, 2) === '--')
    {
        $options[ substr($arg, 2) ] = true;
        continue;
    }

    $ids[ ] = $arg;
}

$objects = [ ];

if (in_array('-', $ids, true))
{
    while (! feof(STDIN))
    {
        $id = trim(fgets(STDIN));
        
        if ($id === '')
            continue;
            
        addObject($id);
    }
}
else
{
    foreach ($ids as $id)
    {
        addObject($id);
    }
}

$exporter = new \TopicBank\Utils\XtmExport();

echo $exporter->exportObjects($objects);

