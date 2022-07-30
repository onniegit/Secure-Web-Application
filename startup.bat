ECHO OFF
cd "%~dp0"\app\bin
REM Starts the webserver and initialze the DB.
START cmd /c CALL webserv.bat
REM Opens browser and points to the localhost after a couple of seconds.
REM Port 44343 is used for secure http.
TIMEOUT /t 2

REM display the login page
START https://localhost:44343/public/LoginForm.php