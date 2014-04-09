<?php

namespace Xddb\Interfaces;


interface iOccurrence extends iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
    public function getDatatype();
    public function setDatatype($str);
}
