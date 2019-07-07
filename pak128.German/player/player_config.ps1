Write-Host "Erstelle Player Gebaeude" -ForegroundColor Magenta
$empty_line

cd .\air
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\all
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\monorail
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\rail
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\road
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\tram
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
cd ..\water
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\player.log 2>>$repo\LOG\error_player.log
Set-Location $repo