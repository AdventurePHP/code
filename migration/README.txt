PLEASE NOTE: In order to migrate your APF project to the most recent version update to the before version first.

MIGRATION can be done as follows using a LINUX-like shell (use cygwin for Windows boxes):

$ cd /path/to/your/project/APF
$ ./migration/migrate-code.sh /path/to/php
$ ./migration/migrate-config.sh /path/to/php
$ ./migration/migrate-place-holders.sh /path/to/php

AFTER automatic migration please refer to the manual migration steps described on the main page.
