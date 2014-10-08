########################################################################################################################
# APF 3.0 automatic migration check routine                                                                            #
########################################################################################################################

# evaluate php executable path
echo -n "Checking PHP executable available ... "
if [ -n "$1" ] # path to php is given as parameter
then
   if [ ! -x "$1" -o -d "$1" ] # check for executable and non-directory
   then
      echo "\e[00;32mGiven path to PHP is no executable file. Aborting!\e[00m"
      exit 1
   fi
   echo -e "\e[00;32m[OK]\e[00m"
   echo
   echo "Using given php executable at $1. PHP Version: $($1 -r "echo phpversion();" 2> /dev/null)."
   PHP_BINARY="$1"
else
   php -v > /dev/null 2>&1 # try php in PATH
   if [ $? -eq 127 ] # exit code 127: command not found
   then
      echo -e "\e[00;31m[ERROR]\e[00m"
      echo
      echo -e "\e[00;31mPHP not found in your PATH-scope. Provide path to php as first parameter. Aborting!\e[00m"
      exit 1
   fi
   echo "[OK]"
   echo "PHP executable found."
   PHP_BINARY="php"
fi