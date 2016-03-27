#!/bin/bash
########################################################################################################################
# APF 3.2 automatic code migration script                                                                              #
########################################################################################################################

echo "#############################################"
echo "# APF 3.2 automatic code migration          #"
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
echo "Starting migration ..."

# migrate place holder methods
echo "* Migrate place holder methods ..."
$PHP_BINARY $PHP_SCRIPT_DIR/migrate_place_holder_methods.php

# migrate registerAction() methods
echo "* Rewrite deprecated registerAction() to addAction() ..."
$PHP_BINARY $PHP_SCRIPT_DIR/migrate_register_action.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
