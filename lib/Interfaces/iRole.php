<?php

namespace Xddb\Interfaces;


interface iRole extends iReified, iTyped
{
    public function getPlayer();
    public function setPlayer($topic_id);
}
