<?php

namespace TopicCards;


interface iOccurrence extends iCore, iReified, iScoped, iTyped
{
    public function getValue();
    public function setValue($str);
    public function getDatatypeId();
    public function setDatatypeId($topic_id);
    public function getDatatype();
    public function setDatatype($topic_subject);
}
