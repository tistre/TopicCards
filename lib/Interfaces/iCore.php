<?php

namespace TopicBank\Interfaces;


interface iCore
{
    public function __construct(iServices $services);
    public function getServices();
}
