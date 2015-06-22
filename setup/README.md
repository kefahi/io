
# Server setup (Fedora 22, 64bit)

## General package update and installation

```bash
dnf update
dnf install mariadb-server nginx php git byobu vim-enhanced php-mysqlnd php-fpm mosh fcgi firewalld fail2ban php-pecl-imagick php-gd php-mbstring php-pecl-apcu php-opcache  php-pecl-redis php-intl php-pecl-zip http://rpms.famillecollet.com/fedora/remi-release-22.rpm
dnf install --enablerepo=remi redis


systemctl enable nginx
systemctl enable redis
systemctl enable php-fpm
systemctl enable firewalld
systemctl enable fail2ban

systemctl enable mariadb
systemctl start mariadb
systemctl start redis

echo '

" ADDED BY KEFAH
colorscheme delek
set is
set ic
set ts=2
set sw=2 ' >> /etc/vimrc
```

## Add admin user to be used for ssh instead of root
```bash
useradd -G wheel admin
# visudo : comment the first wheel line and uncomment the second to enable passwordless switch to root
su - admin
mkdir .ssh
echo '
ssh-dss xxx someone
ssh-rsa yyy anotherone
' >> .ssh/authorized_keys
chmod 600 .ssh/authorized_keys

echo "
alias s='sudo su -'
alias u='sudo dnf update' " >> ~/.bashrc

exit # exit admin and back to root
```

## Improve SSH and security

Disable root or password-based ssh 
Test that you can access admin via ssh and that sudo is working as well before executing the following step.
```bash
# vim /etc/ssh/sshd_config
#   PermitEmptyPasswords no
#   PasswordAuthentication no
#   PermitRootLogin no
#   AllowUsers admin

systemctl restart sshd

systemctl restart firewalld

firewall-cmd --add-service=http --permanent
firewall-cmd --add-service=https --permanent
firewall-cmd --add-port=60000-61000/udp --permanent
firewall-cmd --reload

systemctl restart fail2ban

# Set mariadb root password and create a database
mysqladmin password xxx
```

## Setup main app user and db
```bash
useradd io
chmod o+x /home/io
setsebool -P httpd_read_user_content 1
setsebool -P httpd_enable_homedirs 1


su - io
git clone https://github.com/kefahi/sedr.git repo
mkdir -p data logs run/tmp bin
mysql -uroot -pxxx <<EOF
create database io character set utf8;
create user 'io'@'localhost' identified by 'xxx';
grant all privileges on io.* to 'io'@'localhost';
EOF

curl -sS https://getcomposer.org/installer | php
mv composer.phar bin/composer
chmod a+x bin/composer
cd public
composer global require "fxp/composer-asset-plugin:~1.0.0"
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
ln -s /home/io/repo/basic/web /home/io/public

```

## Web server (nginx/php-fpm)
```bash
# vim /etc/php.ini
#  expose_php = Off
#  date.timezone = UTC
#  cgi.fix_pathinfo=0
  
mv /etc/nginx/nginx.conf /etc/nginx/nginx.conf.off
mv /etc/nginx/conf.d/php-fpm.conf /etc/nginx/conf.d/php-fpm.conf.off
mv /etc/php-fpm.d/www.conf /etc/php-fpm.d/www.conf.off
mv /home/io/repo/setup/nginx/nginx.conf /etc/nginx
mv /home/io/repo/setup/nginx/conf.d/io.conf /etc/nginx/conf.d
mv /home/io/repo/setup/php-fpm.d/io.conf /etc/php-fpm.d

systemctl start php-fpm
systemctl start nginx
```


## Check that the app is up and running
- http://sedr.io/requirements.php
- http://sedr.io

