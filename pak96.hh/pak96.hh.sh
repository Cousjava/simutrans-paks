#!/bin/bash

# del old files

rm ./simutrans/pak96.hh/*.pak
#rm ./german_addon/simutrans/pak96.hh/*.pak
#rm ./german_industrien/simutrans/pak96.hh/*.pak


# new files write
#  ------------------------
cd city
# city buildings commercial
cd com
../../makeobj PAK96 ./ ./ >../../err_city.txt

../../makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_0x.pak ./building.COM_*_0?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_1x.pak ./building.COM_*_1?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_2x.pak ./building.COM_*_2?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.COM_xx_3x.pak ./building.COM_*_3?.pak >>../../err_city.txt

rm *.pak

# city buildings industrial
cd ../ind
../../makeobj PAK96 ./ ./ >>../../err_city.txt

../../makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_0x.pak ./building.IND_*_0?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_1x.pak ./building.IND_*_1?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_2x.pak ./building.IND_*_2?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.IND_xx_3x.pak ./building.IND_*_3?.pak >>../../err_city.txt

rm *.pak

# city buildings residental
cd ../res
../../makeobj PAK96 ./ ./ >>../../err_city.txt

../../makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_0x.pak ./building.RES_*_0?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_1x.pak ./building.RES_*_1?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_2x.pak ./building.RES_*_2?.pak >>../../err_city.txt
#../../makeobj MERGE ../../simutrans/pak96.hh/building.RES_xx_3x.pak ./building.RES_*_3?.pak >>../../err_city.txt

rm *.pak

# city townhalls and pedestrians
cd ../extra
../../makeobj PAK96 ./ ./ >>../../err_city.txt

../../makeobj MERGE ../../simutrans/pak96.hh/citycar.citycars.pak ./citycar.*.pak >>../../err_city.txt
../../makeobj MERGE ../../simutrans/pak96.hh/pedestrian.pedestrian.pak ./pedestrian.*.pak >>../../err_city.txt
../../makeobj MERGE ../../simutrans/pak96.hh/building.townhalls.pak ./building.*.pak >>../../err_city.txt

rm *.pak

# monuments
cd ../monument
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_city.txt

# curiosity in city
cd ../../cur/cur-city
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >../../err_cur.txt

# curiosity out of city
cd ../cur-land
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_cur.txt


# ------------------------
cd ../../ground
../makeobj PAK96 ../simutrans/pak96.hh/ ./ >../err_ground.txt
cd ../simutrans/pak96.hh
mv ground.Outside.pak ground.Outside_.pak

# ------------------------
cd ../../other
../makeobj PAK ../simutrans/pak96.hh/ ./ >../err_other.txt
../makeobj PAK96 ../simutrans/pak96.hh/ ./new_cursor.txt >>../err_other.txt
../makeobj PAK96 ../simutrans/pak96.hh/ ./symbols96.txt >>../err_other.txt
../makeobj PAK96 ../simutrans/pak96.hh/ ./MiscImages.txt >>../err_other.txt
# ../makeobj PAK128 ./ ./BigLogo.txt >>../err_other.txt

cd ../simutrans/pak96.hh
../../makeobj MERGE ./symbols.pak ./symbol.*.pak >>../../err_other.txt
rm symbol.*.pak

# ------------------------
cd ../../player
# player special
../makeobj PAK96 ../simutrans/pak96.hh/ ./ >../err_player.txt

# player air
cd air
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player maglev
cd ../maglev
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player monorail
cd ../monorail
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player narrowgauge | unused
cd ../narrowgauge
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player rail
cd ../rail
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player road
cd ../road
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player tram
cd ../tram
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt

# player water
cd ../water
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_player.txt


# ------------------------
cd ../../tree
../makeobj pak96 ../simutrans/pak96.hh/ ./ >../err_tree.txt

# ------------------------
cd ../smoke
../makeobj pak96 ../simutrans/pak96.hh/ ./ >../err_smoke.txt

# ------------------------
cd ../good
./create.sh
cp good.None.pak ../simutrans/pak96.hh
cp good.Passagiere.pak ../simutrans/pak96.hh
cp good.Post.pak ../simutrans/pak96.hh

# ------------------------
cd ../way
# way air
cd air
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >../../err_way.txt

# way maglev
cd ../maglev
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way monorail
cd ../monorail
#../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way narrowgauge
cd ../narrowgauge
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way rail
cd ../rail
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way road
cd ../road
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way tram
cd ../tram
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way water
cd ../water
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# way power ( Catenarys and Powerline/Transformer )
cd ../power
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# signals air
cd ../signale/air
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals maglev
cd ../maglev
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals monorail
cd ../monorail
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals narrowgauge
cd ../narrowgauge
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals rail
cd ../rail
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals road
cd ../road
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals tram
cd ../tram
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# signals water
cd ../water
../../../makeobj PAK96 ../../../simutrans/pak96.hh/ ./ >>../../../err_way.txt

# crossings for all
cd ../../crossing
../../makeobj PAK96 ../../simutrans/pak96.hh/ ./ >>../../err_way.txt

# ------------------------
cd ../../vehicle
../makeobj pak96 ./ ./Engines/ >../err_vehicle.txt
../makeobj pak96 ./ ./Post_Pass/ >>../err_vehicle.txt
../makeobj pak96 ./ ./Monorail/ >>../err_vehicle.txt
../makeobj pak96 ./ ./Maglev/ >>../err_vehicle.txt
../makeobj pak96 ./ ./Narrowgauge/ >>../err_vehicle.txt

# Rail merged

# Tram merged
../../makeobj MERGE ../../simutrans/pak96.hh/vehicle.T13.pak ./vehicle.T13_*.pak >>../err_vehicle.txt
rm vehicle.T13_*.pak

# Road merged

# copy pak file
cd ../simutrans/pak96.hh
cp ../../vehicle/* .

# ------------------------
# mkdir text
cd text
# pause Loeschbefehl
rm *.tab
rm *.txt
cp ../../../pak.text/* .
mv compat.tab ../compat.tab
# ------------------------
cd ..
# mkdir sound
cd sound
rm *.wav
rm *.tab
cp ../../../sound/*.wav .
# ------------------------
cd ..
# mkdir config
cd config
rm *
cp ../../../config/* .
# ------------------------

cd ..
cd ..
cd ..




