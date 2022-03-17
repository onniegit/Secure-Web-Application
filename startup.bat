ECHO OFF
cd "%~dp0"\app\bin
REM Starts the webserver.
START cmd /c CALL webserv.bat
REM Opens browser and points to the localhost after a couple of seconds.
TIMEOUT /t 2 /nobreak
START https://localhost:44343/public/index.php