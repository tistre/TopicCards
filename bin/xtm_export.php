<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/config.php';


function addObject($id)
{
    global $getopt;
    
    if ($getopt[ 'topics' ])
    {
        addTopic($id);
    }
    elseif ($getopt[ 'associations' ])
    {
        addAssociation($id);
    }
}


function addTopic($topic_id)
{
    global $topicmap;
    global $getopt;
    global $objects;
    
    $topic = $topicmap->newTopic();
    $topic->load($topic_id);

    $objects[ ] = $topic;
    
    if ($getopt[ 'with_associations' ])
    {
        foreach ($topicmap->getAssociationIds([ 'role_player_id' => $topic_id ]) as $association_id)
            addAssociation($association_id);
    }    
}


function addAssociation($association_id)
{
    global $topicmap;
    global $getopt;
    global $objects;
    
    $association = $topicmap->newAssociation();
    $reifier = $topicmap->newTopic();
    
    $association->load($association_id);
        
    $reifier_id = $association->getReifierId();
 
    if ($getopt[ 'with_reifiers' ] && (strlen($reifier_id) > 0))
    {
        $reifier->load($reifier_id);

        $objects[ ] = $reifier;
    }
    
    $objects[ ] = $association;
}


$getopt = new Getopt(
[
    new Option(null, 'topics'),
    new Option(null, 'associations'),
    new Option(null, 'with_associations'),
    new Option(null, 'with_reifiers'),
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nTopicBank XTM export\n\n");
    
    echo $getopt->getHelpText();
    exit;
}

$objects = [ ];

if ($getopt->getOperand(0) === '-')
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
    foreach ($getopt->getOperands() as $id)
    {
        addObject($id);
    }
}

$exporter = new \TopicBank\Utils\XtmExport();

echo $exporter->exportObjects($objects);

