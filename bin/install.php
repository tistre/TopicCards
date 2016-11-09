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
    $getopt->setBanner("\nTopicBank installation\n\n");
    
    echo $getopt->getHelpText();
    exit;
}

fwrite(STDOUT, "Inititalizing database…\n");

\TopicCards\Utils\InstallationUtils::initDb($topicmap);

fwrite(STDOUT, "Done.\n");
