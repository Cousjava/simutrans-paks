rem del old files

cd simutrans
cd pak96.hh
del *.pak
cd ..\
cd ..\

rem write new files
rem  ------------------------
cd city\extra
..\..\makeobj PAK96 ./ ./ >>..\..\err_city.txt

..\..\makeobj MERGE ../../simutrans/pak96.hh/citycar.citycars.pak ./citycar.*.pak >>..\..\err_city.txt
..\..\makeobj MERGE ../../simutrans/pak96.hh/pedestrian.pedestrian.pak ./pedestrian.*.pak >>..\..\err_city.txt
..\..\makeobj MERGE ../../simutrans/pak96.hh/building.townhalls.pak ./building.*.pak >>..\..\err_city.txt

del *.pak

rem ------------------------
cd ..\res
cd ..\res
..\..\makeobj PAK96 ./ ./ >>..\..\err_city.txt

..\..\makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_0x.pak ./building.RES_*_0?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_1x.pak ./building.RES_*_1?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_2x.pak ./building.RES_*_2?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_3x.pak ./building.RES_*_3?.pak >>..\..\err_city.txt

del *.pak

rem ------------------------
cd ..\com
..\..\makeobj PAK96 ./ ./ >..\..\err_city.txt

..\..\makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_0x.pak ./building.COM_*_0?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_1x.pak ./building.COM_*_1?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_2x.pak ./building.COM_*_2?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_3x.pak ./building.COM_*_3?.pak >>..\..\err_city.txt

del *.pak

rem ------------------------
cd ..\ind
..\..\makeobj PAK96 ./ ./ >>..\..\err_city.txt

..\..\makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_0x.pak ./building.IND_*_0?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_1x.pak ./building.IND_*_1?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_2x.pak ./building.IND_*_2?.pak >>..\..\err_city.txt
rem..\..\makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_3x.pak ./building.IND_*_3?.pak >>..\..\err_city.txt

del *.pak

rem ------------------------
cd ..\monument
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_city.txt

rem ------------------------
cd ..\..\cur\cur-city
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >..\..\err_cur.txt

rem ------------------------
cd ..\cur-land
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_cur.txt

rem ------------------------
cd ..\..\ground
..\makeobj PAK96 ../simutrans/pak96.hh/ ./ >..\err_ground.txt
cd ..\simutrans/pak96.hh
ren ground.Outside.pak ground.Outside_.pak

rem ------------------------
cd ..\..\other
..\makeobj PAK ../simutrans/pak96.hh/ ./ >..\err_other.txt
..\makeobj PAK96 ../simutrans/pak96.hh/ ./new_cursor.txt >>..\err_other.txt
..\makeobj PAK96 ../simutrans/pak96.hh/ ./symbols96.txt >>..\err_other.txt
..\makeobj PAK96 ../simutrans/pak96.hh/ ./MiscImages.txt >>..\err_other.txt
rem ..\makeobj PAK128 ./ ./Logo-2.txt >>..\err_other.txt

cd ../simutrans/pak96.hh
..\..\makeobj MERGE ./symbols.pak ./symbol.*.pak >>..\..\err_other.txt
del symbol.*.pak
cd ..\

rem ------------------------
cd ..\player
rem player special
..\makeobj PAK96 ../simutrans/pak96.hh/ ./ >..\err_player.txt

rem player air
cd air
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player maglev
cd ..\maglev
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player monorail
cd ..\monorail
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player narrowgauge | unused
cd ..\narrowgauge
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player rail
cd ..\rail
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player road
cd ..\road
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player tram
cd ..\tram
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem player water
cd ..\water
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_player.txt

rem ------------------------
cd ..\..\tree
..\makeobj pak96 ../simutrans/pak96.hh/ ./ >..\err_tree.txt

rem ------------------------
cd ..\smoke
..\makeobj pak96 ../simutrans/pak96.hh/ ./ >..\err_smoke.txt

rem ------------------------
cd ..\good
call create.bat
move good.None.pak ..\simutrans\pak96.hh
move good.Passagiere.pak ..\simutrans\pak96.hh
move good.Post.pak ..\simutrans\pak96.hh

rem ------------------------
cd ..\way
rem way air
cd air
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >..\..\err_way.txt

rem way maglev
cd ..\maglev
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way monorail
cd ..\monorail
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way narrowgauge
cd ..\narrowgauge
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way rail
cd ..\rail
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way road
cd ..\road
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way tram
cd ..\tram
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem way water
cd ..\water
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_city.txt

rem way power ( Catenarys and Powerline/Transformer )
cd ..\power
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_city.txt

rem signals air
cd ..\signale/air
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals maglev
cd ..\maglev
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals monorail
cd ..\monorail
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals narrowgauge
cd ..\narrowgauge
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals rail
cd ..\rail
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals road
cd ..\road
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals tram
cd ..\tram
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem signals water
cd ..\water
..\..\..\makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>..\..\..\err_way.txt

rem crossings for all
cd ..\..\crossing
..\..\makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>..\..\err_way.txt

rem ------------------------
cd ..\..\vehicle
..\makeobj PAK96 ./ ./Engines/ >..\err_vehicle.txt
..\makeobj PAK96 ./ ./Post_Pass/ >>..\err_vehicle.txt
..\makeobj PAK96 ./ ./Monorail/ >>..\err_vehicle.txt
..\makeobj PAK96 ./ ./Maglev/ >>..\err_vehicle.txt
..\makeobj PAK96 ./ ./Narrowgauge/ >>..\err_vehicle.txt

rem Rail merged

rem Tram merged
..\makeobj MERGE ../simutrans/pak96.hh/vehicle.T13.pak ./vehicle.T13_*.pak >>..\err_vehicle.txt
del vehicle.T13_*.pak

rem Road merged

rem copy pak file
cd ..\simutrans\pak96.hh
copy ..\..\vehicle\*.* .

rem ------------------------
rem mkdir text
cd text
del *.tab
del *.txt
copy ..\..\..\pak.text\*.tab .
copy ..\..\..\pak.text\*.txt .
move compat.tab ..\compat.tab
rem ------------------------
cd ..\
rem mkdir sound
cd sound
del *.wav
del *.tab
copy ..\..\..\sound\*.* .
rem ------------------------
cd ..\
rem mkdir config
cd config
copy ..\..\..\config\*.* .
rem ------------------------

cd ..\..\..\


