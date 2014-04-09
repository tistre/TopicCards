<?php

namespace Xddb\Backends\Db;


class Topic extends Core implements \Xddb\Interfaces\iTopic
{
    use Persistent;
    
    protected $subject_identifiers = [ ];
    protected $subject_locators = [ ];
    protected $types = [ ];
    protected $names = [ ];
    protected $occurrences = [ ];


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
    

    public function newName()
    {   
        $name = new Name($this->services);
        
        $this->names[ ] = $name;
        
        return $name;
    }

    
    public function getNames(array $filters)
    {
        return $this->names;
    }
    
    
    public function setNames(array $names)
    {
        $this->names = $names;
        return 1;
    }
    
    
    public function newOccurrence()
    {   
        $occurrence = new Occurrence($this->services);
        
        $this->occurrences[ ] = $occurrence;
        
        return $occurrence;
    }


    public function getOccurrences(array $filters)
    {
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
    
    
    public function load($id)
    {
        $rows = $this->services->db->selectTopicData($this->getTopicMap(), [ 'id' => $id ]);
        
        if (! is_array($rows))
            return $rows;
            
        if (count($rows) === 0)
            return -1;
            
        return $this->setAll($rows[ 0 ]);
    }
    
    
    public function setAll(array $data)
    {
        $this->setTypes($data[ 'types' ]);

        $this->setSubjectIdentifiers($data[ 'subject_identifiers' ]);

        $this->setSubjectLocators($data[ 'subject_locators' ]);
        
        foreach ($data[ 'names' ] as $name_data)
        {
            $name = $this->newName();
            $name->setAll($name_data);
        }
        
        foreach ($data[ 'occurrences' ] as $occurrence_data)
        {
            $occurrence = $this->newOccurrence();
            $occurrence->setAll($occurrence_data);
        }
        
        return 1;
    }
}
