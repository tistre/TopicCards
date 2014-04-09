<?php

namespace Xddb\Interfaces;


interface iScoped
{
    public function getScope();
    public function setScope(array $topic_ids);
}
