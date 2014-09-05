<?php

namespace TopicBank\Interfaces;


interface iReified extends iCore
{
    public function getReifier();
    public function setReifier($topic_id);
    public function newReifierTopic();
}
