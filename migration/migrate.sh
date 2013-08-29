#!/bin/bash

# check directory
if [ -d "./core" -a -d "./migration" ]
then
   echo "Directory check OK..."
else
   echo "Wrong directory. Aborting!"
   exit 1
fi

# evaluate php executable path
if [ -n "$1" ] # path to php is given as parameter
then
   if [ ! -x "$1" -o -d "$1" ] # check for executable and non-directory
   then
      echo "Given path to PHP is no executable file. Aborting!"
      exit 1
   fi
   echo "Using given php executable at $1. PHP Version: $($1 -r "echo phpversion();" 2> /dev/null). Starting migartion:"
   PHP_BINARY="$1"
else
   php -v > /dev/null 2>&1 # try php in PATH
   if [ $? -eq 127 ] # exit code 127: command not found
   then
      echo "PHP not found in your PATH-scope. Give path to php as second parameter. Aborting!"
      exit 1
   fi
   echo "PHP executable found. Starting migration:"
   PHP_BINARY="php"
fi

echo # blank line

# introduces namespaces and migrates import() to use
$PHP_BINARY migration/migrate_import_calls.php
echo "introduced namespace-declarations..."
echo "migrated import() to use..."

# migrates class documentation (PHPDoc comments)
$PHP_BINARY migration/migrate_code_documentation.php
echo "migrated PHPDoc comments of classes..."

# migrates tag-declarations
$PHP_BINARY migration/migrate_document_controller_statements.php
$PHP_BINARY migration/migrate_taglib_declaration.php
$PHP_BINARY migration/migrate_taglib_statements.php
echo "migrated declaration and usage of taglibs..."

# migrates usage of DI-/ServiceManager
$PHP_BINARY migration/migrate_di_configuration.php
$PHP_BINARY migration/migrate_sm_calls.php
echo "migrated service calls and di-service configuration..."

# migrates usage of registry
$PHP_BINARY migration/migrate_core_registry_calls.php
echo "migrated registry calls ..."

# migrates usage of Session(Manager)
$PHP_BINARY migration/migrate_core_session_calls.php
echo "migrated session(manager) calls ..."

# add missing use statements
$PHP_BINARY migration/migrate_missing_use_statements.php
echo "Add missing use statements ..."

# migrates config
echo "configuration migration:"
$PHP_BINARY migration/migrate_config_calls.php
echo "   ... config calls"
$PHP_BINARY migration/migrate_db_config.php
echo "   ... database configuration"
$PHP_BINARY migration/migrate_cache_configuration.php
echo "   ... cache configuration"
$PHP_BINARY migration/migrate_fc_configuration.php
echo "   ... frontcontroller configuration"
$PHP_BINARY migration/migrate_gorm_configuration.php
echo "   ... GORM configuration"
$PHP_BINARY migration/migrate_pager_configuration.php
echo "   ... Pager configuration"
$PHP_BINARY migration/migrate_umgt_configuration.php
echo "   ... UMGT-modul configuration"

echo # blank line
echo # blank line
echo "Ready!"
exit 0
