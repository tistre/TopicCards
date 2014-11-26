<?php

require_once dirname(__DIR__) . '/include/config.php';

$services->db_utils->connect();

$filenames = $argv;

unset($filenames[ 0 ]);

foreach ($filenames as $filename)
{
    $objects = new \TopicBank\Utils\XtmReader($filename, $topicmap);

    $services->db_utils->beginTransaction();

    $ok = 0;

    foreach ($objects as $object)
    {
        if (! is_object($object))
            continue;
            
        $ok = $object->save();
        
        printf
        (
            "%s: %s <%s> (%s)\n",
            $filename,
            ($object instanceof \TopicBank\Interfaces\iTopic ? 'topic' : 'association'),
            $object->getId(),
            $ok
        );
    
        if ($ok < 0)
        {
            $services->db_utils->rollback();
            break;
        }
    }

    if ($ok >= 0)
        $services->db_utils->commit();
}
