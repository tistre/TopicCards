<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

/** @var \TopicCards\Interfaces\TopicMapInterface $topicmap */

if ($_SERVER[ 'REQUEST_METHOD' ] !== 'POST')
    die('Please POST');

if (in_array($_REQUEST[ 'reifies_type' ], [ 'name', 'occurrence' ]))
{
    $topic = $topicmap->newTopic();
    $topic->load($_REQUEST[ 'topic' ]);

    if ($_REQUEST[ 'reifies_type' ] === 'name')
    {
        foreach ($topic->getNames([ ]) as $name)
        {
            if ($name->getId() !== $_REQUEST[ 'reifies_id' ])
                continue;

            $reifier_topic = $name->newReifierTopic();

            $ok = $reifier_topic->save();
    
            break;
        }
    }
    elseif ($_REQUEST[ 'reifies_type' ] === 'occurrence')
    {
        foreach ($topic->getOccurrences([ ]) as $occurrence)
        {
            if ($occurrence->getId() !== $_REQUEST[ 'reifies_id' ])
                continue;

            $reifier_topic = $occurrence->newReifierTopic();

            $ok = $reifier_topic->save();
    
            break;
        }
    }

    if ($ok >= 0)
        $ok = $topic->save();
}
elseif (in_array($_REQUEST[ 'reifies_type' ], [ 'association', 'role' ]))
{
    $association = $topicmap->newAssociation();
    $association->load($_REQUEST[ 'association' ]);

    if ($_REQUEST[ 'reifies_type' ] === 'association')
    {
        $reifier_topic = $association->newReifierTopic();

        $ok = $reifier_topic->save();
    }
    elseif ($_REQUEST[ 'reifies_type' ] === 'role')
    {
        foreach ($association->getRoles([ ]) as $role)
        {
            if ($role->getId() !== $_REQUEST[ 'reifies_id' ])
                continue;

            $reifier_topic = $role->newReifierTopic();

            $ok = $reifier_topic->save();
    
            break;
        }
    }

    if ($ok >= 0)
    {
        $ok = $association->save();
    }
}

if ($ok < 0)
{
    echo 'ERROR ' . $ok;
}
else
{
    $edit_topic_url = sprintf
    (
        '%sedit_topic/%s',
        TOPICBANK_BASE_URL,
        $reifier_topic->getId()
    );

    header('Location: ' . $edit_topic_url);
}
