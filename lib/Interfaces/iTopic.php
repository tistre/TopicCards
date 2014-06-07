<?php

namespace TopicBank\Interfaces;


interface iTopic extends iPersistent
{
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
}
