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