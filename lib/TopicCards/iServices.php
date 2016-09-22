<?php

namespace TopicCards;


interface iServices
{
    public function getTopicMapSystem();


    /**
     * @param \Monolog\Logger $logger
     * @return void
     */
    public function setLogger(\Monolog\Logger $logger);


    /**
     * @return \Monolog\Logger
     */
    public function getLogger();
}
