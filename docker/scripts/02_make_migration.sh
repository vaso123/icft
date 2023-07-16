#!/bin/bash

docker exec -it php /bin/bash -c "/var/www/html/bin/console  make:migration"