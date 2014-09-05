<?php

namespace TopicBank\Interfaces;


interface iTopicMap extends iCore, iReified
{    
    public function setUrl($url);
    public function getUrl();
    public function createId();
    public function newTopic();
    public function getTopics(array $filters);
    public function getTopicBySubjectIdentifier($uri);
    public function getTopicLabel($id);
    public function newAssociation();
    public function getAssociations(array $filters);
    public function getTopicTypes(array $filters);
    public function getNameTypes(array $filters);
    public function getNameScopes(array $filters);
    public function getOccurrenceTypes(array $filters);
    public function getOccurrenceDatatypes(array $filters);
    public function getOccurrenceScopes(array $filters);
    public function getAssociationTypes(array $filters);
    public function getAssociationScopes(array $filters);
    public function getRoleTypes(array $filters);
    public function getRolePlayers(array $filters);
}
