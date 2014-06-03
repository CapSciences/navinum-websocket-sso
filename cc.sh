#!/bin/bash
php app/console cache:clear --env=prod
php app/console cache:clear --env=dev
chmod -R 777 app/cache app/logs
