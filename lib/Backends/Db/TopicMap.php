<?php

namespace TopicBank\Backends\Db;


class TopicMap implements \TopicBank\Interfaces\iTopicMap
{
    use TopicMapDbAdapter;
     
    protected $url;
    protected $services;
    protected $db_table_prefix;
    protected $search_index;
    protected $upload_path;
    

    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }
        
    
    public function setUrl($url)
    {
        $this->url = $url;
        
        return 1;
    }
    
    
    public function getUrl()
    {
        return $this->url;
    }
    

    public function setDbTablePrefix($prefix)
    {
        $this->db_table_prefix = $prefix;
        
        return 1;
    }
    
    
    public function getDbTablePrefix()
    {
        return $this->db_table_prefix;
    }
    
    
    public function setSearchIndex($index)
    {
        $this->search_index = $index;
        
        return 1;
    }
    
    
    public function getSearchIndex()
    {
        return $this->search_index;
    }


    public function setUploadPath($path)
    {
        $this->upload_path = $path;
        
        return 1;
    }
    
    
    public function getUploadPath()
    {
        return $this->upload_path;
    }
        
    
    public function getReifierId()
    {
        return $this->getTopicIdBySubject($this->getUrl());
    }
    
    
    public function createId()
    {
        // http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/2040279#2040279

        return sprintf
        ( 
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
    
    
    public function newTopic()
    {   
        $topic = new Topic($this->services, $this);
        
        return $topic;
    }
    
    
    public function getTopicIds(array $filters)
    {
        return $this->selectTopics($filters);
    }
    

    public function getTopicIdBySubject($uri)
    {
        return $this->selectTopicBySubject($uri);
    }
    
    
    public function getTopicSubject($topic_id)
    {
        // XXX we might want to optimize this and not do 2 calls
        // to get at the locator
        
        $result = $this->selectTopicSubjectIdentifier($topic_id);
        
        if ($result === false)
            $result = $this->selectTopicSubjectLocator($topic_id);
        
        return $result;
    }
    
    
    public function getTopicSubjectIdentifier($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
            
        return $this->selectTopicSubjectIdentifier($topic_id);
    }
    
    
    public function getTopicSubjectLocator($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
            
        return $this->selectTopicSubjectLocator($topic_id);
    }
    
    
    public function getTopicLabel($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
            
        $topic = $this->newTopic();
        
        $ok = $topic->load($topic_id);
        
        if ($ok < 0)
            return false;
        
        return $topic->getLabel();
    }
    
    
    public function newAssociation()
    {
        $association = new Association($this->services, $this);
        
        return $association;
    }
    
    
    public function getAssociationIds(array $filters)
    {
        return $this->selectAssociations($filters);
    }


    public function getTopicTypeIds(array $filters)
    {
        return $this->selectTopicTypes($filters);
    }


    public function getNameTypeIds(array $filters)
    {
        return $this->selectNameTypes($filters);
    }


    public function getNameScopeIds(array $filters)
    {
        return $this->selectNameScopes($filters);
    }


    public function getOccurrenceTypeIds(array $filters)
    {
        return $this->selectOccurrenceTypes($filters);
    }


    public function getOccurrenceDatatypeIds(array $filters)
    {
        return $this->selectOccurrenceDatatypes($filters);
    }


    public function getOccurrenceScopeIds(array $filters)
    {
        return $this->selectOccurrenceScopes($filters);
    }


    public function getAssociationTypeIds(array $filters)
    {
        return $this->selectAssociationTypes($filters);
    }


    public function getAssociationScopeIds(array $filters)
    {
        return $this->selectAssociationScopes($filters);
    }


    public function getRoleTypeIds(array $filters)
    {
        return $this->selectRoleTypes($filters);
    }


    public function getRolePlayerIds(array $filters)
    {
        return $this->selectRolePlayers($filters);
    }
    
    
    public function newFileTopic($filename)
    {
        $topic = $this->newTopic();

        $name = $topic->newName();
        
        $name->setType('http://www.strehle.de/schema/fileName');
        $name->setValue(pathinfo($filename, PATHINFO_BASENAME));
    
        $topic->setSubjectLocators([ 'file://' . $filename ]);

        $occurrence = $topic->newOccurrence();    
        $occurrence->setType('http://schema.org/contentSize');
        $occurrence->setDatatype('http://www.strehle.de/schema/sizeInBytes');
        $occurrence->setValue(filesize($filename));

        $occurrence = $topic->newOccurrence();    
        $occurrence->setType('http://www.strehle.de/schema/fileContentChecksum');
        $occurrence->setDatatype('http://www.w3.org/2001/XMLSchema#string');
        $occurrence->setValue(md5_file($filename));

        $type = 'http://www.strehle.de/schema/file';
    
        $finfo = finfo_open(FILEINFO_MIME_TYPE);    
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);

        if (strlen($mimetype) > 0)
        {
            $occurrence = $topic->newOccurrence();    
            $occurrence->setType('http://www.strehle.de/schema/mimeType');
            $occurrence->setDatatype('http://www.w3.org/2001/XMLSchema#string');
            $occurrence->setValue($mimetype);
        
            if (substr($mimetype, 0, 6) === 'image/')
                $type = 'http://schema.org/ImageObject';
        }

        $topic->setTypes([ $type ]);
    
        $size = getimagesize($filename);
    
        if (is_array($size))
        {
            $occurrence = $topic->newOccurrence();    
            $occurrence->setType('http://schema.org/width');
            $occurrence->setDatatype('http://www.w3.org/2001/XMLSchema#nonNegativeInteger');
            $occurrence->setValue($size[ 0 ]);

            $occurrence = $topic->newOccurrence();    
            $occurrence->setType('http://schema.org/height');
            $occurrence->setDatatype('http://www.w3.org/2001/XMLSchema#nonNegativeInteger');
            $occurrence->setValue($size[ 1 ]);
        }
        
        return $topic;
    }
}
