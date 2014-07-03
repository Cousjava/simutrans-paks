@echo off

rem Updated 04/12/2012 by HDomos and Fabio Gonella
rem Last update 09/11/2013 by Fabio Gonella
rem Edited for pak128.CS 30/06/2014 by Miziiik

echo Compile Pak128.CS
echo ==============
echo.
echo This batch compiles to folder simutrans/pak128.CS
echo It requires the file makeobj.exe to be in the same
echo folder as this file pak128CS.bat.
echo.
if not exist .\makeobj.exe goto abort

rem delete old
rem  ------------------------
del pak128.CS.zip
cd simutrans\pak128.CS
if errorlevel 1 goto skip_delete
rem if folder does not exist, skip deleting old data
echo removing old data
del *.pak
del config\*.tab
del text\*.tab
del text\*.txt
del text\*.zip
del doc\*.txt
del sound\*.wav
del sound\*.tab
rmdir /Q /S scenario
cd..
cd..
:skip_delete

rem copy config & translation

rem  ------------------------
xcopy /E pak128.CS.prototype\*.* simutrans\pak128.CS\
rem for newer Windows versions can be added /EXCLUDE:svn

rem new writing
rem  ------------------------
cd base
..\makeobj.exe pak128 ../simutrans/pak128.CS/ ./ >..\err.txt

cd .\misc_GUI
..\..\makeobj.exe pak128 >..\..\err.txt
rem symbol.biglogo.pak must stay a single file - so it is copied into the pak folder before the others are moved and merged
copy symbol.biglogo.pak ..\..\simutrans\pak128.CS
del symbol.biglogo.pak
copy symbol.*.pak ..\misc_GUI_64
del symbol.*.pak
copy *.pak ..\..\simutrans\pak128.CS\*.*
del *.pak

cd ..\misc_GUI_64
..\..\makeobj.exe pak >>..\..\err.txt
..\..\makeobj.exe merge symbol.all.pak symbol.*.pak >>..\..\err.txt
copy symbol.all.pak ..\..\simutrans\pak128.CS\*.*
del symbol.*.pak
copy *.pak ..\..\simutrans\pak128.CS\*.*
del *.pak

echo Compiling Pedestrians

cd ..\pedestrians
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/pedestrian.all.pak ./ >>..\..\err.txt

echo Compiling Smokes

cd ..\smokes
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/smokes.all.pak ./ >>..\..\err.txt

echo Compiling Airport Tools

cd ..\..\infrastructure\airport_buildings_towers
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/airports.buildings.pak ./ >>..\..\err.txt

cd ..\airport_depots
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/airports.depots.pak ./ >>..\..\err.txt

cd ..\airport_ways_items
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/airports.misc.pak ./ >>..\..\err.txt

echo Compiling Catenaries

cd ..\catenary_all
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/catenary.all.pak ./ >>..\..\err.txt

echo Compiling Crossings

cd ..\road_rail_crossings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.crossing.road_rail.pak ./ >>..\..\err.txt

cd ..\road_ng_crossings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.crossing.road_ng.pak ./ >>..\..\err.txt

cd ..\road_water_crossings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.crossing.road_water.pak ./ >>..\..\err.txt

cd ..\rail_water_crossings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.crossing.rail_water.pak ./ >>..\..\err.txt

cd ..\ng_water_crossings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.crossing.ng_water.pak ./ >>..\..\err.txt

echo Compiling Depots

cd ..\depots_rail_road_tram_ng
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/depots.some.pak ./ >>..\..\err.txt

echo Compiling Headquarters

cd ..\headquarters
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.hq.all.pak ./ >>..\..\err.txt

echo Compiling Powerlines

cd ..\powerlines
..\..\makeobj.exe pak176 ../../simutrans/pak128.CS/powerlines.all.pak ./ >>..\..\err.txt

echo Compiling Rail tools

cd ..\rail_bridges
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.rail_bridges.all.pak ./ >>..\..\err.txt

cd ..\rail_signals
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/rail_signals.all.pak ./ >>..\..\err.txt

cd ..\rail_stations
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/rail_station.all.pak ./ >>..\..\err.txt

cd ..\rail_tracks
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.rail_track.all.pak ./ >>..\..\err.txt

cd ..\rail_tunnels
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.rail_tunnels.all.pak ./ >>..\..\err.txt

echo Compiling Narrow Gauge tools

cd ..\ng_bridges
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.ng_bridges.all.pak ./ >>..\..\err.txt

cd ..\ng_signals
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ng_signals.all.pak ./ >>..\..\err.txt

cd ..\ng_stations
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ng_station.all.pak ./ >>..\..\err.txt

cd ..\ng_tracks
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.ng_track.all.pak ./ >>..\..\err.txt

cd ..\ng_tunnels
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.ng_tunnels.all.pak ./ >>..\..\err.txt

echo Compiling Road Tools

cd ..\road_bridges
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.road_bridges.all.pak ./ >>..\..\err.txt

cd ..\road_signs
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/road_signs.all.pak ./ >>..\..\err.txt

cd ..\road_stops
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.road_stop.all.pak ./ >>..\..\err.txt

cd ..\road_tunnels
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.road_tunnels.all.pak ./ >>..\..\err.txt

cd ..\roads
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.road.all.pak ./ >>..\..\err.txt

echo Compiling Station Buildings

cd ..\station_buildings
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ext_buildings.pak ./ >>..\..\err.txt

echo Compiling Tram tools

cd ..\tram_tracks
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/way.tram_track.all.pak ./ >>..\..\err.txt

echo Compiling Water Tools

cd ..\water_all
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/water_buildings.all.pak ./ >>..\..\err.txt

echo Compiling Citycars

cd ..\..\citycars
..\makeobj.exe pak128 ../simutrans/pak128.CS/citycar.all.pak ./ >>..\err.txt

echo Compiling Cityhouses

cd ..\cityhouses\com
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\ind
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\res
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling factories

cd ..\..\..\factories
..\makeobj.exe pak128 ../simutrans/pak128.CS/ ./ >>..\err.txt

cd ..\factories\powerplants
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling landscape

cd ..\..\landscape\groundobj_static
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/groundobj.all.pak ./ >>..\err.txt

cd ..\grounds
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ground.all.pak ./ >..\..\err.txt

cd ..\rivers
..\..\makeobj.exe pak160 ../../simutrans/pak128.CS/rivers.all.pak ./ >>..\err.txt

cd ..\trees
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/trees.all.pak ./ >>..\err.txt

echo Compiling special buildings

cd ..\..\special_buildings\city
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.special.city.pak ./ >>..\err.txt

cd ..\landscape
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.special.landscape.pak ./ >>..\err.txt

cd ..\monuments
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.special.monuments.pak ./ >>..\err.txt

cd ..\townhalls
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/building.special.townhalls.pak ./ >>..\err.txt

echo Compiling Airplanes

cd ..\..\vehicles\airplanes
..\..\makeobj.exe pak176 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling Rail vehicles

cd ..\rail-cargo
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\rail-engines
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\rail-psg+mail
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling Narrow Gauge vehicles

cd ..\ng-cargo
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\ng-engines
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\ng-psg+mail
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling Road Vehicles

cd ..\road-cargo
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\road-psg+mail
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling Ships

cd ..\ships-cargo
..\..\makeobj.exe pak250 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd ..\ships-ferries
..\..\makeobj.exe pak250 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

echo Compiling Trams

cd ..\trams
..\..\makeobj.exe pak128 ../../simutrans/pak128.CS/ ./ >>..\..\err.txt

cd..
cd..

echo DONE
goto end

:abort
echo ERROR: makeobj.exe was not found in current folder.
pause

:end
