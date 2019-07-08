Write-Host "Erstelle Leitungen, Signale und Wege" -ForegroundColor Magenta
$empty_line

Set-Location $repo\way\air
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\crossing
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\fence
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\monorail\schwebebahn
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\..\rail
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log 
cd ..\road
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log 
cd ..\signale\air
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\monorail
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\rail
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\road
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\..\strom
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd Oberleitung
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
Set-Location $repo\way\tram
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
cd ..\water
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\way.log 2>>$repo\LOG\error_way.log
Set-Location $repo