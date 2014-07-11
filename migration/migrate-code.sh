#!/bin/bash
########################################################################################################################
# APF 3.0 automatic code migration script                                                                              #
########################################################################################################################

echo "#############################################"
echo "# APF 3.0 automatic code migration          #"
echo "#############################################"
echo

SCRIPT_DIR=$(dirname $0)

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

echo
echo "#############################################"
echo
echo "Starting migration ..."

# migrate tag lib declaration
echo "* Migrate taglib declaration statements ..."
$PHP_BINARY $SCRIPT_DIR/migrate_taglib_registration.php

echo "* Consolidate tag usage ..."
$PHP_BINARY $SCRIPT_DIR/migrate_consolidate_tag_usage.php

echo "* Remove redundant tag lib registration ..."
$PHP_BINARY $SCRIPT_DIR/migrate_extract_add_tag_statements.php

echo "* Switch to new place holder logic ..."
$PHP_BINARY $SCRIPT_DIR/migrate_update_place_holders_to_expression_notation.php

echo "* Migrate iterator item place holders ..."
$PHP_BINARY $SCRIPT_DIR/migrate_iterator_place_holders.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
