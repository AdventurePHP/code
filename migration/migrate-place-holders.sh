#!/bin/bash
########################################################################################################################
# APF 3.0 automatic place holder migration script                                                                      #
########################################################################################################################

echo "#############################################"
echo "# APF 3.0 automatic place holder migration  #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting migration ..."

$PHP_BINARY $SCRIPT_DIR/migrate_place_holders_to_extended_syntax.php

echo
echo "#############################################"
echo
echo "Migration done! Place holders are now written in extended templating syntax."

exit 0
