<?php  
require_once('./include/parameter.php');
require_once('./include/dblib.php');


if ( file_exists('../data/rss/rss_items_roh') ) {

   $data = file('../data/rss/rss_items_roh');
   unlink('../data/rss/rss_items_roh');
   rsort($data);

   if ( file_exists('../data/rss/rss_items') ) 
   { $data2 = file('../data/rss/rss_items');
     rsort($data2);
   } else $data2 = array(" \n");

   $log_new = fopen("../data/rss/rss_items_new", "w");

   $line = $data2[0];
   // doppelte suchen, den jüngsten und alle nicht doppelten schreiben
   $roh = array();      
   foreach ($data as $line_roh)
   { if ($line_roh <= $line) break;
     $r = substr($line_roh, 19);
     if ( !in_array($r, $roh))
     { $roh[] = $r;
       fwrite($log_new,$line_roh);
     }
   }

   // Eintraege die aelter als 30 Tage sind loeschen  
   $verfall = 30 * 86400;
   $m =  date("Y-m-d",time() - $verfall);
   foreach ($data2 as $line)
   { if ($line < $m) break;
     fwrite($log_new,$line);
   }
   fclose($log_new);
   unlink('../data/rss/rss_items');
   rename('../data/rss/rss_items_new', '../data/rss/rss_items');
} 

   
?>





