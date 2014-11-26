<?php

require_once dirname(__DIR__) . '/include/config.php';

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

$topic = $topicmap->newTopic();
$reifier = $topicmap->newTopic();
$association = $topicmap->newAssociation();

if ($options[ 'topics' ])
{
    foreach ($ids as $topic_id)
    {
        $topic->load($topic_id);
    
        if ($options[ 'with-associations' ])
        {
            foreach ($topicmap->getAssociations([ 'role_player' => $topic_id ]) as $association_id)
            {
                $association->load($association_id);
        
                $reifier_id = $association->getReifier();
        
                $ok = $association->delete();
            
                printf("Deleted association %s (%s)\n", $association_id, $ok);
            
                if ($options[ 'with-reifiers' ] && (strlen($reifier_id) > 0))
                {
                    $reifier->load($reifier_id);

                    $ok = $reifier->delete();
        
                    printf("Deleted reifier %s (%s)\n", $reifier_id, $ok);
                }
            }
        }
    
        $ok = $topic->delete();
    
        printf("Deleted topic %s (%s)\n", $topic_id, $ok);
    }
}
elseif ($options[ 'associations' ])
{
    foreach ($ids as $association_id)
    {
        $association->load($association_id);
        
        $reifier_id = $association->getReifier();
     
        $ok = $association->delete();
            
        printf("Deleted association %s (%s)\n", $association_id, $ok);
            
        if ($options[ 'with-reifiers' ] && (strlen($reifier_id) > 0))
        {
            $reifier->load($reifier_id);

            $ok = $reifier->delete();
        
            printf("Deleted reifier %s (%s)\n", $reifier_id, $ok);
        }
    }
}

