#!/bin/bash
php run-tests.php -p `which php` --temp-target tmp --show-diff *.phpt
