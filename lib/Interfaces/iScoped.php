<?php

namespace TopicBank\Interfaces;


interface iScoped
{
    public function getScopeIds();
    public function setScopeIds(array $topic_ids);
    public function getScope();
    public function setScope(array $topic_subjects);
}
