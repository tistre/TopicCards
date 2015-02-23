<?php

if (PHP_SAPI === 'cli')
{   
    error_reporting(E_ALL);
    ini_set('error_log', false);
    ini_set('display_errors', 'stderr');
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function($class) 
{
    // project-specific namespace prefix
    $prefix = 'TopicBank\\';

    // base directory for the namespace prefix
    $base_dir = dirname(__DIR__) . '/lib/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    
    if (substr($class, 0, $len) !== $prefix)
    {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $filename = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($filename)) 
    {
        require $filename;
    }
});

define('TOPICBANK_BASE_DIR', dirname(__DIR__));

// Allow passing the TOPICBANK_CONFIG via --config

if (PHP_SAPI === 'cli')
{
    $key = array_search('--config', $argv, true);
    
    if (($key !== false) && (! empty($argv[ ($key + 1) ])))
        putenv('TOPICBANK_CONFIG=' . $argv[ ($key + 1) ]);
}

define('TOPICBANK_CONFIG', getenv('TOPICBANK_CONFIG'));

if (strlen(TOPICBANK_CONFIG) === 0)
    exit("TOPICBANK_CONFIG environment variable not found.\n");

if (! file_exists(TOPICBANK_CONFIG))
    exit(TOPICBANK_CONFIG . " file not found.\n");

require_once TOPICBANK_CONFIG;

?>
