<?php

require_once __DIR__ . '/init.php';

require_once dirname(__DIR__) . '/lib/Utils.php';

session_name(md5(TOPICBANK_CONFIG));
session_start();
