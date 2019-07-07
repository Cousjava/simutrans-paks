echo -e "\033[0;95m Erstelle Smoke"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/smoke.log" 2>> "$repo/LOG/error_smoke.log"
cd $repo