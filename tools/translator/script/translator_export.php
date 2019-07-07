<?php

////////////////////////////////////////////////////////////////////////////////
// Translator translate files
$translator_lang_page = array();
$translator_lang_page['lng_main'][0] = 'LNG_HEAD';
$translator_lang_page['lng_main'][1] = 'LANG_CONTACT';
$translator_lang_page['lng_main'][2] = 'LNG_MAIN';
$translator_lang_page['lng_main'][3] = 'LNG_LOGIN';
$translator_lang_page['lng_main'][4] = 'LNG_FORM';
$translator_lang_page['lng_main'][5] = 'LNG_FORM_SUB';
$translator_lang_page['lng_main'][6] = 'LNG_GUIDE';
$translator_lang_page['lng_main'][7] = 'LNG_WRAP';
$translator_lang_page['lng_main'][8] = 'LNG_STATS';
$translator_lang_page['lng_main'][9] = 'LNG_STATS_TRANS';
$translator_lang_page['lng_main'][10] = 'LNG_STATS_VEH';
//$translator_lang_page['lng_main'][11] = 'LNG_BROWSE';
//$translator_lang_page['lng_main'][12] = 'LNG_BROWSE_NOT';
//$translator_lang_page['lng_main'][13] = 'LNG_BROWSE_HAS';
$translator_lang_page['lng_main'][14] = 'LNG_BROWSE_LIST';
$translator_lang_page['lng_main'][15] = 'LNG_LOAD';
$translator_lang_page['lng_main'][16] = 'LNG_LOAD2';
$translator_lang_page['lng_main'][17] = 'LNG_LOAD3';
$translator_lang_page['lng_main'][18] = 'LNG_EDIT';
$translator_lang_page['lng_main'][19] = 'LNG_LANGUAGE';
$translator_lang_page['lng_main'][20] = 'LNG_RSS_FEED';
$translator_lang_page['lng_main'][21] = 'LNG_INFO';
$translator_lang_page['lng_main'][22] = 'LNG_SETCOMPARE';
//$translator_lang_page['lng_main'][] = '';
$translator_lang_page['lng_manage'][0] = 'LNG_MANAGE';
$translator_lang_page['lng_manage'][1] = 'LNG_OBJ_IMPORT';
$translator_lang_page['lng_manage'][2] = 'LNG_OBJ_BROWSER';
$translator_lang_page['lng_manage'][3] = 'LNG_ADMIN';
//$translator_lang_page['lng_manage'][] = '';
$translator_lang_page['lng_preferences'][0] = 'LNG_USER';
//$translator_lang_page['lng_preferences'][] = '';


include_once('tpl_script/header.php');



////////////////////////////////////////////////////////////////////////////////
function translation_text($lang_id)
{

    GLOBAL $translator_lang_page;

    $tab = 'translations_'.TRANSLATOR_SET_ID; 
    $version_id = TRANSLATOR_SET_ID;
    //$lang_id = 'de';
 
    //select the objects
    //order by object type (way, builing...) and than by name
    //$query= "SELECT * FROM `objects` o JOIN `translations` t ON (o.obj_name=t.object_obj_name) WHERE t.language_language_id='".$lang_id."' AND t.object_version_version_id=".$version_id." AND (tr_text IS NOT NULL) ORDER BY `obj`,`obj_name`";
    $query= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.object_version_version_id=".$version_id." AND t.language_language_id='".$lang_id."' ORDER BY `obj_name` ASC";
    $query_en= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.object_version_version_id=".$version_id." AND t.language_language_id='en' ORDER BY `obj_name` ASC";
 
    $result = db_query($query) or die("SQL error : " . db_error() );
    $result_en = db_query($query_en) or die("SQL error : " . db_error() );

    $obj2= "";
    $res = '';
    
    $lng_main = '';
    $lng_preferences = '';
    $lng_manage = '';
    $ref_main = '';
    $ref_preferences = '';
    $ref_manage = '';

    while($row=db_fetch_object($result))
    {
      $row_en=db_fetch_object($result_en);
      //add a header at the begining of new section (depending on object type
      $obj = substr($row->obj_name, 0, strpos($row->obj_name, "["));
      $file = substr($obj, 1);
                 
      if ( $obj != $obj2 )
      {
              $s = 0;
            if ( strpos($lng_main, $obj.' = array();') > 0 || strpos($lng_preferences, $obj.' = array();') > 0 || strpos($lng_manage, $obj.' = array();') > 0 ) {
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
         if ( in_array($file, $translator_lang_page['lng_main']) ) 
         {  $ref_main .= $para.' = '.$text.";\n";                  
         } elseif ( in_array($file, $translator_lang_page['lng_preferences']) ) 
         {  $ref_preferences .= $para.' = '.$text.";\n";                 
         } elseif ( in_array($file, $translator_lang_page['lng_manage']) )
         {  $ref_manage .= $para.' = '.$text.";\n";                  
         }
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

      if ( in_array($file, $translator_lang_page['lng_main']) ) 
      {  $lng_main .= $res;                  
      } elseif ( in_array($file, $translator_lang_page['lng_preferences']) ) 
      {  $lng_preferences .= $res;                 
      } elseif ( in_array($file, $translator_lang_page['lng_manage']) )
      {  $lng_manage .= $res;                  
      }
      $res = '';
                
    }

    db_free_result($result);
    db_free_result($result_en);  
  
    translator_files ($lang_id, $lng_main.$ref_main, 'lng_main.php');
    translator_files ($lang_id, $lng_preferences.$ref_preferences, 'lng_preferences.php');
    translator_files ($lang_id, $lng_manage.$ref_manage, 'lng_manage.php');

}

function translator_objects()
{

    GLOBAL $tabpfad, $translator_lang_page;

    $version_id = TRANSLATOR_SET_ID;
    //$lang_id = 'de';

    //select the objects
    //order by object type (way, builing...) and than by name
     //$query= "SELECT * FROM `objects` o JOIN `translations` t ON (o.obj_name=t.object_obj_name) WHERE t.language_language_id='".$lang_id."' AND t.object_version_version_id=".$version_id." AND (tr_text IS NOT NULL) ORDER BY `obj`,`obj_name`";
     $query= "SELECT * FROM `objects`  WHERE version_version_id=".$version_id." ORDER BY `obj_name` ASC";
 
    $result = db_query($query) or die("SQL error : " . db_error() );
  
    $lng_main = '';
    $lng_preferences = '';
    $lng_manage = '';

    while($row=db_fetch_object($result))
  {
    $obj = substr($row->obj_name, 0, strpos($row->obj_name, "["));
    $file = substr($obj, 1);
                
    $res = 'obj='.$row->obj."\n";     
    $res .= 'name='.$row->obj_name."\n";      
    $res .= 'note='.$row->note."\n";
    $res .= "---\n";      

    if ( in_array($file, $translator_lang_page['lng_main']) ) {
      $lng_main .= $res;                  
    } elseif ( in_array($file, $translator_lang_page['lng_preferences']) ) {
      $lng_preferences .= $res;                 
    } elseif ( in_array($file, $translator_lang_page['lng_manage']) ) {
      $lng_manage .= $res;                  
    }
                
  }

  db_free_result($result);  
  
        $file_name = $tabpfad.$version_id.'/lng_main.dat';    
        //$fp=fopen($tempfilepfad.$file,"wb");
        $fp=fopen($file_name,"wb");
          fwrite($fp, $lng_main);
          fclose($fp); 

        $file_name = $tabpfad.$version_id.'/lng_preferences.dat';    
        //$fp=fopen($tempfilepfad.$file,"wb");
        $fp=fopen($file_name,"wb");
          fwrite($fp, $lng_preferences);
          fclose($fp); 

        $file_name = $tabpfad.$version_id.'/lng_manage.dat';    
        //$fp=fopen($tempfilepfad.$file,"wb");
        $fp=fopen($file_name,"wb");
          fwrite($fp, $lng_manage);
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
