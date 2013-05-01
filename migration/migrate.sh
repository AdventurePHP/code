#!/bin/bash

# Wechselt in den apps-Ordner der Applikation (als Skript-Parameter angeben!!)
cd "$1"

# Führt die Namespace-Deklaration ein und schreibt import() auf use um
php migration/migrate_import_calls.php
echo "Namespace-Deklaration eingeführt..."
echo "import() zu use umgeschrieben..."

# Migriert die vorhandene Klassen-Dokumentation
php migration/migrate_code_documentation.php
echo "Klassen-Dokumentation migriert..."

# Migriert Tag-Deklaration
php migration/migrate_document_controller_statements.php
php migration/migrate_taglib_declaration.php
php migration/migrate_taglib_statements.php
echo "Taglib-Deklarationen und Verwendung migriert..."

# Migriert ServiceManager-/DIServiceManager-Nutzung
php migration/migrate_di_configuration.php
php migration/migrate_sm_calls.php
echo "Service-Manager-Nutzung migriert..."

# Migriert Registry-Nutzung
php migration/migrate_core_registry_calls.php
echo "Registry-Nutzung migriert..."

# Migriert Konfiguration
echo "Migriere Konfiguration:"
php migration/migrate_config_calls.php
echo "   ... Aufrufe"
php migration/migrate_db_config.php
echo "   ... der Datenbank Datenbank"
php migration/migrate_cache_configuration.php
echo "   ... des Cache"
php migration/migrate_fc_configuration.php
echo "   ... des Frontcontroller"
php migration/migrate_gorm_configuration.php
echo "   ... des GORM"
php migration/migrate_pager_configuration.php
echo "   ... des Pagers"
php migration/migrate_umgt_configuration.php
echo "   ... des UMGT-Moduls"

echo "Fertig!"
