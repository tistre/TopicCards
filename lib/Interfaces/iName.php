<?php

namespace Xddb\Interfaces;


interface iName extends iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
}
