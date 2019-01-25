Write-Host "Erstelle Waren" -ForegroundColor Magenta
$empty_line

& "$repo\makeobj.exe" "PAK" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\goods.log 2>$repo\LOG\error_goods.log
Set-Location $repo