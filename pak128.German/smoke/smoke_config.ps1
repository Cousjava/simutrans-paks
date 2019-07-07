Write-Host "Erstelle Rauch&Dampf Objekte" -ForegroundColor Magenta
$empty_line

& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\smoke.log 2>>$repo\LOG\error_smoke.log
Set-Location $repo