echo -e "\033[0;95m Erstelle Fahrzeuge\n"
echo -e "\033[0;92m Erstelle Vehicle/Air"
cd ./air/catg_01
$repo/makeobj PAK255 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../catg_04
$repo/makeobj PAK255 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../Passagiere
$repo/makeobj PAK255 $repo/vehicle/ ./ > "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../Passagiere_alt
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../Post
$repo/makeobj PAK255 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../Post_alt
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../Vieh
$repo/makeobj PAK255 $repo/vehicle/ ./ >> "$repo/LOG/air.log" 2>> "$repo/LOG/error_air.log"
cd ../../
echo "Erstelle Vehicle/Monorail"
cd ./monorail
$repo/makeobj PAK128 $repo/vehicle/ ./ > "$repo/LOG/monorail.log" 2> "$repo/LOG/error_monorail.log"
cd ../
echo "Erstelle Vehicle/Rail"
cd ./rail/Autos
$repo/makeobj PAK128 $repo/vehicle/ ./ > "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_01
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_02
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_03
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_04
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_05
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_06
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_07
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../catg_08
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../Lokomotiven/Dampf
$repo/makeobj PAK128 >> "$repo/LOG/dampflokomotiven.log" 2>> "$repo/LOG/error_dampfloks.log"
$repo/makeobj MERGE ./vehicle.BZT_prP8.pak ./vehicle.P8.pak ./vehicle.P8Tender.pak >> "$repo/LOG/dampflokomotiven.log" 2>> "$repo/LOG/error_dampfloks.log"
rm -f vehicle.P8.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm -f vehicle.P8Tender.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
$repo/makeobj MERGE ./vehicle.BZT_prG8.pak ./vehicle.G8.pak ./vehicle.PrG8Tender.pak >> "$repo/LOG/dampflokomotiven.log" 2>> "$repo/LOG/error_dampfloks.log"
rm -f vehicle.G8.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm -f vehicle.PrG8Tender.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Diesel
$repo/makeobj PAK128 >> "$repo/LOG/dieseloks.log" 2>> "$repo/LOG/error_dieseloks.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd_dieseloks.log" 2>> "$repo/LOG/error_dieseloks.log"
cd ../Elek
$repo/makeobj PAK128 >> "$repo/LOG/e-loks.log" 2>> "$repo/LOG/error_e-loks.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../../
cd Passagiere
$repo/makeobj PAK128 >> "$repo/LOG/passagierwaggons.log" 2>> "$repo/LOG/error_passagierwaggons.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Post
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/rail.log" 2>> "$repo/LOG/error_rail.log"
cd ../Stahl
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/raillog_allgemein.log" 2>> "$repo/LOG/error_rail_allgemein.log"
cd ../Triebwagen/Akku
$repo/makeobj PAK128 >> "$repo/LOG/triebwagenakku.log" 2>> "$repo/LOG/error_dieseloks.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Dampf
$repo/makeobj PAK128 >> "$repo/LOG/triebwagendampf.log" 2>> "$repo/LOG/error_dieseloks.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Diesel
$repo/makeobj PAK128 >> "$repo/LOG/triebwagendiesel.log" 2>> "$repo/LOG/error_triebwagendiesel.log"
$repo/makeobj MERGE vehicle.Integral_all.pak ./vehicle.IntegBOB*.pak >> "$repo/LOG/triebwagendiesel.log" 2>> "$repo/LOG/error_triebwagendiesel.log"
mv vehicle.Integral_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm -f vehicle.IntegBOB*.pak >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Elek
$repo/makeobj PAK128 >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.ICE_1_all.pak ./vehicle.ICE1*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.ICE_3_all.pak ./vehicle.ICE3*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.Flirt_all.pak ./vehicle.FLIRT*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.BR_477_all.pak ./vehicle.BR477_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.BR_480_all.pak ./vehicle.BR480_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.BR_481_all.pak ./vehicle.BR481_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.BR_485rot_all.pak ./vehicle.BR485_rot*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.BR485_rot_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.BR_485_all.pak ./vehicle.BR485_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
$repo/makeobj MERGE vehicle.ET_165_all.pak ./vehicle.ET165_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
mv vehicle.BR_477_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BR_480_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BR_481_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BR_485_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BR_485rot_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.ET_165_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.ICE_1_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.Flirt_all.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.ET25A.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BZT_ET25B.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
mv vehicle.BZT_ET25C.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
rm -f ./vehicle.ICE1*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.ICE3*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.FLIRT*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.BR477_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.BR480_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.BR481_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.BR485_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
rm -f ./vehicle.ET165_*.pak >> "$repo/LOG/triebwagenstrom.log" 2>> "$repo/LOG/error_triebwagenstrom.log"
mv ./*.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../../Vieh
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/raillog_allgemein.log" 2>> "$repo/LOG/error_rail_allgemein.log"
cd ../../
echo "Erstelle Vehicle/Road"
cd ./road/Auto
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_01
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_02
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_03
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_04
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_05
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_06
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_07
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../catg_08
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../Passagiere
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../Post
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../Stahl
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../Vieh
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../Zugmaschinen
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/road.log" 2>> "$repo/LOG/error_road.log"
cd ../../
echo "Erstelle Vehicle/Tram"
cd ./tram/Passagiere
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/tram.log" 2>> "$repo/LOG/error_tram.log"
cd ../Fracht
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/tram.log" 2>> "$repo/LOG/error_tram.log"
cd ../Post
$repo/makeobj PAK128 $repo/vehicle/ ./ >> "$repo/LOG/tram.log" 2>> "$repo/LOG/error_tram.log"
cd ../
cd ../
echo -e "Erstelle Vehicle/Water\033[0m"
cd ./water/Auto
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_01
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_02
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_03
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_04
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_05
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_06
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_07
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../catg_08
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Passagiere
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Passagiere_alt
$repo/makeobj PAK128 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Post
$repo/makeobj PAK128 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Post255
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Schlepper
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Stahl
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Treidel_Pferde_PAK128
$repo/makeobj PAK128 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
cd ../Vieh
$repo/makeobj PAK255 >> "$repo/LOG/water.log" 2>> "$repo/LOG/error_water.log"
mv *.pak $repo/vehicle >> "$repo/LOG/cmd.log" 2>> "$repo/LOG/error_cmd.log"
echo -e "zusammenpacken der Vehicle\033[0m"
cd $repo/vehicle
$repo/makeobj MERGE $repo/simutrans/PAK128.german/vehicle.all.pak ./vehicle.*.pak >> "$repo/LOG/vehicle_all.log" 2>> "$repo/LOG/error_vehicle_all.log"
#cp ./vehicle.*.pak $repo/simutrans/PAK128.german/  >> "$repo/LOG/vehicle_all.log" 2>> "$repo/LOG/error_vehicle_all.log"
rm ./vehicle.*.pak >> "$repo/LOG/vehicle_all.log" 2>> "$repo/LOG/error_vehicle_all.log"
cd $repo
