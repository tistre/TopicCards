<?php

namespace TopicBank\Interfaces;


interface iName extends iCore, iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
}
