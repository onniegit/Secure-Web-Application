REM To quit the server, first close the window for php-cgi
REM When prompted to terminate batch job, select no
ECHO OFF
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
start /wait /min php-cgi.exe -b 127.0.0.1:9000
cd bin\nginx
REM Quits the nginx server after the cgi is closed
nginx -s quit
REM Note that the web application will not work unless the server and cgi processes are running