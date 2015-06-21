
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
```
useradd -G wheel admin
# visudo : comment the first wheel line and uncomment the second to enable passwordless switch to root
su - admin
mkdir .ssh
echo '
ssh-dss AAAAB3NzaC1kc3MAAACBALiKnp7eI1zL1itp1WmW2VwFA0RzzzBQTZC7c65a4cfPYKk8Luyw4fWR2F3EEmOe9sXwsxPhp+fn7eYoUa2ghOrAbYSf678VMBtcak93OxPnXZs0+ehwvV0k4LPcmAJbqWqyZxGCPFW7V0WTWPCFM7AwTds9/ut0+0zKU1GB24CtAAAAFQC3g3XHWNTzTM55lJOKJ+y1kretgwAAAIBUkHQ8t3f2rERNwC7L+HdJouXwStxsBmBaNAWPeGORLno3AFtc9cZwbm7UtJiW0BTyZChA5AEd9WpYvY5/7kdWLgbAp1ZdgSWpnon6ccMugGJSOpXpzJWDiQa/01+YA5xp/x7zy45u5Qu1Qyxw2cPkI7Yktq/TMdYtohJv2GHrRgAAAIACUJctsRAF/5tGZ5+yHS4UeaG1ozb6nn4QOFXevPTwwvZfraXUz2DxtBNEd2D9mR2x97tqMQYaUDtgtQ1yJfjjSjrEYFnY1B3LDDVpimv4SioJhU2RevL40Vk36+yIBS+nVN+gaGGeaCYPa+6NNl4STeYIXaDrztuSGCnUTpfwmg== Kefah T. Issa
ssh-dss AAAAB3NzaC1kc3MAAACBAOWXOCHHMkp2DYKeqohpWCNziSBoz8BPb1yReFE1CDvKnRwDOUBMTc5RuCQd9yDKQK5y4m/uZ6TeoRxRcVGV1+yMeojh8IiuENZZiAPT3EXsrCXp3OPQmioJjw6ZR5azhIrt//6hrKcCOl5jCC4NNpCfY611oVy7G+XhjtoGx3QdAAAAFQC6KMXWusw51JGX37kIspR8RKMFlQAAAIBEf32QsWBSPfjLYBRomveqkUC5W/9R+aAtCQlC/pckLJRbRZRPEQjB3WAcGrCLuIngTF56Ercoq/sYPQcaB9LY0OM5Jfthy89tZ4SrJbhOcyto7jRWF2UJioXn5/Z8TP1H2MKvXC0AgWDFMoBRDSFm6uICnhff45K5aNRXoZNZwAAAAIEAgcv7kvORRvcwut6fd8WGuWQ2d6rGSUc3FVtGGp9kMG6ZkJD91d7ZPGDKAwzmBH+xMKHGemKxveHNO+w8SukkYj8CVceqOo6mA6X3nAAZZKXvc0PUlxpZcTCPNCVroy6JxWjEdIMggfxqZ6W4YPffDJVOi9lGpoXOjjXOvNmRUpI= enas@localhost.localdomain
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCwTgPwogvQShS3vkpEZfPxtQXol1UDSY+Y7lhKvRv02uCPqSUFJvLI126a8ueB+Jw8AE6HiCmpD/gvvOd+/E+URz+5XkFy2iOZBRZ1qARUTEXgc22FlCGQl5+9Ae+s4ItmyHtBuf6W5C6BX78UbUZFco6UACP4rSOzr0Ouw6dHMeuitqFaBZDomETGv4ZRzPBO69YZPBgrA+UjVwf2zVzhc0Wm8kMQ4XQD5btQ+f/Rva3Wx0jTQqPZ1kJ7ToyUPrhotdbY3WWOnF0o2j8Vv+zrNjfnc1h7IbgarysFT3VKv72irsWh9JZAfQ+lrZ4I5qZ8GHd8cR50NC6inx+X7ZQn mohd.anini@gmail.com
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
```
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
```
useradd io
chmod o+x /home/io
setsebool -P httpd_read_user_content 1
setsebool -P httpd_enable_homedirs 1


su - io
git clone https://github.com/kefahi/sedr.git repo
mkdir -p data logs run/tmp bin
mysql -uroot -pxxx <<EOF
create database sedr character set utf8;
create user 'sedr'@'localhost' identified by 'sedr';
grant all privileges on sedr.* to 'sedr'@'localhost';
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
```
# vim /etc/php.ini
#  expose_php = Off
#  date.timezone = UTC
  
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
http://sedr.io/requirements.php
http://sedr.io

