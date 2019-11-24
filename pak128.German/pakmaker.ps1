$repo = "D:\HerrUntermoser\Subversion\PAK128German"
$empty_line = "--------------------------------"
Add-Type -AssemblyName System.Windows.Forms
if (-NOT (Test-Path "$repo\makeobj.exe"))
{
[System.Windows.Forms.MessageBox]::Show("Bitte MakeObj ins SVN kopieren",'MakeObj nicht gefunden!','0',"Error")
exit
}
$eingabe = [System.Windows.Forms.MessageBox]::Show('Set loeschen & neues erstellen?','Pakmaker for Powershell 17.11.2018','1',"Question")
if ($eingabe -eq "Cancel") {exit}
else {
if (Test-Path .\LOG){ Remove-Item .\LOG\*.log}
if (Test-Path .\simutrans)
{
Write-Host "Loesche vorheriges Pakset" -ForegroundColor Red
$empty_line
Remove-Item .\simutrans\PAK128.german\*.pak 4>&1> "$repo\LOG\Pakmaker.log" 2> "$repo\LOG\Error_Pakmaker.log"
Remove-Item .\simutrans\PAK128.german\compat.tab 4>&1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"
Remove-Item .\simutrans\PAK128.german\README\*.html 4>&1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"
Remove-Item -Recurse .\simutrans\PAK128.german\scenario\* 4>&1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"
}
 
}
Write-Host "Kopiere Doku" -ForegroundColor Cyan
$empty_line
Copy-Item -Path $repo\README\*.html -Destination $repo\simutrans\PAK128.german\README -PassThru 4>&1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"
Copy-Item -Path $repo\README\inc\* -Destination $repo\simutrans\PAK128.german\README\inc -PassThru 4>&1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"

Write-Host "kopiere Scenario" -ForegroundColor Cyan
xcopy $repo\scenario $repo\simutrans\PAK128.german\scenario\ /S/e 1>> "$repo\LOG\Pakmaker.log" 2>> "$repo\LOG\Error_Pakmaker.log"
$empty_line

Write-Host "Kopiere Konfigurationsdateien" -ForegroundColor Cyan
Copy-Item $repo\config\*.tab $repo\simutrans\PAK128.german\config -PassThru 4>&1>>$repo\log\Pakmaker.log 2>>$repo\LOG\Error_Pakmaker.log
$empty_line

Write-Host "Kopiere Sound" -ForegroundColor Cyan
Copy-Item $repo\sound\*.wav $repo\simutrans\PAK128.german\sound -PassThru 4>&1>>$repo\log\Pakmaker.log 2>>$repo\log\Error_sound.log
Copy-Item $repo\sound\sound.tab $repo\simutrans\PAK128.german\sound -PassThru 4>&1>>$repo\log\Pakmaker.log 2>>$repo\log\Error_sound.log
$empty_line

Write-Host "Kopiere Pakset Textdateien" -ForegroundColor Cyan
Set-location $repo\pak.text
Copy-Item .\*.txt $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\compat.tab $repo\simutrans\PAK128.german -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\de2.tab $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\de.tab $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\en.tab $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\ja.tab $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
Copy-Item .\pl.tab $repo\simutrans\PAK128.german\text -PassThru 4>&1>> $repo\log\Pakmaker.log 2>>$repo\log\Error_Pakmaker.log
$empty_line

Set-location $repo\city
. .\city_config.ps1
Set-Location $repo\cur
. .\cur_config.ps1
Set-Location $repo\factory
. .\factory_config.ps1
Set-Location $repo\good
. .\good_config.ps1
Set-Location $repo\ground
. .\ground_config.ps1
Set-Location $repo\other
. .\other_config.ps1
Set-Location $repo\player
. .\player_config.ps1
Set-Location $repo\smoke
. .\smoke_config.ps1
Set-Location $repo\tree
. .\tree_config.ps1
Set-Location $repo\vehicle
. .\vehicle_config.ps1
Set-Location $repo\way
. .\way_config.ps1
#Zeile drunter nur auskommentieren, wenn die Batchdatei nicht bei jedem Update des SVN-Clienten sowieso aufgerufen wird.
#. .\repository_info.bat
Write-Host "aktualisiere Outside.dat" -ForegroundColor Yellow
$empty_line
""
$revisionstext = Get-Content ".\repository_info.tab"
$outside = Get-Content "$repo\ground\Outside.dat"
$revision = $revisionstext|Select-String -Pattern "Revision"
$revision = $revision -replace "Revision:","Rev."
$outside = $outside -replace "Rev.\s\d+","$revision"
$outside | Set-Content ".\Outside.dat"
if (-NOT (Test-Path "$repo\Outside.png"))
{Copy-Item $repo\ground\Outside.png $repo -PassThru >>.\LOG\Pakmaker.log}
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>.\LOG\ground.log 2>>.\LOG\Pakmaker.log
Set-Location $repo\simutrans\Pak128.German
$Dateien = Get-ChildItem -Recurse | Measure-Object | %{$_.Count}
$Ordner = Get-ChildItem -Directory | Measure-Object | %{$_.Count}
[System.Windows.Forms.MessageBox]::Show("Pakset erstellt: $Dateien Dateien & $Ordner Ordner",'Pakmaker for Powershell 17.11.2018','0',"Asterisk")
Set-Location $repo
. .\pakmakerfehler.ps1
