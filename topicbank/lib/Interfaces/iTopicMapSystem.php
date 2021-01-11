<?php

namespace TopicBank\Interfaces;


interface iTopicMapSystem
{
    public function __construct(iServices $services);
    public function getServices();

    public function newTopicMap($key);
    public function getTopicMap($key);
    public function hasTopicMap($key);
    public function getTopicMapKeys();
}
