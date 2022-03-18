ECHO OFF
REM Quits the php-cgi process and shuts the server down
cd "%~dp0"\app\bin\nginx
taskkill /IM php-cgi.exe /F
nginx -s quit
REM Note that this is a graceful shutdown and the process does not terminate immediately