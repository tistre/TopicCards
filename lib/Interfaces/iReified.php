<?php

namespace TopicBank\Interfaces;


interface iReified
{
    public function getReifierId();
    public function setReifierId($topic_id);
    public function newReifierTopic();
}
