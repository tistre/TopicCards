<?php

error_reporting(E_ALL);
ini_set('error_log', false);
ini_set('display_errors', 'stderr');

require_once dirname(__DIR__) . '/include/config.php';

$services->db_utils->connect();

$filenames = $argv;

unset($filenames[ 0 ]);

foreach ($filenames as $filename)
{
    $xml = file_get_contents($filename);

    $importer = new \TopicBank\Utils\XtmImport();
    
    $objects = $importer->importObjects($xml, $topicmap);

    $services->db_utils->beginTransaction();

    $ok = 0;

    foreach ($objects as $object)
    {
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
