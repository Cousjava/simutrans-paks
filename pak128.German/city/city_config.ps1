Write-Host "Erstelle City Objekte"  -ForegroundColor Magenta
$empty_line

cd .\city_cars
Write-Host "City Cars" -ForegroundColor Green
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" 1>>$repo\LOG\citycars.log 2>>$repo\LOG\error_ccars.log

Set-Location $repo\city\com
Write-Host "City-COM" -ForegroundColor Green
& "$repo\makeobj.exe" "PAK128" >$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_ALLCLT.pak" "./building.COM_ALL_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_ALPIN.pak" "./building.COM_ALP_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_ALPVOR.pak" "./building.COM_AVL_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_MITGEB.pak" "./building.COM_MGB_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_OSTDTL.pak" "./building.COM_ODL_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
& "$repo\makeobj.exe" "MERGE" "building.COM_NORDTE.pak" "./building.COM_NDT_*.pak" 1>>$repo\LOG\building_com.log 2>>$repo\LOG\error_com.log
move .\building.COM_ALLCLT.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move .\building.COM_ALPIN.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move .\building.COM_ALPVOR.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move .\building.COM_MITGEB.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move .\building.COM_OSTDTL.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move .\building.COM_NORDTE.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
Remove-Item .\*.pak

Write-Host "City Extra" -ForegroundColor Green
Set-Location $repo\city\extra
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\building_extra.log 2>>$repo\LOG\error_extra.log

Write-Host "City Industrie" -ForegroundColor Green
Set-Location $repo\city\ind
& "$repo\makeobj.exe" "PAK128" >$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_ALLCLT.pak" "./building.IND_ALL_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_ALPIN.pak" "./building.IND_ALP_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_ALPVOR.pak" "./building.IND_AVL_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_MITGEB.pak" "./building.IND_MGB_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_OSTDTL.pak" "./building.IND_ODL_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
& "$repo\makeobj.exe" "MERGE" "building.IND_NORDTE.pak" "./building.IND_NDT_*.pak" >>$repo\LOG\building_ind.log 2>>$repo\LOG\error_ind.log
move .\building.IND_ALLCLT.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
move .\building.IND_ALPIN.pak $repo\simutrans\PAK128.german  >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
move .\building.IND_ALPVOR.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
move .\building.IND_MITGEB.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
move .\building.IND_OSTDTL.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
move .\building.IND_NORDTE.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log
Remove-Item .\*.pak >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_ind.log

Write-Host "City Monumente" -ForegroundColor Green
Set-Location $repo\city\monument
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\building_monument.log 2>>$repo\LOG\error_monument.log

Write-Host "City Wohnhaeuser" -ForegroundColor Green
Set-Location $repo\city\res
& "$repo\makeobj.exe" "PAK128" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_ALLCLT.pak" "./building.RES_ALL_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_ALPIN.pak" "./building.RES_ALP_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_ALPVOR.pak" "./building.RES_AVL_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_MITGEB.pak" "./building.RES_MGB_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_OSTDTL.pak" "./building.RES_ODL_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
& "$repo\makeobj.exe" "MERGE" "building.RES_NORDTE.pak" "./building.RES_NDT_*.pak" >>$repo\LOG\building_res.log 2>>$repo\LOG\error_res.log
move building.RES_ALLCLT.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move building.RES_ALPIN.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move building.RES_ALPVOR.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move building.RES_MITGEB.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move building.RES_OSTDTL.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
move building.RES_NORDTE.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log
Remove-Item .\*.pak >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_Pakmaker.log

Write-Host "Erstelle Pedestrians" -ForegroundColor Green
$empty_line
Set-Location $repo\city\pedestrians
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\pedestrians.log 2>>$repo\LOG\error_pedestrians.log
Write-Host "Erstelle mehrkachelhaus" -ForegroundColor Green
$empty_line

Set-Location $repo\city\mehrkachelhaus
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\mehrkachelhaus.log 2>>$repo\LOG\error_mehrkachelhaus.log

Set-Location $repo\city\Clusterhaus
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\Clusterhaus.log 2>>$repo\LOG\error_Clusterhaus.log
Set-Location $repo
