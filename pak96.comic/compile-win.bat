@echo off

echo pak96.comic open-source repository compiler for Windows
echo =======================================================
echo.
echo This batch compiles this repository into a new folder
echo called compiled, makeobj.exe must be in root folder.
echo.
echo Checking for makeobj.exe...
echo.
if not exist .\makeobj.exe goto abort

rem Create folder for *.paks or delete all old paks if folder already exists
if exist .\compiled\ (del .\compiled\*.pak) else (md compiled)

echo.
echo -------------------------------------------------------
echo Compiling air transport...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir air /ad /b') do (makeobj pak96 ./compiled/ ./air/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling city and lanscape...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir city-and-landscape /ad /b') do (makeobj pak96 ./compiled/ ./city-and-landscape/%%d/)
for /f "delims=" %%d in ('dir city-and-landscape\buildings /ad /b') do (makeobj pak96 ./compiled/ ./city-and-landscape/buildings/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling factories...
echo -------------------------------------------------------
makeobj pak96 ./compiled/ ./factory/

echo.
echo -------------------------------------------------------
echo Compiling maglevs...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir maglev /ad /b') do (makeobj pak96 ./compiled/ ./maglev/%%d/)
for /f "delims=" %%d in ('dir maglev\ways /ad /b') do (makeobj pak96 ./compiled/ ./maglev/ways/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling monorails...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir monorail /ad /b') do (makeobj pak96 ./compiled/ ./monorail/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling others...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir other /ad /b') do (makeobj pak96 ./compiled/ ./other/%%d/)
for /f "delims=" %%d in ('dir other\powerlines /ad /b') do (makeobj pak96 ./compiled/ ./other/powerlines/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling railroad...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir rail /ad /b') do (makeobj pak96 ./compiled/ ./rail/%%d/)
for /f "delims=" %%d in ('dir rail\ways /ad /b') do (makeobj pak96 ./compiled/ ./rail/ways/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling narrowgauge railroad...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir rail-narrow /ad /b') do (makeobj pak96 ./compiled/ ./rail-narrow/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling road transport...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir road /ad /b') do (makeobj pak96 ./compiled/ ./road/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling trams...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir tram /ad /b') do (makeobj pak96 ./compiled/ ./tram/%%d/)

echo.
echo -------------------------------------------------------
echo Compiling naval transport...
echo -------------------------------------------------------
for /f "delims=" %%d in ('dir water /ad /b') do (makeobj pak96 ./compiled/ ./water/%%d/)
for /f "delims=" %%d in ('dir water\ways /ad /b') do (makeobj pak96 ./compiled/ ./water/ways/%%d/)

echo.
echo =====================
echo Compilation Complete!
echo =====================
echo.

goto end

:abort
echo ERROR: makeobj not found on root folder.

:end
pause
