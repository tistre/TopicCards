<?php

namespace TopicBank\Interfaces;


interface iAssociation extends iPersistent, iReified, iScoped, iTyped
{
    const EVENT_SAVING = 'association_saving';
    const EVENT_DELETING = 'association_deleting';
    
    public function getRoles(array $filters = [ ]);
    public function setRoles(array $roles);
}
