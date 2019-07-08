echo -e "\033[0;95m Erstelle Signale, Strassen und Wege\033[0m"
cd ./air
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../crossing
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../fence
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../monorail/schwebebahn
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../../rail
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../road
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../signale/air
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../monorail
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../rail
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../road
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../../strom
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd Oberleitung
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../../
cd ./tram
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd ../water
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/way.log" 2>> "$repo/LOG/error_way.log"
cd $repo