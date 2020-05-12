<?php
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
define('_DOMAIN_NAME_', 'blog.engrev.fr'); // $_SERVER['SERVER_NAME'] can be falsified
define('_PATH_', '/edsa-blog');
define('_COOKIE_DOMAIN_', '127.0.0.1');
define('_COOKIE_PATH_', _COOKIE_DOMAIN_.'/edsa-blog/');