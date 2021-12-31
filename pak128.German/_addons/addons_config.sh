#!/bin/bash
echo -e "\033[1;95m \nErstelle addon Objekte\n"
echo "Regenerative_Kraftwerke"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/Regenerative_Kraftwerke.pak ./Regenerative_Kraftwerke/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
echo "nonsens"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_bonbon.pak ./_nonsens/bonbon/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_die_etwas_andere_Fleischfabrik.pak ./_nonsens/die_etwas_andere_Fleischfabrik/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_hasenfurz.pak ./_nonsens/hasenfurz/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_monsterbike.pak ./_nonsens/monsterbike/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_rubiks_wohncube.pak ./_nonsens/rubiks_wohncube/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK255 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_simulatorbug.pak ./_nonsens/simulatorbug/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_weihnachtsbaum.pak ./_nonsens/weihnachtsbaum/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_wkatapult.pak ./_nonsens/wkatapult/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.nonsens_wmann.pak ./_nonsens/wmann/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
echo "alte_vehicle"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/addon.alte_vehicle.pak ./alte_vehicle_noch_als_addon/ >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
cd $repo
