<?php

namespace TopicBank\Backends\Db;


class Occurrence extends Core implements \TopicBank\Interfaces\iOccurrence
{
    use Reified, Scoped, Typed, OccurrenceDbAdapter;
    
    protected $value = false;
    protected $datatype = false;
    
    
    public function getValue()
    {
        return $this->value;
    }
    
    
    public function setValue($str)
    {
        $this->value = $str;
        return 1;
    }
    
    
    public function getDatatype()
    {
        return $this->datatype;
    }
    
    
    public function setDatatype($str)
    {
        $this->datatype = $str;
        return 1;
    }
    
    
    public function getAll()
    {
        $result = 
        [
            'value' => $this->getValue(),
            'datatype' => $this->getDatatype()
        ];

        $result = array_merge($result, $this->getAllTyped());

        $result = array_merge($result, $this->getAllReified());

        $result = array_merge($result, $this->getAllScoped());
            
        return $result;
    }
    
    
    public function setAll(array $data)
    {
        $data = array_merge(
        [
            'value' => false,
            'datatype' => false
        ], $data);
        
        $ok = $this->setValue($data[ 'value' ]);
        $ok = $this->setDatatype($data[ 'datatype' ]);
        
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        if ($ok >= 0)
            $ok = $this->setAllScoped($data);
            
        return $ok;
    }
}
