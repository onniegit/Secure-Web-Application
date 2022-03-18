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
start /min php-cgi.exe -b 127.0.0.1:9000
REM Note that the web application will not work unless the server and cgi processes are running

REM Prompt the user if they want to shut down the server or not
:choice
set /p input="Shut down the web server (Y/N)? "
echo %input%
if /i "%input%" EQU "Y" goto :yes
if /i "%input%" EQU "YES" goto :yes
if /i "%input%" EQU "N" goto :no
if /i "%input%" EQU "NO" goto :no
goto :choice

:yes
cd ..
START cmd /c CALL shutdown.bat

:no
exit