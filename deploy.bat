@echo off
cd C:\xampp
start xampp-control.exe
timeout /t 5 /nobreak >nul

rem Iniciar Apache
call apache\bin\httpd.exe -k start

rem Iniciar MySQL
call mysql\bin\mysqld.exe

timeout /t 5 /nobreak >nul

rem Iniciar servidor Symfony
cd C:\ruta\de\tu\proyecto
symfony server:start
