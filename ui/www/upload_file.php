<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

if (strlen($topicmap->getUploadPath()) === 0)
    die('Upload path not set');
    
// Form submitted?

if 
(
    ($_SERVER[ 'REQUEST_METHOD' ] === 'POST') 
    && is_array($_FILES[ 'file' ]) 
    && ($_FILES[ 'file' ][ 'error' ] === 0)
    && is_uploaded_file($_FILES[ 'file' ][ 'tmp_name' ])
)
{
    $file_arr = $_FILES[ 'file' ];
    
    $topic_id = $topicmap->createId();
    
    $dirname = $topicmap->getUploadPath();
    
    if (! file_exists($dirname))
        mkdir($dirname);

    $extension = strtolower(pathinfo($file_arr[ 'name' ], PATHINFO_EXTENSION));
    
    // Make sure we don't accidentally execute uploaded PHP code
    
    if ((substr($extension, 0, 3) === 'php') || ($extension === 'phtml'))
        $extension .= '.txt';
    
    $filename = sprintf('%s/%s.%s', $dirname, $topic_id, $extension);
    
    $ok = move_uploaded_file($file_arr[ 'tmp_name' ], $filename);
    
    if (! $ok)
        die('Failed to copy file to ' . $filename);

    $topic = $topicmap->newFileTopic($filename);
    
    $topic->setId($topic_id);

    // Fix the name: Use the name provided on upload, not the randomly generated name
    
    foreach ($topic->getNames([ 'type_subject' => 'http://www.strehle.de/schema/fileName' ]) as $name)
        $name->setValue(pathinfo($file_arr[ 'name' ], PATHINFO_BASENAME));
    
    $ok = $topic->save();

    if ($ok >= 0)
    {
        $edit_topic_url = sprintf
        (
            '%stopic/%s',
            TOPICBANK_BASE_URL,
            $topic_id
        );

        header('Location: ' . $edit_topic_url);
    }
}

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifier());

include TOPICBANK_BASE_DIR . '/ui/templates/upload_file.tpl.php';
