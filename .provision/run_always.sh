#!/bin/bash

# Starting nginx after folder syncing as it fails first when missing logs/error.log file
sudo service nginx start

# Making sure we always have all the needed access
sudo chmod -R 0777 /var/lib/mysql-files/

# Add the default web server user to the vagrant group
#sudo usermod -a -G ubuntu www-data
sudo sed -i "s/user www-data;/user vagrant;/g" /etc/nginx/nginx.conf
sudo sed -i "s/user = www-data/user = vagrant/g" /etc/php/8.0/fpm/pool.d/www.conf
sudo sed -i "s/group = www-data/group = vagrant/g" /etc/php/8.0/fpm/pool.d/www.conf
sudo service nginx restart
sudo service php8.0-fpm restart

# Making sure that we always use the same latest autoprefixer/uglify rules to avoid collisions
sudo apt-get install -y nodejs
sudo npm install -g --no-progress less@4.1.1 less-plugin-autoprefix less-plugin-clean-css
sudo npm remove uglify-js -g
sudo npm install uglify-es -g

# File watchers
# Keeping the file contents here so we could edit it without full provision
sudo chmod +x /srv/devstarter.local/.provision/file-watcher.sh
sudo /srv/devstarter.local/.provision/file-watcher.sh &
