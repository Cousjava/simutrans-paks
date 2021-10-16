<?php



include_once('tpl_script/header.php');



////////////////////////////////////////////////////////////////////////////////
function translation_text($lang_id)
{
    $tab = 'translations_'.TRANSLATOR_SET_ID; 
    $version_id = TRANSLATOR_SET_ID;

    $query= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.object_version_version_id=".$version_id." AND t.language_language_id='".$lang_id."' AND obj='translator' ORDER BY `obj_name` ASC";
    $query_en= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.object_version_version_id=".$version_id." AND t.language_language_id='en' AND obj='translator' ORDER BY `obj_name` ASC";
 
    $result = db_query($query) or die("SQL error : " . db_error() );
    $result_en = db_query($query_en) or die("SQL error : " . db_error() );

    $obj2= "";
    $res = '';
    
    $lng_main = '';
    $ref_main = '';

    while($row=db_fetch_object($result))
    {
      $row_en=db_fetch_object($result_en);
      //add a header at the begining of new section (depending on object type
      $obj = substr($row->obj_name, 0, strpos($row->obj_name, "["));
      $file = substr($obj, 1);
                 
      if ( $obj != $obj2 )
      {
              $s = 0;
            if ( strpos($lng_main, $obj.' = array();') > 0 ) {
              $s = 1;
            } 
            if ( $s == 0 ) { $res .= "\n".$obj." = array();\n"; }
                    $obj2 = $obj;
      }
        
      if( $row->tr_text != '' || $row->tr_text != null )
      { $para = $row->obj_name;
        $text = $row->tr_text;
      } else 
      { $para = $row_en->obj_name;
        $text = $row_en->tr_text;
      }
      if ( substr($text, 0, 1) == '$' )
      {  // die referenzen nach hinten stellen weil sie sonst eventuell nicht gesetzt sind
         $ref_main .= $para.' = '.$text.";\n";
      } else 
      { //print object name
        $res .= $para.' = ';
        //print translation 
        if ( strpos($text, '%s') != false )
        { //$my_line = str_replace('"', '\"', $text);
          $res .= '"'.$text.'";'."\n"; 
        } else 
        { $my_line = str_replace("'", "\'", $text);
          $res .= "'".$my_line."';\n"; 
        }
      }

      $lng_main .= $res;
      $res = '';
    }

    db_free_result($result);
    db_free_result($result_en);  
  
    translator_files ($lang_id, $lng_main.$ref_main, 'lng_main.php');
}

function translator_objects()
{
    GLOBAL $tabpfad;

    $version_id = TRANSLATOR_SET_ID;

    $query= "SELECT * FROM `objects`  WHERE version_version_id=".$version_id." ORDER BY `obj_name` ASC";
 
    $result = db_query($query) or die("SQL error : " . db_error() );
  
    $lng_main = '';

    while($row=db_fetch_object($result))
    {
      $obj = substr($row->obj_name, 0, strpos($row->obj_name, "["));
      $file = substr($obj, 1);
  
      $res = 'obj='.$row->obj."\n";     
      $res .= 'name='.$row->obj_name."\n";      
      $res .= 'note='.$row->note."\n";
      $res .= "---\n";      
      $lng_main .= $res;
    }

    db_free_result($result);  

    $file_name = $tabpfad.$version_id.'/lng_main.dat';    
    $fp=fopen($file_name,"wb");
    fwrite($fp, $lng_main);
    fclose($fp); 
}

function translator_files ($lang_id, $data, $file)
{
    GLOBAL $tabpfad, $tempfilepfad, $tab_file_path;
   
    $filetext = "<?PHP\n\n";
    $filetext .= $data;
    $filetext .= "\n?>";
    
    if ( !is_dir($tabpfad.TRANSLATOR_SET_ID ) ) { mkdir($tabpfad.TRANSLATOR_SET_ID, 0775); }
    if ( !is_dir($tabpfad.TRANSLATOR_SET_ID.'/'.$lang_id ) ) { mkdir($tabpfad.TRANSLATOR_SET_ID.'/'.$lang_id, 0775); }

    $file_name = $tabpfad.TRANSLATOR_SET_ID.'/'.$lang_id.'/'.$file;    
    $fp=fopen($file_name,"wb");
    fwrite($fp, $filetext);
    fclose($fp); 

    echo  $file_name.' -- '.filesize($file_name).'<br>'; 
}


//////////////////////////////////
// Main
//////////////////////////////////

$languages = db_query("SELECT `language_id` FROM `languages`");
    while ($row = db_fetch_array($languages))
    {
        translation_text($row[0]);
    }
    db_free_result($languages);
    translator_objects();
 
    $pack_name = 'translator.zip';
    
    $pfad = $tabpfad.TRANSLATOR_SET_ID.'/';
    //pak the temp file for zip
    $rt = zip_target_directory ($pfad, $tabpfad, $pack_name);

    $file_name = $zippfad.basename($pack_name);


    print_line ('<h2><a href="'.$file_name.'">'.$LNG_WRAP[9].': '.basename($file_name).'</a></h2>', 1);


?>
