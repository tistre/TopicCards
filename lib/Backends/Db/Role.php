<?php

namespace TopicBank\Backends\Db;


class Role extends Core implements \TopicBank\Interfaces\iRole
{
    use Reified, Typed, RoleDbAdapter;
    
    protected $player = false;
    
    
    public function getPlayer()
    {
        return $this->player;
    }
    
    
    public function setPlayer($topic_id)
    {
        $this->player = $topic_id;
        return 1;
    }
    
    
    public function getAll()
    {
        $result =
        [
            'player' => $this->getPlayer()
        ];
        
        $result = array_merge($result, $this->getAllTyped());

        $result = array_merge($result, $this->getAllReified());
            
        return $result;
    }
    
    
    public function setAll(array $data)
    {
        $data = array_merge(
        [
            'player' => false
        ], $data);
        
        $ok = $this->setPlayer($data[ 'player' ]);
        
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        return $ok;
    }
}
