<?php
date_default_timezone_set('UTC');
error_reporting(0);
chdir(__DIR__.'/../');

require 'lib/simple_html_dom.php';
define('PATH_ROOT', realpath(__DIR__.'/../'));
define('PATH_TEMPLATES', PATH_ROOT.'/tests/templates');
