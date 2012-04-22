#!/bin/sh
cd ~/git/anontune
git pull
rsync -avz --exclude cphp/config.mysql.php --exclude .git ./public_html/ /var/www/
echo "Done deploying AnonTune."
