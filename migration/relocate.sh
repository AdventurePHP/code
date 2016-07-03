#!/bin/bash
########################################################################################################################
# APF 3.3 automatic namespace/vendor relocation                                                                        #
########################################################################################################################

echo "#################################################"
echo "# APF 3.3 automatic namespace/vendor relocation #"
echo "#################################################"
echo

SCRIPT_DIR=$(dirname $0)

case "$(uname -s)" in
    CYGWIN*) PHP_SCRIPT_DIR=$(cygpath -m $SCRIPT_DIR) ;;
    *) PHP_SCRIPT_DIR=$SCRIPT_DIR ;;
esac

# include checks and preparation
source "$SCRIPT_DIR/check.sh"

# add extra check for argument 2 (from namespace) and 3 (to namespace)
echo
echo -n "Checking necessary parameters available ... "
if [ -z "$2" ] || [ -z "$3" ]
then
      echo -e "\e[00;31m[ERROR]\e[00m"
      echo
      echo -e "\e[00;31mSource and/or target namespace missing. Provide source namespace as second and target namespace as third parameter. Aborting!\e[00m"
      exit 1
else
      # original namespace to relocate
      SOURCE_NAMESPACE=$2

      # namespace (including vendor) to switch to
      TARGET_NAMESPACE=$3

      echo -e "\e[00;32m[OK]\e[00m"
fi

echo
echo "######################################"
echo
echo "Starting relocation ..."

# relocate files in file system
$PHP_BINARY $PHP_SCRIPT_DIR/relocate.php $SOURCE_NAMESPACE $TARGET_NAMESPACE

if [ $? -ne 0 ]
then
      echo
      echo
      echo -e "\e[00;31mRelocating files failed. Please see previous message for details. Aborting!\e[00m"
      exit 1
fi

echo
echo "######################################"
echo
echo "Relocation done! Please check your code and follow instructions within migration documentation!"

exit 0
