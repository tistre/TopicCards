<?php

namespace TopicBank\Interfaces;


interface iTopic extends iPersistent
{
    const REIFIES_NONE = 0;
    const REIFIES_NAME = 1;
    const REIFIES_OCCURRENCE = 2;
    const REIFIES_ASSOCIATION = 3;
    const REIFIES_ROLE = 4;
    
    public function getSubjectIdentifiers();
    public function setSubjectIdentifiers(array $strings);
    public function getSubjectLocators();
    public function setSubjectLocators(array $strings);
    public function getTypes();
    public function setTypes(array $topic_ids);
    public function newName();
    public function getNames(array $filters);
    public function setNames(array $names);
    public function newOccurrence();
    public function getOccurrences(array $filters);
    public function setOccurrences(array $occurrences);
    public function getIsReifier();
    public function setIsReifier($is_reifier);
    public function getReifiedObject();
}
