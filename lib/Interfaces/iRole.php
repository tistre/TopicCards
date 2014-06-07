<?php

namespace TopicBank\Interfaces;


interface iRole extends iReified, iTyped
{
    public function getPlayer();
    public function setPlayer($topic_id);
}
