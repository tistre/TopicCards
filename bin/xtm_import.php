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
        {
            continue;
        }
            
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
    
    $objects = new \TopicCards\Utils\XtmReader($filename, $topicmap);

    $ok = 0;

    foreach ($objects as $object)
    {
        if (! is_object($object))
        {
            continue;
        }
            
        $ok = $object->save();
        
        $subject = '';
        
        if ($object instanceof \TopicCards\iTopic)
        {
            foreach ($object->getSubjectIdentifiers() as $subject)
            {
                break;
            }
                
            if ($subject === '')
            {
                foreach ($object->getSubjectLocators() as $subject)
                {
                    break;
                }
            }
            
            if ($subject !== '')
            {
                $subject = sprintf('[%s] ', $subject);
            }
        }
        
        printf
        (
            "%s: Created %s %s<%s> (%s)\n",
            $filename,
            ($object instanceof \TopicCards\iTopic ? 'topic' : 'association'),
            $subject,
            $object->getId(),
            $ok
        );
    
        if ($ok < 0)
        {
            break;
        }
    }
}
