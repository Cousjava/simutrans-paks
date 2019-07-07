#!/bin/bash
repo="/home/herruntermoser/Subversion/PAK128German"
rm -f $repo/LOG/*.log
echo -e "\033[91m LOG geleert\n"
rm -f ./simutrans/PAK128.german/*.pak > "$repo/LOG/Pakmaker.log" 2> "$repo/LOG/Error_Pakmaker.log"
rm -f ./simutrans/PAK128.german/compat.tab >> "$repo/LOG/Pakmaker.log" 2>> "$repo/LOG/Error_Pakmaker.log"
rm -f ./simutrans/PAK128.german/README/*.html >> "$repo/LOG/Pakmaker.log" 2>> "$repo/LOG/Error_Pakmaker.log"
rm -rf ./simutrans/PAK128.german/scenario/* >> "$repo/LOG/Pakmaker.log" 2>> "$repo/LOG/Error_Pakmaker.log"
rm -f ./simutrans/PAK128.german/README/inc/*.html
rm -f ./simutrans/PAK128.german/README/inc/*.css
rm -f ./simutrans/PAK128.german/README/inc/*.png
echo -e "Ordner Simutrans/PAK128German geleert\033[0m\n"
echo -e "\033[1;36m Kopiere Doku"
cp -f $repo/README/*.html   $repo/simutrans/PAK128.german/README
cp -f $repo/README/inc/*.html $repo/simutrans/PAK128.german/README/inc
cp -f $repo/README/inc/*.png $repo/simutrans/PAK128.german/README/inc
cp -f $repo/README/inc/pak.css $repo/simutrans/PAK128.german/README/inc
echo " Kopiere Scenario"
cp -rf $repo/scenario $repo/simutrans/PAK128.german/
echo "Kopiere Konfigurationsdateien"
cp -f $repo/config/*.tab $repo/simutrans/PAK128.german/config >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
echo "Kopiere Sound"
cd ./sound
cp -f *.wav $repo/simutrans/PAK128.german/sound >> "$repo/LOG/sound.log" 2>> "$repo/LOG/error_sound.log"
cp -f sound.tab $repo/simutrans/PAK128.german/sound >> "$repo/LOG/sound.log" 2>> "$repo/LOG/error_sound.log"
echo -e "Kopiere Pakset Textdateien\033[0m"
cd ../pak.text
cp *.txt $repo/simutrans/PAK128.german/text >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cp compat.tab $repo/simutrans/PAK128.german >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cp de.tab $repo/simutrans/PAK128.german/text >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cp en.tab $repo/simutrans/PAK128.german/text >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cd ../translator
cp ./ja.tab $repo/simutrans/PAK128.german/text >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cp ./pl.tab $repo/simutrans/PAK128.german/text >> "$repo/LOG/paktext.log" 2>> "$repo/LOG/error_paktext.log"
cd $repo
cd ./city
. ./city_config.sh
cd ./cur
. ./cur_config.sh
cd ./factory
. ./factory_config.sh
cd ./good
. ./good_config.sh
cd ./ground
. ./ground_config.sh
cd ./other
. ./other_config.sh
cd ./player
. ./player_config.sh
cd ./smoke
. ./smoke_config.sh
cd ./tree
. ./tree_config.sh
cd ./vehicle
. ./vehicle_config.sh
cd ./way
. ./way_config.sh
if [ ! -f Outside.png ]; then
echo -e "\033[1;92m Kopiere Outside.png"
cp $repo/ground/Outside.png $repo
fi
. ./repository_info.sh
info=$(cat ./Repository_Info.tab | grep -oP 'Revision: \K([0-9]+)')
sed "s/Rev\. [0-9]*/Rev\. $info/" $repo/ground/Outside.dat > ./Outside.dat
$repo/makeobj PAK128 simutrans/PAK128.german/ ./ >>"./LOG/Pakmaker.log" 2>> "./LOG/Error_Pakmaker.log"
echo -e "\033[1;91mPakset komplett!\033[0m"