<?php
include('include/select.php');
//defines, and wrapping functions
include('include/wrapper_include.php');

//this page is publicly acessible

//created header (and establishes session information)
  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('tpl/wrap.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] =  $page_titel[$title];


//this function generates the selection menu - called when no post data are present
function selection_menu ($version_id,$lang_id)
{
  GLOBAL $LNG_FORM, $LNG_WRAP,$versions_all, $language_all;
  GLOBAL $v_att, $v_template;

  //language selector $LNG_FORM[43]
  $v_att['lang_form_9'] = $LNG_FORM[9];
  select_box("select_language",$language_all,$lang_id,'',-1,$LNG_FORM[43]);
 
  //version selector
  $v_att['version_form_6'] = $LNG_FORM[6];
  select_box('select_box_version',$versions_all,$version_id,'',-1,$LNG_FORM[7]);


  $v_att['lang_form_11'] = $LNG_FORM[11];
  $v_att['lang_form_12'] = $LNG_FORM[12];
  $v_att['lang_form_13'] = $LNG_FORM[13];
  $v_att['lang_form_16'] = $LNG_FORM[51];

  //only display all for logged users (top prevent search bots and other pset from triggering
  if ( isset($_SESSION['userId']) ) 
  { $v_att['lang_form_14'] = $LNG_FORM[14];
    $v_att['lang_form_15'] = $LNG_FORM[52];
  }
  
  $v_att['submit_export'] = $LNG_FORM[15];
  
  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;

  // info text
  if ( isset($_SESSION['userId']) ) $message4 = sprintf($LNG_WRAP[4], $LNG_FORM[14]);
  else                              $message4 = "";
  
  info_box ($LNG_WRAP[3], $LNG_WRAP[12], $LNG_WRAP[21], $message4, $css_class = '', $css_style = 'width:700px;'); 
}

////////////////////////////////////////////////////////////////////////////////
//this generates one translation file
function generate_translation_file_for_language ($choice,$lang_id, $version_id, $target_path)
{   GLOBAL  $LNG_WRAP;
       
    //detect output encoding
    $err = db_query ("SELECT lng_coding, lang_code2, font1 FROM `languages` WHERE `language_id`='".$lang_id."';");
    $row = db_fetch_array($err);
    //write to file  
    $target_encoding = "UTF-8";
    if ($choice != "save") $target_encoding = $row['lng_coding'];
    $m = $lang_id;      
    if ( strlen($lang_id) > 2 ) $m = $row['lang_code2'];
    $font1 = $row['font1'];

    if ( !is_dir($target_path ) )  mkdir($target_path, 0775); 
    $file_name = $target_path."/".$m.".tab";    

    $res = generate_translation_text ($choice,$lang_id, $version_id, $target_encoding, $font1);
    if ($res != '') 
    { //prefix - § for UTF-8 files UTF "§" C2 A7
      $prefix = ($target_encoding == "UTF-8")?"\xC2\xA7":"";
      $fp=fopen($file_name,"wb");    
      fwrite($fp, $prefix . $res);
      fclose($fp); 
      $is_ok = 'OK';
    } else 
    { if ( file_exists($file_name) ) unlink($file_name); 
      $is_ok = 'empty';
    }
    print_line ("<p class='tight'>".$LNG_WRAP[1]." " . $lang_id . ".tab > $is_ok</p>");

    return $file_name;
}

function generate_language_helpfile_pack (&$lang_id, $pfad, $setid)
{   GLOBAL $versions_all;
    $m = $lang_id;      
    if ( strlen($lang_id) > 2 ) $m = db_one_field_query ("SELECT `lang_code2` FROM `languages` WHERE `language_id`='".$lang_id."';");  

    $pack_name = $m.'_'.urlencode($versions_all[$setid]).".zip";

    $ok = zip_target_directory ($pfad.$setid."/".$m, $pfad, $pack_name);
    if ($ok) return $pfad.$pack_name;
    else return '';
}


////////////////////////////////////////////////////////////////////////////////
//target path is a target for file to download
function generate_language_pack ($choice,$version_id)
{     
    GLOBAL $versions_all,$language_all,$tabpfad, $savpfad, $LNG_WRAP, $LNG_LANGUAGE;
    if ($choice=="save") 
    { $pfad = $savpfad;
      $setpfad = $pfad.$version_id."/";
      $pack_name = "save_pack-" . urlencode($versions_all[$version_id]) . ".zip";
      $obj_list  = $setpfad.'_objectlist.dat';
    } else  
    { $pfad = $tabpfad;
      $setpfad = $pfad.$version_id."/";
      $pack_name = "language_pack-" . urlencode($versions_all[$version_id]) . ".zip";
      $obj_list  = $setpfad.'_objectlist.txt';
    }

    //fetch all languages
    foreach ($language_all as $lang_id => $lang_name )  
    { if (($version_id == HELP_BASE__SET_ID or
           $version_id == HELP_EXTEN_SET_ID) and $choice=="all" )
      { generate_help_files ($lang_id, $version_id,$setpfad, 0 );
        print_line ("<p class='tight'>".$LNG_WRAP[19]." ".$lang_name . "</p>");
      } else
      { //generate the tab file for given language to defined location
        generate_translation_file_for_language ($choice,$lang_id, $version_id, $setpfad);
        if ( $version_id == BASE_TEXTS_SET_ID and $choice=="all" ) 
        { print_line ("<p class='tight'>".$LNG_WRAP[19]." ".$lang_name . "</p>");
          generate_help_files ($lang_id,HELP_BASE__SET_ID, $setpfad, 0 ); 
        } elseif ( $version_id == EXTE_TEXTS_SET_ID and $choice=="all" ) 
        { print_line ("<p class='tight'>".$LNG_WRAP[19]." ".$lang_name . "</p>");
          generate_help_files ($lang_id, HELP_EXTEN_SET_ID,$setpfad, 0 ); 
        }
      }
    }

    generate_userlist($version_id,$setpfad);
    ob_export_object_list($version_id,$obj_list);
    if ($choice=="save") 
    {  echo '<p>Save of Suggestion</p>';
       foreach ($language_all as $lang_id => $lang_name )  
       { generate_translation_file_for_language ('save-sugge',$lang_id, $version_id, $setpfad.'suggestions/');
       }
    }
    
    $ok = zip_target_directory ($setpfad,$pfad, $pack_name);
    if ($ok) return $pfad.$pack_name;
    else return '';
}

////////////////////////////////////////////////////////////////////////////////
///////////////////////////print translation file///////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// export requested
// hook - only work for this special post data - tied to button value
$lang_id    = select_box_read_language();
$version_id = select_box_read_version();
if (isset($_POST["submit"]) or isset($_GET ["choice"])) 
{  if ($_POST["submit"]==$LNG_FORM[15])
  { $choice = "unknown";
    if     (isset($_POST["choice"])) $choice=$_POST["choice"];
    elseif (isset($_GET ["choice"])) $choice=$_GET ["choice"];

    //user input check - 255 = default
    //set choice to uknown
    //vid must be set
    if ($version_id == 255) $choice = "unknown";
    //lang type must be set (except for language pack option)
    if ($lang_id == 255 AND $choice != "all" and $choice != "save") $choice = "unknown";
    if ($lang_id != 255 and $version_id == TRANSLATOR_SET_ID)      $choice = "unknown";

    if ( in_array($lang_id, $lang_fontsfiles['wenquanyi']) ) {
      print_line ("<p>".$LNG_WRAP[5]." <a href='../data/".$lang_fonts['cn']."'>".$lang_fonts['cn']."</a> ( ~".$lang_fonts['cn_byte']." ) ".$LNG_WRAP[6]."</p>");
    }
    if ( in_array($lang_id, $lang_fontsfiles['cyr'] ) ) {
      print_line ("<p>".$LNG_WRAP[5]." <a href='../data/".$lang_fonts['cyr']."'>".$lang_fonts['cyr']."</a> ( ~".$lang_fonts['cyr_byte']." ) ".$LNG_WRAP[6]."</p>");
    }

    if ($choice=="screen")
    {   //generates complete translation file to the variable coded using INTERNAL_ENCODING
        //send it to screen - after proper coding update
      echo "<pre>" . htmlentities(generate_translation_text ($choice,$lang_id, $version_id, "UTF-8","screen"), ENT_QUOTES, "UTF-8") . "</pre>"."\n";
    } elseif ($choice=="file" or $choice=="suggestion")
    { if ($choice=="suggestion") $pfad = $sugpfad;
      else                       $pfad = $tabpfad;
      if (($version_id == HELP_BASE__SET_ID or
           $version_id == HELP_EXTEN_SET_ID))
      { generate_help_files ($lang_id, $version_id,$pfad.$version_id."/", 1 );
        $file_name = generate_language_helpfile_pack ($lang_id, $pfad, $version_id);
        if ($file_name != '') print_line ('<h2><a href="'.$file_name.'">'.$LNG_WRAP[9].': '.basename($file_name).'</a></h2>', 1);
      } else
      { //generate file to target location
        $file_name = generate_translation_file_for_language ($choice,$lang_id, $version_id, $pfad.$version_id."/");
        print_line ('<h2><a href="'.$file_name.'">'.$LNG_WRAP[7].': '.basename($file_name).'</a></h2>', 1);
        info_box ($LNG_WRAP[8]);
      }
    } elseif ( $choice=="all" and $version_id == TRANSLATOR_SET_ID)
    { include ('./translator_export.php');
    } elseif (($choice=="all" and $version_id != TRANSLATOR_SET_ID) or $choice == "save")
    {   //generate complete language pack for given version
        $file_name = generate_language_pack ($choice,$version_id);
        if ($file_name != '') print_line ('<h2><a href="'.$file_name.'">'.$LNG_WRAP[9].': '.basename($file_name).'</a></h2>', 1);
    } else
    { //wrong post data
      print_line ("<h2>".$LNG_WRAP[11]."</h2>", 1);
      print_line ("<p class='tight'>".$LNG_WRAP[10]."</p>");
    }


    print_line ("<h3 class='center'><a href='main.php?lang=".$st."&page=wrap'>".$LNG_MAIN[19]."</a></h3>");
    print_line ("<h3 class='center'><a href='main.php'>".$LNG_MAIN[20]."</a></h3>");

  }
}
////////////////////////////////////////////////////////////////////////////////
////////////////////no data posted - ask for them///////////////////////////////
////////////////////////////////////////////////////////////////////////////////
else selection_menu ($version_id,$lang_id);

?>
