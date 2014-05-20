<?php

namespace Xddb\Backends\Db;


class Topic extends Core implements \Xddb\Interfaces\iTopic
{
    use Persistent, TopicDbAdapter;
    
    protected $subject_identifiers = [ ];
    protected $subject_locators = [ ];
    protected $types = [ ];
    protected $names = [ ];
    protected $occurrences = [ ];


    public function __construct(\Xddb\Interfaces\iServices $services, $data = false)
    {
        parent::__construct($services);
        
        if (! is_array($data))
            $data = array();
            
        $this->setAll($data);        
    }
    
    
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
        if ($this->getVersion() === 0)
        {
            $ok = $this->insertAll($this->getAll());
        }
        else
        {
            $ok = $this->updateAll($this->getAll());
        }
            
        if ($ok >= 0)
            $this->setVersion($this->getVersion() + 1);
        
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
            'occurrences' => [ ]
        ];
        
        foreach ($this->names as $name)
            $result[ 'names' ][ ] = $name->getAll();
        
        foreach ($this->occurrences as $occurrence)
            $result[ 'occurrences' ][ ] = $occurrence->getAll();
        
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
            'occurrences' => [ ]
        ], $data);
        
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
        
        return 1;
    }
}
