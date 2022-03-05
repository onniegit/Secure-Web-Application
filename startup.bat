cd "%~dp0"\app\bin
REM Starts the webserver.
START cmd /k CALL webserv.bat
START cmd /k CALL cgi.bat
REM Opens browser and points to the localhost after a couple of seconds.
SET WAIT_TIME=2
START https://localhost:443/public/index.php