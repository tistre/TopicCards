<?php

namespace TopicCards\DbBackend;


class Core implements \TopicCards\iCore
{
    /** @var \TopicCards\iServices */
    protected $services;
    
    /** @var \TopicCards\iTopicMap */
    protected $topicmap;
    
    /** @var \Monolog\Logger */
    protected $logger;
    
    
    public function __construct(\TopicCards\iServices $services, \TopicCards\iTopicMap $topicmap)
    {
        $this->services = $services;
        $this->topicmap = $topicmap;
        $this->logger = $this->services->getLogger();
    }
    
    
    public function getServices()
    {
        return $this->services;
    }


    public function getTopicMap()
    {
        return $this->topicmap;
    }


    public function validate(&$msg_html)
    {
        $msg_html = '';
        
        return 0;
    }
}
