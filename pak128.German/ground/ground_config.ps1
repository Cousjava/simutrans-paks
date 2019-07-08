Write-Host "Erstelle Bodenobjekte" -ForegroundColor Magenta
$mpty_line
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\ground.log 2>>$repo\LOG\error_ground.log
Set-Location $repo