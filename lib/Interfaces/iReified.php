<?php

namespace TopicBank\Interfaces;


interface iReified
{
    public function getReifier();
    public function setReifier($topic_id);
    public function newReifierTopic();
}
