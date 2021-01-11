<?php

require_once __DIR__ . '/init.php';

session_name(md5(TOPICBANK_CONFIG));
session_start();
