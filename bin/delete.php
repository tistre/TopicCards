<?php

require_once dirname(__DIR__) . '/include/config.php';


function deleteObject($id)
{
    global $options;
    
    if ($options[ 'topics' ])
    {
        deleteTopic($id);
    }
    elseif ($options[ 'associations' ])
    {
        deleteAssociation($id);
    }
}


function deleteTopic($topic_id)
{
    global $topicmap;
    global $options;
    
    $topic = $topicmap->newTopic();
    $topic->load($topic_id);
    
    if ($options[ 'with-associations' ])
    {
        foreach ($topicmap->getAssociations([ 'role_player' => $topic_id ]) as $association_id)
            deleteAssociation($association_id);
    }
    
    $ok = $topic->delete();

    printf("Deleted topic <%s> (%s)\n", $topic_id, $ok);
}


function deleteAssociation($association_id)
{
    global $topicmap;
    global $options;
    
    $association = $topicmap->newAssociation();
    $reifier = $topicmap->newTopic();
    
    $association->load($association_id);
        
    $reifier_id = $association->getReifier();
 
    $ok = $association->delete();
            
    printf("Deleted association <%s> (%s)\n", $association_id, $ok);
        
    if ($options[ 'with-reifiers' ] && (strlen($reifier_id) > 0))
    {
        $reifier->load($reifier_id);

        $ok = $reifier->delete();
    
        printf("Deleted reifier %s (%s)\n", $reifier_id, $ok);
    }
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

if (in_array('-', $ids, true))
{
    while (! feof(STDIN))
    {
        $id = trim(fgets(STDIN));
        
        if ($id === '')
            continue;
            
        deleteObject($id);
    }
}
else
{
    foreach ($ids as $id)
    {
        deleteObject($id);
    }
}
