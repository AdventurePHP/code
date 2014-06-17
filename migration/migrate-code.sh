#!/bin/bash
########################################################################################################################
# APF 2.2 automatic code migration script                                                                              #
########################################################################################################################

echo "#############################################"
echo "# APF 2.2 automatic code migration          #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting migration ..."

echo
echo "#############################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
