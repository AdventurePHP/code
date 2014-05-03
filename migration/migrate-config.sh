#!/bin/bash
########################################################################################################################
# APF 2.1 automatic configuration migration script                                                                     #
########################################################################################################################

echo "#############################################"
echo "# APF 2.1 automatic configuration migration #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting configuration migration ..."

echo "* Front controller configuration files ..."
$PHP_BINARY $SCRIPT_DIR/migrate_fc_configuration.php

echo "* Database configuration files ..."
$PHP_BINARY $SCRIPT_DIR/migrate_db_configuration.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your configuration and follow instructions within migration documentation!"

exit 0
