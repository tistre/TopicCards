<?php

namespace TopicCards\DbBackend;


class Name extends Core implements \TopicCards\iName
{
    use Id, Reified, Scoped, Typed, NameDbAdapter;
    
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
    
    
    public function getAll()
    {
        $result = 
        [
            'value' => $this->getValue()
        ];

        $result = array_merge($result, $this->getAllId());

        $result = array_merge($result, $this->getAllTyped());

        $result = array_merge($result, $this->getAllReified());

        $result = array_merge($result, $this->getAllScoped());
            
        return $result;
    }
    
    
    public function setAll(array $data)
    {
        $data = array_merge(
        [
            'value' => false
        ], $data);
        
        $ok = $this->setValue($data[ 'value' ]);
        
        if ($ok >= 0)
            $ok = $this->setAllId($data);
            
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        if ($ok >= 0)
            $ok = $this->setAllScoped($data);
            
        return $ok;
    }


    /**
     * Mark an existing (saved) name for removal on topic save
     */
    public function remove()
    {
        $this->setValue('');
    }
}
