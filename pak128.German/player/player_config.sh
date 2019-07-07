#!/bin/bash
echo -e "\033[0;95m Erstelle Player Gebaeude"
cd ./air
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/player.log" 2> "$repo/LOG/error_player.log"
cd ../all
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd ../monorail
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd ../rail
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd ../road
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd ../tram
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd ../water
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >> "$repo/LOG/player.log" 2>> "$repo/LOG/error_player.log"
cd $repo