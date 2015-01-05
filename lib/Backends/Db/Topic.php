<?php

namespace TopicBank\Backends\Db;

use \TopicBank\Interfaces\iTopic;


class Topic extends Core implements iTopic
{
    use Id, Persistent, TopicDbAdapter, TopicSearchAdapter;
    
    protected $subject_identifiers = [ ];
    protected $subject_locators = [ ];
    protected $types = [ ];
    protected $names = [ ];
    protected $occurrences = [ ];
    protected $isreifier = 0;


    public function getSubjectIdentifiers()
    {
        return $this->subject_identifiers;
    }
    
    
    public function setSubjectIdentifiers(array $strings)
    {
        $this->subject_identifiers = $strings;
        return 1;
    }
    
    
    public function getSubjectLocators()
    {
        return $this->subject_locators;
    }
    
    
    public function setSubjectLocators(array $strings)
    {
        $this->subject_locators = $strings;
        return 1;
    }
    
    
    public function getTypes()
    {
        return $this->types;
    }
    
    
    public function setTypes(array $topic_ids)
    {
        $this->types = $topic_ids;        
        return 1;
    }
    

    public function getTypeSubjects()
    {
        $result = [ ];
        
        foreach ($this->getTypes() as $topic_id)
            $result[ ] = $this->getTopicMap()->getTopicSubject($topic_id);
            
        return $result;
    }
    
    
    public function setTypeSubjects(array $topic_subjects)
    {
        $topic_ids = [ ];
        $result = 1;
        
        foreach ($topic_subjects as $topic_subject)
        {
            $topic_id = $this->getTopicMap()->getTopicBySubject($topic_subject);
            
            if (strlen($topic_id) === 0)
            {
                $result = -1;
            }
            else
            {
                $topic_ids[ ] = $topic_id;
            }   
        }
        
        $ok = $this->setTypes($topic_ids);
        
        if ($ok < 0)
            $result = $ok;
        
        return $result;
    }
    
        
    public function hasType($topic_id)
    {
        return in_array($topic_id, $this->types);
    }
    
    
    public function hasTypeSubject($topic_subject)
    {
        return $this->hasType($this->getTopicMap()->getTopicBySubject($topic_subject));
    }
    

    public function newName()
    {   
        $name = new Name($this->services, $this->topicmap);
        
        $this->names[ ] = $name;
        
        return $name;
    }

    
    public function getNames(array $filters = [ ])
    {
        if (count($filters) === 0)            
            return $this->names;
        
        $result = [ ];
        
        if (isset($filters[ 'type_subject' ]))
            $filters[ 'type' ] = $this->getTopicMap()->getTopicBySubject($filters[ 'type_subject' ]);

        foreach ($this->names as $name)
        {
            if (isset($filters[ 'type' ]))
            {
                if ($name->getType() !== $filters[ 'type' ])
                    continue;
            }

            if (isset($filters[ 'reifier' ]))
            {
                if ($name->getReifier() !== $filters[ 'reifier' ])
                    continue;
            }

            $result[ ] = $name;
        }
        
        return $result;
    }
    
    
    public function setNames(array $names)
    {
        $this->names = $names;
        return 1;
    }
    

    public function getLabel()
    {
        $result = '';

        // Prefer basename, otherwise use first name without scope
        
        $basename = '';

        foreach ($this->getNames([ ]) as $name)
        {
            if (count($name->getScope()) > 0)
                continue;
    
            if ($name->getType() === 'basename')
                $basename = $name->getValue();

            if (strlen($result) === 0)    
                $result = $name->getValue();
        }

        if (strlen($basename) > 0)
            $result = $basename;
                
        return $result;
    }

    
    public function newOccurrence()
    {   
        $occurrence = new Occurrence($this->services, $this->topicmap);
        
        $this->occurrences[ ] = $occurrence;
        
        return $occurrence;
    }


    public function getOccurrences(array $filters = [ ])
    {
        if (count($filters) === 0)
            return $this->occurrences;
            
        $result = [ ];
        
        foreach ($this->occurrences as $occurrence)
        {
            if (isset($filters[ 'type' ]) && ($occurrence->getType() !== $filters[ 'type' ]))
                continue;
                
            $result[ ] = $occurrence;
        }
        
        return $result;
    }
    
    
    public function setOccurrences(array $occurrences)
    {
        $this->occurrences = $occurrences;
        return 1;
    }


    public function getIsReifier()
    {
        return $this->isreifier;
    }
    

    public function setIsReifier($isreifier)
    {
        $this->isreifier = intval($isreifier);
        return 1;
    }
    
    
    public function getReifiedObject()
    {
        return $this->selectReifiedObjectInfo($this->id, $this->isreifier);
    }
    
    
    public function validate(&$msg_html)
    {
        $result = 1;
        $msg_html = '';
        
        foreach (array_merge($this->getNames([ ]), $this->getOccurrences([ ])) as $obj)
        {
            $ok = $obj->validate($msg);
            
            if ($ok < 0)
            {
                $result = $ok;
                $msg_html .= $msg;
            }
        }
        
        return $result;
    }
    
    
    public function load($id)
    {
        $rows = $this->selectAll([ 'id' => $id ]);
        
        if (! is_array($rows))
            return $rows;
            
        if (count($rows) === 0)
            return -1;
            
        $ok = $this->setAll($rows[ 0 ]);
        
        if ($ok >= 0)
            $this->loaded = true;
            
        return $ok;
    }
    
    
    public function save()
    {
        $ok = $this->validate($dummy);
        
        if ($ok < 0)
            return $ok;
            
        if ($this->getVersion() === 0)
        {
            if (strlen($this->getId()) === 0)
                $this->setId($this->getTopicmap()->createId());
                
            $ok = $this->insertAll($this->getAll());
        }
        else
        {
            $ok = $this->updateAll($this->getAll());
        }
            
        if ($ok >= 0)
        {
            $this->setVersion($this->getVersion() + 1);
            
            $this->index();
        }
        
        return $ok;
    }
    
    
    public function getAll()
    {   
        $result = 
        [
            'types' => $this->getTypes(), 
            'subject_identifiers' => $this->getSubjectIdentifiers(), 
            'subject_locators' => $this->getSubjectLocators(), 
            'names' => [ ], 
            'occurrences' => [ ],
            'isreifier' => $this->getIsReifier()
        ];
        
        foreach ($this->names as $name)
            $result[ 'names' ][ ] = $name->getAll();
        
        foreach ($this->occurrences as $occurrence)
            $result[ 'occurrences' ][ ] = $occurrence->getAll();
        
        $result = array_merge($result, $this->getAllId());
        $result = array_merge($result, $this->getAllPersistent());
                
        return $result;
    }


    public function setAll(array $data)
    {   
        $data = array_merge(
        [
            'types' => [ ], 
            'subject_identifiers' => [ ], 
            'subject_locators' => [ ], 
            'names' => [ ], 
            'occurrences' => [ ],
            'isreifier' => 0
        ], $data);
        
        $this->setAllId($data);

        $this->setAllPersistent($data);
        
        $this->setTypes($data[ 'types' ]);

        $this->setSubjectIdentifiers($data[ 'subject_identifiers' ]);

        $this->setSubjectLocators($data[ 'subject_locators' ]);
        
        $this->setNames([ ]);
        
        foreach ($data[ 'names' ] as $name_data)
        {
            $name = $this->newName();
            $name->setAll($name_data);
        }
        
        $this->setOccurrences([ ]);
        
        foreach ($data[ 'occurrences' ] as $occurrence_data)
        {
            $occurrence = $this->newOccurrence();
            $occurrence->setAll($occurrence_data);
        }
        
        $this->setIsReifier($data[ 'isreifier' ]);
        
        return 1;
    }
    
    
    public function delete()
    {
        if ($this->getVersion() === 0)
            return 0;

        $this->removeFromIndex();
        
        // XXX to be implemented: if this topic is a reifier, empty
        // the reifier property in the reifying object
        
        $ok = $this->deleteById($this->getId(), $this->getVersion());
        
        // Sort of manual rollback: If deletion failed, re-add to index
        
        if ($ok < 0)
            $this->index();
            
        return $ok;
    }
}
