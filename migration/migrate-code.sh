#!/bin/bash
########################################################################################################################
# APF 3.3 automatic code migration script                                                                              #
########################################################################################################################

echo "#############################################"
echo "# APF 3.3 automatic code migration          #"
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

# migrate API for form-to-model mapping
echo "* Migrate form-to-model-mapping API..."
$PHP_BINARY $PHP_SCRIPT_DIR/migrate_form_to_model_mapping.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
