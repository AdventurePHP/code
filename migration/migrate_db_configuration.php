<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*connections.ini');

foreach ($files as $file) {
   $content = file_get_contents($file);

   // remove "DB." prefix from all entries
   $content = str_replace('DB.Host', 'Host', $content);
   $content = str_replace('DB.User', 'User', $content);
   $content = str_replace('DB.Pass', 'Pass', $content);
   $content = str_replace('DB.Name', 'Name', $content);
   $content = str_replace('DB.Type', 'Type', $content);
   $content = str_replace('DB.DebugMode', 'DebugMode', $content);
   $content = str_replace('DB.Charset', 'Charset', $content);
   $content = str_replace('DB.Collation', 'Collation', $content);
   $content = str_replace('DB.PDO', 'PDO', $content);
   $content = str_replace('DB.Socket', 'Socket', $content);

   file_put_contents($file, $content);
}
