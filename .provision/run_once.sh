#!/bin/bash

# Add the default web server user to the vagrant group
#sudo usermod -a -G ubuntu www-data

# Force a blank root password for mysql
export DEBIAN_FRONTEND="noninteractive"
export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8
locale-gen en_US.UTF-8

sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password password"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password password"

# Required to access to latest pear packages compatible with php8.8
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update

# Install mysql, nginx, php-fpm, language-pack-ru (for enable locale ru_RU)
sudo apt-get install -y -f mysql-server mysql-client nginx \
      php8.0-fpm php8.0-zip php8.0-mysql php8.0-curl php8.0-intl php8.0-mbstring php8.0-xmlrpc php8.0-xml libhyphen0 unzip nodejs php8.0-xdebug php8.0-gd language-pack-ru

# Required Kohana folders
sudo rm -rf /srv/devstarter.local/cache /srv/devstarter.local/logs
mkdir -p /srv/devstarter.local/cache /srv/devstarter.local/logs

# Nginx virtual host
sudo cp /srv/devstarter.local/.provision/default.vhost /etc/nginx/sites-available/default
sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Proper database credentials
echo 'create database `app`;' | mysql -uroot -ppassword

# Importing current active database
if [[ -f /srv/devstarter.local/.provision/sql/schema.sql ]]; then
    mysql -uroot -ppassword app < /srv/devstarter.local/.provision/sql/schema.sql
fi

# fill test data
if [[ -f /srv/devstarter.local/.provision/sql/dev.sql ]]; then
    mysql -uroot -ppassword app < /srv/devstarter.local/.provision/sql/dev.sql
fi

# Доступ для импорта mysql-файлов
sudo chmod -R 777 /var/lib/mysql-files

sudo sed -i "s/\/srv\/devstarter.local\/www/\/srv\/devstarter.local\/www/g" /etc/nginx/sites-enabled/default

# Proper user
sudo sed -i "s/user www-data;/user vagrant;/g" /etc/nginx/nginx.conf
sudo sed -i "s/user = www-data/user = vagrant/g" /etc/php/8.0/fpm/pool.d/www.conf
sudo sed -i "s/group = www-data/group = vagrant/g" /etc/php/8.0/fpm/pool.d/www.conf

# Proper PHP version for the CLI
sudo update-alternatives --set php /usr/bin/php8.0

# Proper error reporting to browser window
sudo sed -i "s@display_errors = .*@display_errors = On@g" /etc/php/*/fpm/php.ini /etc/php/*/cli/php.ini
sudo sed -i "s@error_reporting = .*@error_reporting = E_ALL@g" /etc/php/*/fpm/php.ini /etc/php/*/cli/php.ini
sudo sed -i "s@post_max_size = 8M@post_max_size = 30M@g" /etc/php/*/fpm/php.ini /etc/php/*/cli/php.ini
sudo sed -i "s@upload_max_filesize = 2M@upload_max_filesize = 30M@g" /etc/php/*/fpm/php.ini /etc/php/*/cli/php.ini

# Installing and configuring phpmyadmin
rm -rf /tmp/phpmyadmin*
wget -q -c https://files.phpmyadmin.net/phpMyAdmin/5.1.0/phpMyAdmin-5.1.0-all-languages.zip -O /tmp/phpmyadmin.zip
unzip -q /tmp/phpmyadmin.zip -d /tmp/phpmyadmin
sudo mv /tmp/phpmyadmin/phpMyAdmin* /srv/phpmyadmin
cat << 'EOF' | sudo tee /srv/phpmyadmin/config.inc.php
<?php
$cfg = array(
    'Servers' => array(
        1 => array(
            'auth_type' => 'config',
            'password' => 'password',
            'hide_db' => '^(information_schema|mysql|performance_schema|sys)$',
            'AllowNoPassword' => TRUE,
        ),
    ),
);
EOF
sudo chown -R vagrant:vagrant /srv/phpmyadmin

# Installing less processor
curl -sL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo npm install -g --no-progress less@4.1.1 less-plugin-autoprefix
sudo npm install uglify-es -g

# Sendmail stub
cat << 'EOF' | sudo tee /usr/bin/sendmail-stub
#!/bin/bash
dir="/srv/devstarter.local/logs/sendmail"
mkdir -p "$dir"
date=`date \+\%Y-\%m-\%d_\%H-\%M`
file="$dir/$date.eml"
while read line
do
  echo "$line" >> $file
done
chmod 666 $file
echo /bin/true
EOF
sudo chmod +x /usr/bin/sendmail-stub
sudo sed -i "s@;sendmail_path =@sendmail_path = \/usr\/bin\/sendmail-stub@g" /etc/php/*/fpm/php.ini /etc/php/*/cli/php.ini
# SwiftMailer uses direct sendmail file, so providing it
sudo rm -rf /usr/sbin/sendmail
sudo ln -s /usr/bin/sendmail-stub /usr/sbin/sendmail

# Cron
#write out current crontab
sudo crontab -l > /tmp/mycron
#echo new cron into cron file
echo "30 * * * *      /srv/devstarter.local/minion.php --task=HalfHourly &> /dev/null" >> /tmp/mycron
echo "* * * * *       /srv/devstarter.local/minion.php --task=Minutely &> /dev/null" >> /tmp/mycron
echo "0 6 * * *       /srv/devstarter.local/minion.php --task=Daily &> /dev/null" >> /tmp/mycron
echo "0 0 1 * *       /srv/devstarter.local/minion.php --task=Monthly &> /dev/null" >> /tmp/mycron
echo "* * * * *       /srv/devstarter.local/minion.php --task=Migrate &> /dev/null" >> /tmp/mycron
#install new cron file
sudo crontab /tmp/mycron
rm /tmp/mycron

# phpUnit / Composer
# https://confluence.jetbrains.com/display/PhpStorm/Running+PHPUnit+tests+over+SSH+on+a+remote+server+with+PhpStorm
sudo curl -L https://phar.phpunit.de/phpunit.phar -o /usr/local/bin/phpunit
sudo chmod +x /usr/local/bin/phpunit
sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# XDebug
cat << 'EOF' | sudo tee /etc/php/8.0/mods-available/xdebug.ini
zend_extension=xdebug.so
xdebug.mode = debug
xdebug.discover_client_host = on
xdebug.idekey = "vagrant"
EOF

# uploads
create_symlink(){
  cd /srv/devstarter.local/www/ || return
  ln -s ../uploads/public /srv/devstarter.local/www/uploads
}
mkdir -p /srv/devstarter.local/uploads/private /srv/devstarter.local/uploads/public
create_symlink

sudo service php8.0-fpm restart
sudo service nginx restart

# Start ssh with the proper dir
echo "cd /srv/devstarter.local" >> /home/vagrant/.bashrc


