<?php

namespace Xddb\Backends\Db;


class Occurrence extends Core implements \Xddb\Interfaces\iOccurrence
{
    use Reified, Scoped, Typed;
    
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
    
    
    public function setAll(array $data)
    {
        $ok = $this->setValue(isset($data[ 'value' ]) ? $data[ 'value' ] : false);
        $ok = $this->setDatatype(isset($data[ 'datatype' ]) ? $data[ 'datatype' ] : false);
        
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        if ($ok >= 0)
            $ok = $this->setAllScoped($data);
            
        return $ok;
    }
}
