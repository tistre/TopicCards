<?php

namespace Xddb\Backends\Db;


class Role extends Core implements \Xddb\Interfaces\iRole
{
    use Reified, Typed;
    
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
    
    
    public function setAll(array $data)
    {
        $ok = $this->setPlayer(isset($data[ 'player' ]) ? $data[ 'player' ] : false);
        
        if ($ok >= 0)
            $ok = $this->setAllTyped($data);
            
        if ($ok >= 0)
            $ok = $this->setAllReified($data);
            
        return $ok;
    }
}
