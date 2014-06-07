<?php

namespace TopicBank\Backends\Db;


class Association extends Core implements \TopicBank\Interfaces\iAssociation
{
    use Persistent, Reified, Scoped, Typed, AssociationDbAdapter;
    
    protected $roles = [ ];
    
    
    public function newRole()
    {   
        $role = new Role($this->services);
        
        $this->roles[ ] = $role;
        
        return $role;
    }


    public function getRoles()
    {
        return $this->roles;
    }
    
    
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
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
}
