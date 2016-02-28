#!/bin/bash
########################################################################################################################
# APF 3.2 automatic configuration migration script                                                                     #
########################################################################################################################

echo "#############################################"
echo "# APF 3.2 automatic configuration migration #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

case "$(uname -s)" in
    CYGWIN*) PHP_SCRIPT_DIR=$(cygpath -m $SCRIPT_DIR) ;;
    *) PHP_SCRIPT_DIR=$SCRIPT_DIR ;;
esac

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
