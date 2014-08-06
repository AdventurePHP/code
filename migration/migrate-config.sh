#!/bin/bash
########################################################################################################################
# APF 3.0 automatic configuration migration script                                                                     #
########################################################################################################################

echo "#############################################"
echo "# APF 3.0 automatic configuration migration #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting configuration migration ..."

echo "* Migrate cache configuration ..."
$PHP_BINARY $SCRIPT_DIR/migrate_cache_config.php

echo "* Migrate pager configuration ..."
$PHP_BINARY $SCRIPT_DIR/migrate_pager_config.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your configuration and follow instructions within migration documentation!"

exit 0
