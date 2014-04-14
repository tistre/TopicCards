<?php

namespace Xddb\Backends\Db;


class Association extends Core implements \Xddb\Interfaces\iAssociation
{
    use Persistent, Reified, Scoped, Typed;
    
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
        $rows = $this->services->db->selectAssociationData($this->getTopicMap(), [ 'id' => $id ]);
        
        if (! is_array($rows))
            return $rows;
            
        if (count($rows) === 0)
            return -1;
            
        return $this->setAll($rows[ 0 ]);
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
        
        foreach ($data[ 'roles' ] as $role_data)
        {
            $role = $this->newRole();
            $role->setAll($role_data);
        }
        
        return 1;
    }
}
