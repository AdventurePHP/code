#!/bin/bash
########################################################################################################################
# APF 2.0 automatic migration script
########################################################################################################################

echo "######################################"
echo "# APF 2.0 automatic migration        #"
echo "######################################"
echo

# check directory
echo -n "Checking directory ... "
if [ -d "./core" -a -d "./migration" ]
then
   echo -e "\e[00;32m[OK]\e[00m"
else
   echo -e "\e[00;31m[ERROR]\e[00m"
   echo
   echo -e "\e[00;31mScript not started within apps/APF directory. This is where \"core\", \"tools\", \"modules\" etc. directories reside. Aborting!\e[00m"
   exit 1
fi

# evaluate php executable path
echo -n "Checking PHP executable available ... "
if [ -n "$1" ] # path to php is given as parameter
then
   if [ ! -x "$1" -o -d "$1" ] # check for executable and non-directory
   then
      echo "Given path to PHP is no executable file. Aborting!"
      exit 1
   fi
   echo -e "\e[00;32m[OK]\e[00m"
   echo
   echo "Using given php executable at $1. PHP Version: $($1 -r "echo phpversion();" 2> /dev/null)."
   PHP_BINARY="$1"
else
   php -v > /dev/null 2>&1 # try php in PATH
   if [ $? -eq 127 ] # exit code 127: command not found
   then
      echo -e "\e[00;31m[ERROR]\e[00m"
      echo
      echo -e "\e[00;31mPHP not found in your PATH-scope. Provide path to php as second parameter. Aborting!\e[00m"
      exit 1
   fi
   echo "[OK]"
   echo "PHP executable found."
   PHP_BINARY="php"
fi

echo
echo "######################################"
echo
echo "Starting migration ..."

# introduces namespaces and migrates import() to use
$PHP_BINARY migration/migrate_import_calls.php
echo "* Introduced namespace declarations ..."
echo "* Switched from import() to use ..."

# migrates class documentation (PHPDoc comments)
$PHP_BINARY migration/migrate_code_documentation.php
echo "* Migrated PHPDoc comments ..."

# migrates tag-declarations
$PHP_BINARY migration/migrate_document_controller_statements.php
$PHP_BINARY migration/migrate_taglib_declaration.php
$PHP_BINARY migration/migrate_taglib_statements.php
echo "* Migrated declaration and usage of tags ..."

# migrates usage of DI-/ServiceManager
$PHP_BINARY migration/migrate_di_configuration.php
$PHP_BINARY migration/migrate_sm_calls.php
echo "* Migrated service calls and di-service configuration ..."

# migrates usage of registry
$PHP_BINARY migration/migrate_core_registry_calls.php
echo "* Migrated registry calls ..."

# migrates usage of Session(Manager)
$PHP_BINARY migration/migrate_core_session_calls.php
echo "* Migrated session(manager) calls ..."

# add missing use statements
$PHP_BINARY migration/migrate_missing_use_statements.php
echo "* Add missing use statements ..."

# resolve non-namespace'd class calls in singleton/session singleton
$PHP_BINARY migration/migrate_singleton_calls.php
echo "* Migrated Singleton/SessionSingleton calls ..."

# migrates config
echo "* Migrated configuration files:"
$PHP_BINARY migration/migrate_config_calls.php
echo "  * Config calls"
$PHP_BINARY migration/migrate_db_config.php
echo "  * Database configuration"
$PHP_BINARY migration/migrate_cache_configuration.php
echo "  * Cache configuration"
$PHP_BINARY migration/migrate_fc_configuration.php
echo "  * Front controller configuration"
$PHP_BINARY migration/migrate_gorm_configuration.php
echo "  * GORM configuration"
$PHP_BINARY migration/migrate_pager_configuration.php
echo "  * Pager configuration"
$PHP_BINARY migration/migrate_umgt_configuration.php
echo "  * UMGT module configuration"

echo
echo "######################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
