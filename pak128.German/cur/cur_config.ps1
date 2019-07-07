Write-Host "Erstelle CUR Objekte" -ForegroundColor Magenta
$empty_line
Set-Location $repo\cur\cur-city
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\cur-city.log 2>>$repo\LOG\error_cur-city.log
& "$repo\makeobj.exe" "PAK160" "$repo/simutrans/PAK128.german/" "./160/FTHamburg.dat" >>$repo\LOG\cur-city.log 2>>$repo\LOG\error_cur-city.log
Set-Location $repo\cur\cur-land
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\cur-land.log 2>>$repo\LOG\error_cur-land.log
Set-Location $repo