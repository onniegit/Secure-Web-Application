REM ECHO OFF
REM This starts up the database and the server
cd nginx
REM Starts the nginx server
start nginx
cd ..\..\
REM Sets temporary path for libs
SET PATH=php.exe
REM Populates initial database
php bin/nginx/www/src/startup.php
REM Allows for running php script in nginx
php-cgi.exe -b 127.0.0.1:9000
REM Note that when you close the window, the server will not be able to run php script