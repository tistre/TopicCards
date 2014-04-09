<?php

namespace Xddb\Backends\Db;


class Name extends Core implements \Xddb\Interfaces\iName
{
    use Reified, Scoped, Typed;
    
    protected $value = false;
    
    
    public function getValue()
    {
        return $this->value;
    }
    
    
    public function setValue($str)
    {
        $this->value = $str;
        return 1;
    }
    
    
    public function setAll(array $data)
    {
        $ok = $this->setValue(isset($data[ 'value' ]) ? $data[ 'value' ] : false);
        
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        if ($ok >= 0)
            $ok = $this->setAllScoped($data);
            
        return $ok;
    }
}
