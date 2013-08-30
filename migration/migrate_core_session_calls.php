<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

foreach ($files as $file) {
   $content = file_get_contents($file);

   // rename class
   $content = str_replace('new SessionManager(', 'new Session(', $content);

   // rename methods
   $content = str_replace('->destroySession(', '->destroy(', $content);
   $content = str_replace('->loadSessionData(', '->load(', $content);
   $content = str_replace('->loadAllSessionData(', '->loadAll(', $content);
   $content = str_replace('->getEntryDataKeys(', '->getEntryKeys(', $content);
   $content = str_replace('->saveSessionData(', '->save(', $content);
   $content = str_replace('->deleteSessionData(', '->delete(', $content);

   file_put_contents($file, $content);
}
