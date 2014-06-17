#!/bin/bash
########################################################################################################################
# APF 2.1 automatic configuration migration script                                                                     #
########################################################################################################################

echo "#############################################"
echo "# APF 2.11 automatic configuration migration #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting configuration migration ..."

echo
echo "#############################################"
echo
echo "Migration done! Please check your configuration and follow instructions within migration documentation!"

exit 0
