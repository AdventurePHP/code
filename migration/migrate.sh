#!/bin/bash

# change to apps-directory 
cd "$1"

# check directory
if [ -d "./core" -a -d "./migration" ]
then
   echo "Directory check OK..."
else
   echo "Wrong directory. Aborting!"
   exit 1
fi

# evaluate php executable path
if [ -n "$2" ] # path to php is given as parameter
then
   if [ ! -x "$2" -o -d "$2" ] # check for exetubale and non-directory
   then
      echo "Given path to PHP is no executable file. Aborting!"
      exit 1
   fi
   echo "Using given php executable at ${2}. PHP Version: `$2 -r "echo phpversion();" 2> /dev/null`. Starting migartion:"
   phpexec="${2}"
else
   php -v > /dev/null 2>&1 # try php in PATH
   if [ $? -eq 127 ] # exit code 127: command not found
   then
      echo "PHP not found in your PATH-scope. Give path to php as second parameter. Aborting!"
      exit 1
   fi
   echo "PHP executable found. Starting migration:"
   phpexec="php"
fi

echo # blank line
sleep 1


# introduces namespaces and migrates import() to use
`$phpexec migration/migrate_import_calls.php`
echo "introduced namespace-declarations..."
echo "migrated import() to use..."
sleep 0.5

# migrates class documentation (PHPDoc comments)
`$phpexec migration/migrate_code_documentation.php`
echo "migrated PHPDoc comments of classes..."
sleep 0.5

# migrates tag-declarations
`$phpexec migration/migrate_document_controller_statements.php`
`$phpexec migration/migrate_taglib_declaration.php`
`$phpexec migration/migrate_taglib_statements.php`
echo "migrated declaration and usage of taglibs..."
sleep 0.5

# migrates usage of DI-/ServiceManager
`$phpexec migration/migrate_di_configuration.php`
`$phpexec migration/migrate_sm_calls.php`
echo "migrated service calls and di-service configuration..."
sleep 0.5

# migrates usage of registry
`$phpexec migration/migrate_core_registry_calls.php`
echo "migrated registry calls ..."
sleep 0.5

# migrates config
echo "configuration migration:"
`$phpexec migration/migrate_config_calls.php`
echo "   ... config calls"
`$phpexec migration/migrate_db_config.php`
echo "   ... database configuration"
`$phpexec migration/migrate_cache_configuration.php`
echo "   ... cache configuration"
`$phpexec migration/migrate_fc_configuration.php`
echo "   ... frontcontroller configuration"
`$phpexec migration/migrate_gorm_configuration.php`
echo "   ... GORM configuration"
`$phpexec migration/migrate_pager_configuration.php`
echo "   ... Pager configuration"
`$phpexec migration/migrate_umgt_configuration.php`
echo "   ... UMGT-modul configuration"
sleep 1


echo # blank line
echo # blank line
echo "Ready!"
exit 0
