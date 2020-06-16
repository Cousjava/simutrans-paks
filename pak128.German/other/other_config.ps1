Write-Host "Erstelle Symbols, Cursors, Menu und Misc" -ForegroundColor Magenta
$empty_line
& "$repo\makeobj.exe" "PAK" "./" "./" >$repo\LOG\other.log 2>>$repo\LOG\error_other.log
& "$repo\makeobj.exe" "PAK128" "./" "./new_cursor.txt" >>$repo\log\other.log 2>>$repo\LOG\error_other.log
& "$repo\makeobj.exe" "PAK128" "./" "./128/MiscImages.dat" >>$repo\log\other.log 2>>$repo\LOG\error_other.log
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./Logo-2.txt" >>$repo\LOG\other.log 2>>$repo\log\error_other.log
& "$repo\makeobj.exe" "MERGE" "./symbol.pak" "./symbol.*.pak" >>$repo\LOG\other.log 2>>$repo\LOG\error_other.log
move symbol.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_other.log
& "$repo\makeobj.exe" "MERGE" "./misc.pak" "./misc.*.pak" >>$repo\LOG\other.log 2>>$repo\LOG\error_other.log
move misc.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_other.log
move cursor.*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_other.log
move menu.*.pak $repo\simutrans\PAK128.german >>$repo\LOG\Pakmaker.log 2>>$repo\LOG\error_other.log
Remove-Item .\*.pak 2>>$repo\LOG\error_other.log
Set-Location $repo
