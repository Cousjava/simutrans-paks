#!/bin/bash
echo -e "\033[0;95mErstelle Waren"
$repo/makeobj PAK $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/goods.log" 2> "$repo/LOG/error_goods.log"
cd $repo