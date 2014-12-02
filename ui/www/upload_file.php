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
    
    $dirname = sprintf('%s/%s', $topicmap->getUploadPath(), date('Y-m-d'));
    
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

    $finfo = finfo_open(FILEINFO_MIME_TYPE);    
    $mimetype = finfo_file($finfo, $filename);
    finfo_close($finfo);

    $topic = $topicmap->newTopic();
    $topic->setId($topic_id);

    // XXX not every file is an ImageObject!
    $topic->setTypes([ $topicmap->getTopicBySubjectIdentifier('http://schema.org/ImageObject') ]);        
    
    $name = $topic->newName();
        
    $name->setType($topicmap->getTopicBySubjectIdentifier('http://schema.org/name'));
    $name->setValue(pathinfo($file_arr[ 'name' ], PATHINFO_BASENAME));
    
    $topic->setSubjectLocators([ 'file://' . $filename ]);

    $occurrence = $topic->newOccurrence();    
    $occurrence->setType($topicmap->getTopicBySubjectIdentifier('http://schema.org/contentSize'));
    $occurrence->setDatatype($topicmap->getTopicBySubjectIdentifier('http://www.strehle.de/schema/sizeInBytes'));
    $occurrence->setValue(filesize($filename));

    $size = getimagesize($filename);
    
    if (is_array($size))
    {
        $occurrence = $topic->newOccurrence();    
        $occurrence->setType($topicmap->getTopicBySubjectIdentifier('http://schema.org/width'));
        $occurrence->setDatatype($topicmap->getTopicBySubjectIdentifier('http://www.w3.org/2001/XMLSchema#nonNegativeInteger'));
        $occurrence->setValue($size[ 0 ]);

        $occurrence = $topic->newOccurrence();    
        $occurrence->setType($topicmap->getTopicBySubjectIdentifier('http://schema.org/height'));
        $occurrence->setDatatype($topicmap->getTopicBySubjectIdentifier('http://www.w3.org/2001/XMLSchema#nonNegativeInteger'));
        $occurrence->setValue($size[ 1 ]);
    }
    
    $ok = $topic->save();

    if ($ok >= 0)
    {
        $edit_topic_url = sprintf
        (
            '%sedit_topic/%s',
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
