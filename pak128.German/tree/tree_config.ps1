Write-Host "Erstelle Baeume" -ForegroundColor Magenta
$empty_line

& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\tree.log 2>>$repo\LOG\error_tree.log
Set-Location $repo\tree_gross
& "$repo\makeobj.exe" "PAK192" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\tree.log 2>>$repo\LOG\error_tree.log
Set-Location $repo