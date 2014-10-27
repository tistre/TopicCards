<?php

namespace TopicBank\Interfaces;


interface iRole extends iCore, iReified, iTyped
{
    public function getPlayer();
    public function setPlayer($topic_id);
}
