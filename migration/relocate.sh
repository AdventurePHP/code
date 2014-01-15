#!/bin/bash
########################################################################################################################
# APF 2.0 automatic namespace/vendor relocation                                                                        #
########################################################################################################################

echo "######################################"
echo "# APF 2.0 automatic migration        #"
echo "######################################"
echo

# include checks and preparation
source "$(dirname $0)/check.sh"

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
      echo
      echo "Source namespace: $SOURCE_NAMESPACE"
      echo "Target namespace: $TARGET_NAMESPACE"
fi

echo
echo "######################################"
echo
echo "Starting relocation ..."

# execute relocation
$PHP_BINARY migration/relocate.php $SOURCE_NAMESPACE $TARGET_NAMESPACE

if [ $? -ne 0 ]
then
      echo
      echo
      echo -e "\e[00;31mRelocating files failed. Please see previous message for details. Aborting!\e[00m"
      exit 1
fi

echo
echo
echo "######################################"
echo
echo "Relocation done! Please check your code and follow instructions within migration documentation!"

exit 0