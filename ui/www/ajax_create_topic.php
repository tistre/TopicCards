<?php

require_once dirname(dirname(__DIR__)) . '/include/init.php';

$topic = $topicmap->newTopic();

$topic->setId($topicmap->createId());

$name = $topic->newName();
$name->setType('http://schema.org/name');
$name->setValue(trim($_REQUEST[ 'name' ]));
error_log(print_r($_REQUEST, true));

if (! empty($_REQUEST[ 'type' ]))
{
    $types = $_REQUEST[ 'type' ];
    
    if (! is_array($types))
        $types = [ $types ];
        
    $topic->setTypeIds($types);
}

if (! empty($_REQUEST[ 'subject_identifier' ]))
{
    $topic->setSubjectIdentifiers([ $_REQUEST[ 'subject_identifier' ] ]);
}

$ok = $topic->save();

echo json_encode(array( 'id' => $topic->getId(), 'name' => $name->getValue() ));
