<?php

namespace TopicBank\Interfaces;


interface iAssociation extends iPersistent, iReified, iScoped, iTyped
{
    public function getRoles();
    public function setRoles(array $roles);
}
