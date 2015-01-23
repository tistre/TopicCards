<?php

namespace TopicBank\Interfaces;


interface iTopicMap
{    
    public function __construct(iServices $services);
    public function getServices();

    public function setUrl($url);
    public function getUrl();

    public function getReifierId();
    
    public function createId();
    
    public function newTopic();
    public function newAssociation();
    
    public function newFileTopic($filename);

    public function getTopicIds(array $filters);
    public function getTopicIdBySubject($uri);
    public function getTopicSubject($topic_id);
    public function getTopicSubjectIdentifier($topic_id);
    public function getTopicSubjectLocator($topic_id);
    public function getTopicLabel($topic_id);
    public function getAssociationIds(array $filters);
    public function getTopicTypeIds(array $filters);
    public function getNameTypeIds(array $filters);
    public function getNameScopeIds(array $filters);
    public function getOccurrenceTypeIds(array $filters);
    public function getOccurrenceDatatypeIds(array $filters);
    public function getOccurrenceScopeIds(array $filters);
    public function getAssociationTypeIds(array $filters);
    public function getAssociationScopeIds(array $filters);
    public function getRoleTypeIds(array $filters);
    public function getRolePlayerIds(array $filters);
}
