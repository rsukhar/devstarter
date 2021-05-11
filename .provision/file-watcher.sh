#!/bin/bash
MAIN_L_TIME=`find /srv/devstarter.local/www/assets/less -type f -regextype posix-egrep -regex '.+\/less\/(main|page)(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`
VOF_L_TIME=`find /srv/devstarter.local/www/assets/admin/less -type f -regextype posix-egrep -regex '.+\/less\/vof(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`
ADMIN_L_TIME=`find /srv/devstarter.local/www/assets/admin/less -type f -regextype posix-egrep -regex '.+\/less\/(admin|admin_page)(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`

while true
do
	# main.css
	MAIN_A_TIME=`find /srv/devstarter.local/www/assets/less -type f -regextype posix-egrep -regex '.+\/less\/(main|page)(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`
	if [[ "$MAIN_A_TIME" != "$MAIN_L_TIME" ]]
	then
		MAIN_L_TIME=$MAIN_A_TIME
    sudo lessc --silent --autoprefix=">1%" --no-color /srv/devstarter.local/www/assets/less/main.less /srv/devstarter.local/www/assets/css/main.css > /dev/null 2>>/srv/devstarter.local/logs/error.log
		sudo lessc --silent --autoprefix=">1%" --clean-css --no-color /srv/devstarter.local/www/assets/less/main.less /srv/devstarter.local/www/assets/css/main.min.css > /dev/null 2>>/srv/devstarter.local/logs/error.log
		sleep 0.5
	fi
	# vof.css
	VOF_A_TIME=`find /srv/devstarter.local/www/assets/admin/less -type f -regextype posix-egrep -regex '.+\/less\/vof(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`
	if [[ "$VOF_A_TIME" != "$VOF_L_TIME" ]]
	then
		VOF_L_TIME=$VOF_A_TIME
		sudo lessc --silent --autoprefix=">1%" --no-color /srv/devstarter.local/www/assets/admin/less/vof.less /srv/devstarter.local/www/assets/admin/css/vof.css > /dev/null 2>>/srv/devstarter.local/logs/error.log
		sleep 0.5
	fi
	#admin
	ADMIN_A_TIME=`find /srv/devstarter.local/www/assets/admin/less -type f -regextype posix-egrep -regex '.+\/less\/(admin|admin_page)(\.less|\/.+)$' -printf '%y %T@ \n' | sort -n | tail -1 | cut -f2- -d" "`
	if [[ "$ADMIN_A_TIME" != "$ADMIN_L_TIME" ]]
	then
		ADMIN_L_TIME=$ADMIN_A_TIME
		sudo lessc --silent --autoprefix=">1%" --no-color /srv/devstarter.local/www/assets/admin/less/admin.less /srv/devstarter.local/www/assets/admin/css/admin.css > /dev/null 2>>/srv/devstarter.local/logs/error.log
		sleep 0.5
	fi
done
