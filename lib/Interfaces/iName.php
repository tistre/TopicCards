<?php

namespace TopicBank\Interfaces;


interface iName extends iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
}
