<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

if (! isset($_SESSION[ 'choose_topic_history' ]))
    $_SESSION[ 'choose_topic_history' ] = [ ];

$what = $_REQUEST[ 'what' ];

if (! isset($_SESSION[ 'choose_topic_history' ][ $what ]))
    $_SESSION[ 'choose_topic_history' ][ $what ] = [ ];
    
if (! in_array($_REQUEST[ 'topic_id' ], $_SESSION[ 'choose_topic_history' ][ $what ]))
    $_SESSION[ 'choose_topic_history' ][ $what ][ ] = $_REQUEST[ 'topic_id' ];
