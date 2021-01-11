<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/init.php';

$getopt = new Getopt(
[
    new Option(null, 'config', Getopt::REQUIRED_ARGUMENT),
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt->getOperand(0) === '-')
{
    while (! feof(STDIN))
    {
        $filename = trim(fgets(STDIN));
        
        if ($filename === '')
            continue;
            
        importFile($filename);
    }
}
else
{
    foreach ($getopt->getOperands() as $filename)
    {
        importFile($filename);
    }
}

function importFile($filename)
{
    global $topicmap;
    global $services;
    
    $objects = new \TopicBank\Utils\XtmReader($filename, $topicmap);

    $services->db_utils->connect();

    $services->db_utils->beginTransaction();

    $ok = 0;

    foreach ($objects as $object)
    {
        if (! is_object($object))
            continue;
            
        $ok = $object->save();
        
        $subject = '';
        
        if ($object instanceof \TopicBank\Interfaces\iTopic)
        {
            foreach ($object->getSubjectIdentifiers() as $subject)
                break;
                
            if ($subject === '')
            {
                foreach ($object->getSubjectLocators() as $subject)
                    break;
            }
            
            if ($subject !== '')
                $subject = sprintf('[%s] ', $subject);
        }
        
        printf
        (
            "%s: Created %s %s<%s> (%s)\n",
            $filename,
            ($object instanceof \TopicBank\Interfaces\iTopic ? 'topic' : 'association'),
            $subject,
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
