<?php

namespace TopicBank\Interfaces;


interface iCore
{
    public function __construct(iServices $services, iTopicMap $topicmap);
    public function getServices();
    public function getTopicMap();
}
