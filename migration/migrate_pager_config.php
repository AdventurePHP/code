<?php
include(dirname(__FILE__) . '/migrate_base.php');

// {ENVIRONMENT}_pager.ini + {ENVIRONMENT}_arraypager.ini
$files = find('.', '*pager.ini');

foreach ($files as $file) {
   $content = file_get_contents($file);
   $content = preg_replace('#^Pager\.#m', '', $content);
   file_put_contents($file, $content);
}
