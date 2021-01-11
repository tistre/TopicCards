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

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nTopicBank topic subject identifier/locator to ID\n\n");
    
    echo $getopt->getHelpText();
    exit;
}

if ($getopt->getOperand(0) === '-')
{
    while (! feof(STDIN))
    {
        $subject = trim(fgets(STDIN));
        
        if ($subject === '')
            continue;
            
        echo $topicmap->getTopicIdBySubject($subject) . "\n";
    }
}
else
{
    foreach ($getopt->getOperands() as $subject)
    {
        echo $topicmap->getTopicIdBySubject($subject) . "\n";
    }
}
