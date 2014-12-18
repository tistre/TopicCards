<?php

namespace TopicBank\Interfaces;


interface iTyped
{
    public function getType();
    public function setType($topic_id);
    public function getTypeSubject();
    public function setTypeSubject($topic_subject);
    public function hasType($topic_id);
    public function hasTypeSubject($topic_subject);
}
