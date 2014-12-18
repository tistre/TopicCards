<?php

namespace TopicBank\Backends\Db;


class Association extends Core implements \TopicBank\Interfaces\iAssociation
{
    use Id, Persistent, Reified, Scoped, Typed, AssociationDbAdapter;
    
    protected $roles = [ ];
    
    
    public function newRole()
    {   
        $role = new Role($this->services, $this->topicmap);
        
        $this->roles[ ] = $role;
        
        return $role;
    }


    public function getRoles(array $filters = [ ])
    {
        if (count($filters) === 0)            
            return $this->roles;
        
        $result = [ ];
        
        if (isset($filters[ 'type_subject' ]))
            $filters[ 'type' ] = $this->getTopicMap()->getTopicBySubject($filters[ 'type_subject' ]);

        if (isset($filters[ 'player_subject' ]))
            $filters[ 'player' ] = $this->getTopicMap()->getTopicBySubject($filters[ 'player_subject' ]);

        foreach ($this->roles as $role)
        {
            if (isset($filters[ 'type' ]))
            {
                if ($role->getType() === $filters[ 'type' ])
                    $result[ ] = $role;
            }
            elseif (isset($filters[ 'player' ]))
            {
                if ($role->getPlayer() === $filters[ 'player' ])
                    $result[ ] = $role;
            }
        }
        
        return $result;
    }
    
    
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return 1;
    }
    
    
    public function validate(&$msg_html)
    {
        $result = 1;
        $msg_html = '';
        
        foreach ($this->getRoles([ ]) as $role)
        {
            $ok = $role->validate($msg);
            
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
        
        return $ok;
    }
    
    
    public function getAll()
    {
        $result = 
        [
            'roles' => [ ]
        ];

        foreach ($this->getRoles() as $role)
            $result[ 'roles' ][ ] = $role->getAll();
            
        $result = array_merge($result, $this->getAllId());

        $result = array_merge($result, $this->getAllPersistent());

        $result = array_merge($result, $this->getAllTyped());

        $result = array_merge($result, $this->getAllReified());

        $result = array_merge($result, $this->getAllScoped());

        return $result;
    }
    
    
    public function setAll(array $data)
    {
        $data = array_merge(
        [
            'roles' => [ ]
        ], $data);
        
        $this->setAllId($data);
        
        $this->setAllPersistent($data);
        
        $this->setAllTyped($data);
            
        $this->setAllReified($data);
            
        $this->setAllScoped($data);
        
        $this->setRoles([ ]);
        
        foreach ($data[ 'roles' ] as $role_data)
        {
            $role = $this->newRole();
            $role->setAll($role_data);
        }
        
        return 1;
    }
    
    
    public function delete()
    {
        if ($this->getVersion() === 0)
            return 0;
        
        return $this->deleteById($this->getId(), $this->getVersion());
    }
}
