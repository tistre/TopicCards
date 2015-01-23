<?php

namespace TopicBank\Interfaces;


interface iRole extends iCore, iReified, iTyped
{
    public function getPlayerId();
    public function setPlayerId($topic_id);
    public function getPlayer();
    public function setPlayer($topic_subject);
}
