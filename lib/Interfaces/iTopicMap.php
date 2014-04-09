<?php

namespace Xddb\Interfaces;


interface iTopicMap extends iCore, iReified
{    
    public function setUrl($url);
    public function getUrl();
    public function newTopic();
    public function getTopics(array $filters);
    public function newAssociation();
    public function getAssociations(array $filters);
}
