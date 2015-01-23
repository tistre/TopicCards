<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/config.php';


function executeAction($id)
{
    $php_class = getPhpClass($id);
    
    if ($php_class === false)
        return false;
    
    require_once($php_class[ 'class_file' ]);
    
    $class_name = $php_class[ 'class_name' ];
    
    $obj = new $class_name($php_class[ 'constructor_arg' ]);
    
    var_dump($obj->execute());
}


function getPhpClass($id)
{
    global $topicmap;
    
    // Load "Action" association
    
    $association = $topicmap->newAssociation();
    
    $ok = $association->load($id);
    
    if ($ok < 0)
    {
        fwrite(STDERR, sprintf("ERROR Cannot load association <%s> (%s)\n", $id, $ok));
        return;
    }
    
    if ($association->getTypeSubject() !== 'http://www.strehle.de/schema/Action')
    {
        fwrite(STDERR, sprintf("ERROR Association <%s> is not an action\n", $id));
        return;
    }
    
    // Get PHP class name and location from actionCode role
    
    $class_file = $class_name = $class_topic = false;
    
    foreach ($association->getRoles([ 'type' => 'http://www.strehle.de/schema/actionCode' ]) as $role)
    {
        $class_topic = $topicmap->newTopic();
        $class_topic->load($role->getPlayerId());
        
        if (! $class_topic->hasType('http://www.strehle.de/schema/PhpClass'))
            continue;
        
        // Get the file that contains (or is able to autoload) the PHP class
        // Must be specified as a subject locator using the file: protocol
        
        $locators = $class_topic->getSubjectLocators();
        
        $prefix = 'file://';
        
        foreach ($locators as $locator)
        {
            if (substr($locator, 0, strlen($prefix)) !== $prefix)
                continue;
                
            $class_file = substr($locator, strlen($prefix));
            break;
        }
        
        if ($class_file === false)
            continue;
        
        // Get the class name
        
        foreach ($class_topic->getNames([ 'type' => 'http://www.strehle.de/schema/phpClassName' ]) as $name)
        {
            if (count($name->getScopeIds()) > 0)
                continue;
                
            $class_name = $name->getValue();
        }
    }

    if ($class_name === false)
        return false;
        
    return
    [
        'class_file' => $class_file,
        'class_name' => $class_name,
        'constructor_arg' => $association
    ];
}


$getopt = new Getopt(
[
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nExecute TopicBank action\n\n");
    
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
            
        executeAction($id);
    }
}
else
{
    foreach ($getopt->getOperands() as $id)
    {
        executeAction($id);
    }
}
