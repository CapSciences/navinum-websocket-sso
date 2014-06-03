#!/bin/bash

rsync -chavzP --stats -e ssh william@servervip2.wpottier.bdx.la:/var/www/vhosts/servervip2/ ./ --exclude=".git" --exclude=".idea" --exclude="app/cache/" --exclude="app/logs/"
