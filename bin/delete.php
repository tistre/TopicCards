<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/init.php';


function deleteObject($id)
{
    global $getopt;
    
    if ($getopt[ 'topics' ])
    {
        deleteTopic($id);
    }
    elseif ($getopt[ 'associations' ])
    {
        deleteAssociation($id);
    }
}


function deleteTopic($topic_id)
{
    global $topicmap;
    global $getopt;
    
    $topic = $topicmap->newTopic();
    $topic->load($topic_id);
    
    if ($getopt[ 'with_associations' ])
    {
        foreach ($topicmap->getAssociations([ 'role_player_id' => $topic_id ]) as $association_id)
            deleteAssociation($association_id);
    }
    
    $ok = $topic->delete();

    printf("Deleted topic <%s> (%s)\n", $topic_id, $ok);
}


function deleteAssociation($association_id)
{
    global $topicmap;
    global $getopt;
    
    $association = $topicmap->newAssociation();
    $reifier = $topicmap->newTopic();
    
    $association->load($association_id);
        
    $reifier_id = $association->getReifierId();
 
    $ok = $association->delete();
            
    printf("Deleted association <%s> (%s)\n", $association_id, $ok);
        
    if ($getopt[ 'with_reifiers' ] && (strlen($reifier_id) > 0))
    {
        $reifier->load($reifier_id);

        $ok = $reifier->delete();
    
        printf("Deleted reifier %s (%s)\n", $reifier_id, $ok);
    }
}


$getopt = new Getopt(
[
    new Option(null, 'topics'),
    new Option(null, 'associations'),
    new Option(null, 'with_associations'),
    new Option(null, 'with_reifiers'),
    new Option(null, 'config', Getopt::REQUIRED_ARGUMENT),
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nTopicBank topic/association deletion\n\n");
    
    echo $getopt->getHelpText();
    exit;
}

if ($getopt->getOperand(0) === '-')
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
    foreach ($getopt->getOperands() as $id)
    {
        deleteObject($id);
    }
}
