#!/bin/bash
########################################################################################################################
# APF 2.0 automatic migration script                                                                                   #
########################################################################################################################

echo "######################################"
echo "# APF 2.0 automatic migration        #"
echo "######################################"
echo

# include checks and preparation
source "$(dirname $0)/check.sh"

echo
echo "######################################"
echo
echo "Starting migration ..."

# Run prepare scripts to ease migration
echo "* Prepare addtaglib declarations for migration ..."
$PHP_BINARY migration/prepare_addtaglib_statements.php

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

# cleanup for use statements without namespace
echo "* Clean up unnecessary use statements ..."
$PHP_BINARY migration/cleanup_existing_use_statements.php

# resolve non-namespace'd class calls in singleton/session singleton
$PHP_BINARY migration/migrate_singleton_calls.php
echo "* Migrated Singleton/SessionSingleton calls ..."

# migrate calls to CookieManager now named "Cookie"
$PHP_BINARY migration/migrate_tools_cookie_calls.php
echo "* Migrated CookieManager to Cookie class ..."

# migrate PostHandler to RequestHandler calls
$PHP_BINARY migration/migrate_posthandler.php
echo "* Migrated PostHandler to RequestHandler class ..."

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
$PHP_BINARY migration/migrate_contact_configuration.php
echo "  * Contact module configuration"

echo
echo "######################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
