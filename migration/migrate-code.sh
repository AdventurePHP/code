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

echo "* Remove RequestHandler usage ..."
$PHP_BINARY $SCRIPT_DIR/migrate_remove_request_handler.php

echo "* Remove HeaderManager usage ..."
$PHP_BINARY $SCRIPT_DIR/migrate_remove_header_manager.php

echo "* Remove APF\tools\cookie\Cookie usage ..."
$PHP_BINARY $SCRIPT_DIR/migrate_remove_tools_cookie.php

echo "* Migrate session handling to new API ..."
$PHP_BINARY $SCRIPT_DIR/migrate_session_handling.php

echo "* Migrate form definitions ..."
$PHP_BINARY $SCRIPT_DIR/migrate_form_definitions.php

echo "* Migrate service object API ..."
$PHP_BINARY $SCRIPT_DIR/migrate_service_object_api.php

echo
echo "#############################################"
echo
echo "Migration done! Please check your code and follow instructions within migration documentation!"

exit 0
