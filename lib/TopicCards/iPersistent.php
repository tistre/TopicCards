<?php

namespace TopicCards;


interface iPersistent extends iCore
{
    public function getId();
    public function setId($id);
    public function load($id);
    public function save();
    public function delete();
    public function getCreated();
    public function getUpdated();
    public function getVersion();
}
