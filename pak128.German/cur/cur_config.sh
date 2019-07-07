#!bin/bash
echo -e "\033[0;95mErstelle CUR Objekte"
cd ./cur-city
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >$repo/LOG/cur-city.log 2>>$repo/LOG/error_cur-city.log
$repo/makeobj PAK160 $repo/simutrans/PAK128.german/ ./160/FTHamburg.dat >>$repo/LOG/cur-city.log 2>>$repo/LOG/error_cur-city.log
cd ../cur-land
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ >$repo/LOG/cur-land.log 2>>$repo/LOG/error_cur-land.log
cd $repo
