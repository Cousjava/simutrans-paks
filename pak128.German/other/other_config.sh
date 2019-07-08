#!/bin/bash
echo -e "\033[0;95mErstelle Symbols, Cursors, Menu und Misc"

$repo/makeobj PAK ./ ./ > "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"
$repo/makeobj PAK128 ./ ./new_cursor.txt >> "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"
$repo/makeobj PAK128 ./ ./MiscImages.txt >> "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./Logo-2.txt >> "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"

$repo/makeobj MERGE ./symbol.pak ./symbol.*.pak >> "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"
mv symbol.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_other.log"
$repo/makeobj MERGE ./misc.pak ./misc.*.pak >> "$repo/LOG/other.log" 2>> "$repo/LOG/error_other.log"
mv misc.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_other.log"
mv cursor.*.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_other.log"
mv menu.*.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_other.log"
rm -f ./*.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_other.log"
cd $repo