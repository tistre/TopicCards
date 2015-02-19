<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/init.php';


$getopt = new Getopt(
[
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nTopicBank topic ID to subject identifier\n\n");
    
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
            
        echo $topicmap->getTopicSubject($id) . "\n";
    }
}
else
{
    foreach ($getopt->getOperands() as $id)
    {
        echo $topicmap->getTopicSubject($id) . "\n";
    }
}
