<?php

namespace Xddb\Interfaces;


interface iPersistent extends iCore
{
    public function getTopicMap();
    public function setTopicMap(iTopicMap $topicmap);
    public function getId();
    public function setId($id);
    public function load($id);
    public function save();
    public function delete();
}
