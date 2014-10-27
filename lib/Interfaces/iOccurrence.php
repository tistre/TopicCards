<?php

namespace TopicBank\Interfaces;


interface iOccurrence extends iCore, iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
    public function getDatatype();
    public function setDatatype($str);
}
