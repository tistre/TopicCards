<?php

namespace TopicBank\Interfaces;


interface iTopicMap
{    
    public function __construct(iServices $services);
    public function getServices();

    public function setUrl($url);
    public function getUrl();
    
    public function createId();
    
    public function newTopic();
    public function newAssociation();

    public function getTopics(array $filters);
    // XXX rename to ...TopicIdBy...?
    public function getTopicBySubjectIdentifier($uri);
    public function getTopicLabel($id);
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
