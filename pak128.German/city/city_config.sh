#!/bin/bash
echo -e "\033[1;95m \nErstelle City Objekte\n"
echo -e "\033[0;92m City Cars"
cd ./city_cars
$repo/makeobj PAK128 > "$repo/LOG/citycars.log" 2>> "$repo/LOG/error_ccars.log"
$repo/makeobj MERGE $repo/simutrans/PAK128.german/citycar.all.pak ./citycar.*.pak >> "$repo/LOG/citycars.log" 2>> "$repo/LOG/error_citycars.log"
rm -f ./citycar.*.pak >> "$repo/LOG/citycars.log" 2>> "$repo/LOG/error_citycars.log"
cd ../com
$repo/makeobj PAK128 > "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_ALLCLT.pak ./building.COM_ALL_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_ALPIN.pak ./building.COM_ALP_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_ALPVOR.pak ./building.COM_AVL_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_MITGEB.pak ./building.COM_MGB_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_OSTDTL.pak ./building.COM_ODL_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
$repo/makeobj MERGE building.COM_NORDTE.pak ./building.COM_NDT_*.pak >> "$repo/LOG/building_com.log" 2>> "$repo/LOG/error_com.log"
mv building.COM_ALLCLT.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.COM_ALPIN.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.COM_ALPVOR.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.COM_MITGEB.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.COM_OSTDTL.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.COM_NORDTE.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm -f ./building.COM_*.pak
echo "City Extra"
cd ../extra
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/building_extra.log" 2>> "$repo/LOG/error_extra.log"
echo "City Industrie"
cd ../ind
$repo/makeobj PAK128 > "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_ALLCLT.pak ./building.IND_ALL_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_ALPIN.pak ./building.IND_ALP_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_ALPVOR.pak ./building.IND_AVL_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_MITGEB.pak ./building.IND_MGB_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_OSTDTL.pak ./building.IND_ODL_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
$repo/makeobj MERGE building.IND_NORDTE.pak ./building.IND_NDT_*.pak >> "$repo/LOG/building_ind.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_ALLCLT.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_ALPIN.pak $repo/simutrans/PAK128.german  >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_ALPVOR.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_MITGEB.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_OSTDTL.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
mv building.IND_NORDTE.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
rm -f ./building.IND_*.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
echo "City Monumente"
cd ../monument
$repo/makeobj PAK128 $repo/simutrans/PAK128.german/ ./ > "$repo/LOG/building_monument.log" 2>> "$repo/LOG/error_monument.log"
echo "City Wohnhaeuser"
cd ../res
$repo/makeobj PAK128 >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_ALLCLT.pak ./building.RES_ALL_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_ALPIN.pak ./building.RES_ALP_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_ALPVOR.pak ./building.RES_AVL_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_MITGEB.pak ./building.RES_MGB_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_OSTDTL.pak ./building.RES_ODL_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
$repo/makeobj MERGE building.RES_NORDTE.pak ./building.RES_NDT_*.pak >> "$repo/LOG/building_res.log" 2>> "$repo/LOG/error_res.log"
mv building.RES_ALLCLT.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.RES_ALPIN.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.RES_ALPVOR.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.RES_MITGEB.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.RES_OSTDTL.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv building.RES_NORDTE.pak $repo/simutrans/PAK128.german >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm ./building.RES_*.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
echo "Erstelle Pedestrians"
cd ../pedestrians
$repo/makeobj PAK128 > "$repo/LOG/pedestrians.log" 2>> "$repo/LOG/error_pedestrians.log"
$repo/makeobj MERGE $repo/simutrans/PAK128.german/pedestrian.all.pak ./pedestrian.*.pak >> "$repo/LOG/pedestrians.log" 2>> "$repo/LOG/error_pedestrians.log"
rm -f ./pedestrian.*.pak >> "$repo/LOG/pedestrians.log" 2>> "$repo/LOG/error_pedestrians.log"
cd ../mehrkachelhaus
$repo/makeobj PAK128 > "$repo/LOG/mehrkachelhaus.log" 2>> "$repo/LOG/error_mehrkachelhaus.log"
$repo/makeobj MERGE $repo/simutrans/PAK128.german/building.Mehrkachelhaus.pak ./building.*.pak >> "$repo/LOG/mehrkachelhaus.log" 2>> "$repo/LOG/error_mehrkachelhaus.log"
rm -f ./building.*.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_ind.log"
cd ../Clusterhaus
$repo/makeobj PAK128 > "$repo/LOG/Clusterhaus.log" 2>> "$repo/LOG/error_Clusterhaus.log"
$repo/makeobj MERGE $repo/simutrans/PAK128.german/building.Clusterhaus.pak ./building.*.pak >> "$repo/LOG/Clusterhaus.log" 2>> "$repo/LOG/error_Clusterhaus.log"
rm ./building.*.pak >> "$repo/LOG/Clusterhaus.log" 2>> "$repo/LOG/error_Clusterhaus.log"
cd $repo
