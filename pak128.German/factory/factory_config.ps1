Write-Host "Erstelle Fabriken" -ForegroundColor Magenta
$empty_line

Set-Location $repo\factory\Apotheke
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Autohandel
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Baeckerei
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Baumarkt
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Baustellen
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Beton
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Brauerei
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Brennstoffhandel
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Buecher
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Chemiefabrik
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Computerhersteller
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Discounter
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Elektrohandel
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Getraenkehandel
cd 160
& "$repo\makeobj.exe" "PAK160" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Glas
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\ImportExport
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kameras
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kaufhaus
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kelterei                                                     
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kfz-Fabrik                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kieswerk                                                     
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kokerei                                                      
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kraftwerke                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Krankenhaus                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Lebensmittelfabrik                                           
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Markt                                                        
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Marzipanfabrik                                               
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Maschinen                                                    
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Milchwerk                                                    
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Moebel                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Muehlen                                                      
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Muell                                                        
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Papierfabrik                                                 
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Pharmawerk                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Raffinerie                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Saegewerk                                                    
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Schwimmdock                                                  
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Schlachthof                                                  
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\SchrottHandel                                                
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Silo                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Stahlwerk                                                    
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Supermarkt                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Tankstelle                                                   
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Textilfabrik                                                 
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Tierfutterfabrik                                             
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Verpackungsmittel                                            
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Versandhandel                                            
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Verwaltung                                           
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Werft                                                        
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Zement                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Zuckerfabrik                                                 
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
                                                                   
Set-Location $repo\factory\Rohstoffe                               
Write-Host "Erstelle Rohstoff Produzenten" -ForegroundColor Magenta
$empty_line                                                        
                                                                   
cd .\Erz                                                           
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Felder                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Fische                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Getreide                                                     
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Holz                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Hopfen                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Kalisalz                                                     
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\factory.log 2>>$repo\LOG\error_factory.log
cd ..\Kohle                                                        
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Obst                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Oel                                                          
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Sand                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Steine                                                       
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Vieh                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Wein                                                         
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
cd ..\Zuckerrueben                                                 
& "$repo\makeobj.exe" "PAK128" "$repo/simutrans/PAK128.german/" "./" >>$repo\LOG\LOG_rohstoffe.log 2>>$repo\LOG\error_rohstoffe.log
Set-Location $repo
