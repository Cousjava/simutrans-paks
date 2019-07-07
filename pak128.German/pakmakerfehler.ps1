cd .\LOG
$dateien = Get-ChildItem .\error*.log
cd ..\
"PAKMAKER FEHLER" | Set-Content .\PAKMAKERFEHLER.txt
"" | Add-Content .\PAKMAKERFEHLER.txt
foreach ($inhalt in $dateien)
{
$text = Get-Content $inhalt
$text | Select-String -Pattern '^error|warning' | Add-content .\PAKMAKERFEHLER.txt
}