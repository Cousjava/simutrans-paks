Write-Host "Erstelle Fahrzeuge" -ForegroundColor Magenta
$empty_line

Write-Host "Flugzeuge" -ForegroundColor Green
$empty_line

Set-Location $repo\vehicle\air\catg_01
& "$repo\makeobj.exe" "PAK255" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\catg_04
& "$repo\makeobj.exe" "PAK255" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\Passagiere
& "$repo\makeobj.exe" "PAK255" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\Passagiere_alt
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\Post
& "$repo\makeobj.exe" "PAK255" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\Post_alt
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log
cd ..\Vieh
& "$repo\makeobj.exe" "PAK255" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\air.log 2>>$repo\LOG\error_air.log

Set-Location $repo\vehicle\monorail
Write-Host "Schwebebahn" -ForegroundColor Green
$empty_line
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\monorail.log 2>$repo\LOG\error_monorail.log

Set-Location $repo\vehicle\rail\Autos
Write-Host "Zuege" -ForegroundColor Green
$empty_line
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_01
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_02
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_03
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_04
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_05
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_06
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_07
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\catg_08
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\Lokomotiven\Dampf
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\dampflokomotiven.log 2>>$repo\LOG\error_dampfloks.log
& "$repo\makeobj.exe" "MERGE" "./vehicle.BZT_prP8.pak" "./vehicle.P8.pak" "./vehicle.P8Tender.pak" >>$repo\LOG\dampflokomotiven.log 2>>$repo\LOG\error_dampfloks.log
Remove-Item vehicle.P8.pak
Remove-Item vehicle.P8Tender.pak 2>>$repo\LOG\Error_Pakmaker.log
& "$repo\makeobj.exe" "MERGE" "./vehicle.BZT_prG8.pak" "./vehicle.G8.pak" "./vehicle.PrG8Tender.pak" >>$repo\LOG\dampflokomotiven.log 2>>$repo\LOG\error_dampfloks.log
Remove-Item vehicle.G8.pak 2>>$repo\LOG\Error_Pakmaker.log
Remove-Item vehicle.PrG8Tender.pak 2>>$repo\LOG\Error_Pakmaker.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Diesel
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\dieseloks.log 2>>$repo\LOG\error_dieseloks.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\cmd_dieseloks.log 2>>$repo\LOG\error_dieseloks.log
cd ..\Elek
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\e-loks.log 2>>$repo\LOG\error_e-loks.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
Set-Location $repo\vehicle\rail\Passagiere
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\passagierwaggons.log 2>>$repo\LOG\error_passagierwaggons.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Post
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\rail.log 2>>$repo\LOG\error_rail.log
cd ..\Stahl
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\raillog_allgemein.log 2>>$repo\LOG\error_rail_allgemein.log
cd ..\Triebwagen\Akku
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\triebwagenakku.log 2>>$repo\LOG\error_dieseloks.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Dampf
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\triebwagendampf.log 2>>$repo\LOG\error_dieseloks.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Diesel
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\triebwagendiesel.log 2>>$repo\LOG\error_triebwagendiesel.log
& "$repo\makeobj.exe" "MERGE" "vehicle.Integral_all.pak" "./vehicle.IntegBOB*.pak" >>$repo\LOG\triebwagendiesel.log 2>>$repo\LOG\error_triebwagendiesel.log
move vehicle.Integral_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
Remove-Item vehicle.IntegBOB*.pak 2>>$repo\LOG\Error_Pakmaker.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Elek
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.ICE_1_all.pak" "./vehicle.ICE1*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.ICE_3_all.pak" "./vehicle.ICE3*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.Flirt_all.pak" "./vehicle.FLIRT*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.BR_477_all.pak" "./vehicle.BR477_*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.BR_480_all.pak" "./vehicle.BR480_*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.BR_481_all.pak" "./vehicle.BR481_*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.BR_485rot_all.pak" "./vehicle.BR485_rot*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.BR485_rot_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.BR_485_all.pak" "./vehicle.BR485_*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
& "$repo\makeobj.exe" "MERGE" "vehicle.ET_165_all.pak" "./vehicle.ET165_*.pak" >>$repo\LOG\triebwagenstrom.log 2>>$repo\LOG\error_triebwagenstrom.log
move vehicle.BR_477_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BR_480_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BR_481_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BR_485_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BR_485rot_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.ET_165_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.ICE_1_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.Flirt_all.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.ET25A.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BZT_ET25B.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
move vehicle.BZT_ET25C.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
Remove-Item .\vehicle.ICE1*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.ICE3*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.FLIRT*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.BR477_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.BR480_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.BR481_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.BR485_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
Remove-Item .\vehicle.ET165_*.pak 2>>$repo\LOG\error_triebwagenstrom.log
move .\*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
Set-Location $repo\vehicle\rail\Vieh
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\raillog_allgemein.log 2>>$repo\LOG\error_rail_allgemein.log

Set-Location $repo\vehicle\road\Auto
Write-Host "Strassenfahrzeuge" -ForegroundColor green
$empty_line
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_01
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_02
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_03
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_04
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_05
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_06
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_07
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\catg_08
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\Passagiere
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\Post
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\Stahl
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\Vieh
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log
cd ..\Zugmaschinen
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\road.log 2>>$repo\LOG\error_road.log

Set-Location $repo\vehicle\tram\Passagiere
Write-Host "Strassenbahnen" -ForegroundColor Green
$empty_line

& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\tram.log 2>>$repo\LOG\error_tram.log
cd ..\Fracht
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\tram.log 2>>$repo\LOG\error_tram.log
cd ..\Post
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\tram.log 2>>$repo\LOG\error_tram.log

Set-Location $repo\vehicle\water\Auto
Write-Host "Schiffe" -ForegroundColor Green

& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_01
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_02
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_03
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_04
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_05
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_06
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_07
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\catg_08
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Passagiere
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Passagiere_alt
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Post
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Post255
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Schlepper
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Stahl
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Treidel_Pferde_PAK128
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
cd ..\Vieh
& "$repo\makeobj.exe" "PAK255" >>$repo\LOG\water.log 2>>$repo\LOG\error_water.log
move *.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
Set-Location $repo