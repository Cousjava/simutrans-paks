#!/bin/bash
echo -e "\033[1;95m \nErstelle addon Objekte\n"
echo "Regenerative_Kraftwerke"
cd ./Regenerative_Kraftwerke
$repo/makeobj PAK128 > "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj MERGE $repo/simutrans/PAK128.german/addons/PAK128.german/Regenerative_Kraftwerke.pak ./*.pak >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
rm -f ./*.pak >> "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
cd ..
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/bonbon.pak ._addons/_nonsens/bonbon > "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/addons/PAK128.german/die_etwas_andere_Fleischfabrik.pak ._addons/_nonsens/die_etwas_andere_Fleischfabrik > "$repo/LOG/addon.log" 2>> "$repo/LOG/error_addon.log"
cd $repo
