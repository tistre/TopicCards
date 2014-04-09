<?php

namespace Xddb\Interfaces;


interface iCore
{
    public function __construct(iServices $services);
    public function getServices();
}
