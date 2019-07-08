echo -e "\033[0;95mErstelle Ground"

$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/ground.log" 2> "$repo/LOG/error_ground.log"
cd $repo
