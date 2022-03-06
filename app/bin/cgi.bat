cd ..
REM Allows for running php script in nginx
php-cgi.exe -b 127.0.0.1:9000
REM Note that when you close the window, the server will not be able to run php script
cd bin\nginx
REM Quits the nginx server
nginx -s quit