<?php
    require_once('./include/parameter.php'); 
    require_once('./include/dblib.php');
    require_once('./include/general.php');
  include ('./include/images.php');

    // update RSS Feed  
    if ( file_exists('../data/rss/rss_items_roh') ) include('rss_update.php');


    
    cacheImageClear();
    mysqli_query($st_dbi,"OPTIMIZE TABLE `versions`") or die ("db_error".mysqli_error($st_dbi));
    mysqli_query($st_dbi,"OPTIMIZE TABLE `users`")    or die ("db_error".mysqli_error($st_dbi));
    mysqli_query($st_dbi,"OPTIMIZE TABLE `objects`")   or die ("db_error".mysqli_error($st_dbi));
    mysqli_query($st_dbi,"OPTIMIZE TABLE `property`")  or die ("db_error".mysqli_error($st_dbi));

    foreach($versions_all as $vs_id => $vs_a_name) 
    { echo "OPTIMIZE TABLE for Version ".$vs_id.":".$vs_a_name."<br>\n";
      mysqli_query($st_dbi,"OPTIMIZE TABLE images_".      $vs_id) or die("db_error".mysqli_error($st_dbi));
      mysqli_query($st_dbi,"OPTIMIZE TABLE translations_".$vs_id) or die("db_error".mysqli_error($st_dbi));

      // del old translated file
      foreach ($language_all as $lang_id => $lang_name)
       { /*
         if ( file_exists($tabpfad.$vs_id.'/'.$lang_id.'.tab')) unlink($tabpfad.$vs_id.'/'.$lang_id.'.tab');
         
         if ( file_exists($tabpfad.$id_basetexts.'/'.$lang_id."/".$current_ob_name) && $current_ob_obj_type == 'help_file' ) 
         { unlink($tabpfad.$id_basetexts.'/'.$lang_id.'/'.$current_ob_name);
         } */
       }
    };

    
    echo "ende jede nacht alles ok";
    
?>
