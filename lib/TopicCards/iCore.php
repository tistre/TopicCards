<?php

namespace TopicCards;


interface iCore
{
    public function __construct(iServices $services, iTopicMap $topicmap);
    public function getServices();
    public function getTopicMap();
    public function validate(&$msg_html);
}
