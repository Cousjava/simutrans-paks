echo -e "\033[0;95m Erstelle Baeume"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/tree.log" 2>> "$repo/LOG/error_tree.log"
echo "Erstelle Baeume_Gross"
cd ../tree_gross
$repo/makeobj PAK192 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/tree_gross.log" 2>> "$repo/LOG/error_tree.log"
cd $repo