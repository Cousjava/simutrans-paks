<?php
$title='File_upload';
include("./tpl_script/header.php");
$u_level = array('gu','painter','pakadmin','admin');

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

include('./tpl_script/setadmin_links.php');

include ('./include/translations.php');
include ('./include/select.php');

$phpFileUploadErrors = array(
    0 => '0 : There is no error, the file uploaded with success',
    1 => '1 : The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => '2 : The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => '3 : The uploaded file was only partially uploaded',
    4 => '4 : No file was uploaded',
    6 => '6 : Missing a temporary folder',
    7 => '7 : Failed to write file to disk.',
    8 => '8 : A PHP extension stopped the file upload.',
);

// im file_upload erlaubte Datei Typen
$ext_tab = array('.png','.jpg','.jpeg','.zip','.pdf','.dat','.pak','.sve','.css','.mpg','.mp4','.mpeg');

////////////////////////////////////////////////////////////////////////////////
//  $lang_name=$LNG_LANGUAGE[$lang_id];
function user_edit_languages()
{ GLOBAL $LNG_LANGUAGE, $user;
  // create user lang array
  $sql = "SELECT * FROM `translate` WHERE `translator_user_id`='".$user."'";
  $query = db_query($sql);
  $user_lang = array();
  while ($row=db_fetch_array($query)) {
       $l_id= $row['lng_tr_language_id'];
       $user_lang[$l_id] = $LNG_LANGUAGE[$l_id];
  }  
  db_free_result($query);
  return $user_lang;
}

function help_file_namen($version_id) 
{ if (!is_numeric($version_id) or $version_id < 0) die("help_file_namen version_id error");
  $tab = array();
  $sql = "SELECT obj_name FROM `objects` WHERE `version_version_id`=".$version_id." ORDER BY obj_name COLLATE utf8_unicode_ci";
  $query = db_query($sql);
  while ($row=db_fetch_array($query))
  { $tab[] = $row['obj_name'];
  }
  db_free_result($query);
  return $tab;
}

////////////////////////////////////////////////////////////////////////////////
// main prg
////////////////////////////////////////////////////////////////////////////////
  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file   
  $v_template->parseFile('./tpl/file_upload.htm');  
  // ----- Prepare data
  $v_att = array();
  //prints page title
  $v_att['page_title'] = $page_titel[$title];

  $language        = select_box_read_language();
  $version_auswahl = select_box_read_version();

  $dir_auswahl = '';  
  $dir_tab = array();
  if (isset($_SESSION['dir_tab']) and
      $version_auswahl != HELP_BASE__SET_ID and
      $version_auswahl != HELP_EXTEN_SET_ID)
  { $dir_tab = $_SESSION['dir_tab'];
    $dir_auswahl     = select_box_read('select_box_webdir',$dir_tab,255,-2);
  }


if ($version_auswahl != 255 and isset($_POST['load_up']) and $_POST['load_up'] == $LNG_HEAD[23])
{ for ( $x = 1; $x <= 8; $x++) 
  { $error    = $_FILES['file_name_'.$x]['error'];
    $name     = $_FILES['file_name_'.$x]['name']; // Originalname der Datei
    $tmp_name = $_FILES['file_name_'.$x]['tmp_name']; // Uploadname der Datei
    $size     = $_FILES['file_name_'.$x]['size']; // Größe der Datei
    $type     = $_FILES['file_name_'.$x]['type']; // Dateityp (.htm, .gif usw.)

    if ( empty($_FILES['file_name_'.$x]['name']) ) continue;
    if ($error != 0)
    { $v_att['value_message']['messages'][]['message'] = $name.' -> PHP Error:'.$phpFileUploadErrors[$error];
      continue;
    }
    $ext_tmp = explode(".", $name);
    if (count($ext_tmp) != 2)
    { if (count($ext_tmp) == 1) $error = 33;
      else                      $error = 28;
      $v_att['value_message']['messages'][]['message'] = $name.' -> '.$LNG_MANAGE[$error];
      continue;
    }
    $ext = ".".strtolower($ext_tmp[1]);
    if (!in_array($ext,$ext_tab))
    { $v_att['value_message']['messages'][]['message'] = $name.' -> '.$LNG_MANAGE[30].implode(' ',$ext_tab);
      continue;
    }
    $name = $ext_tmp[0];
    if ($version_auswahl == HELP_BASE__SET_ID 
     or $version_auswahl == HELP_EXTEN_SET_ID)
    { $name = $_POST['help_filename_'.$x];
      if (!in_array($name,help_file_namen($version_auswahl)))           $error = 23;
      if ($ext != '.png')                                               $error = 32;
    } elseif (preg_match('#^[a-zA-Z0-9_-]{1,20}$#',$name) != 1)         $error = 24;
    if ($size < 10)                                                     $error = 29;

    if ($error != 0) 
    { $v_att['value_message']['messages'][]['message'] = $name.' -> '.$LNG_MANAGE[$error];
      continue;
    }

    $path = $webpfad.$version_auswahl.'/';
    if ($dir_auswahl != $LNG_MANAGE[27] and $dir_auswahl != 255) $path .= $dir_auswahl.'/';
    
    if ($version_auswahl == HELP_BASE__SET_ID) $path = $htmlpfad  .$language."/img/";
    if ($version_auswahl == HELP_EXTEN_SET_ID) $path = $htmlexpfad.$language."/img/";
    if ( !is_dir($path) ) mkdir($path, 0775); 
      
 //   echo "test:".$path.'#'.$name.'#'.$tmp_name.'#'.$size.'#'.$type.'#'.$ext.'<br>';

    $neuerDateiname = $path.$name.$ext;
    
    $log_text = 'store_file';
    if (file_exists($neuerDateiname)) 
    { unlink($neuerDateiname);
      $log_text = 'replace_file';
    }
    if(copy($tmp_name, $neuerDateiname) )    echo  $name.$ext." = OK <br />";

    // update log
    $t = date("Y-m-d H:i:s", time());
    // Date and Time | User | Object type | Object name | Language | Message | Object Id
    $data = $t."|".$user."|".$dir_auswahl."|".$name.$ext."|".$language."|". $log_text."|"."\n";
    write_log($version_auswahl, $data);
  }
}

select_box_version($version_auswahl); 
$v_att['submit'] = $LNG_FORM[45];

$v_att['max_upload'] = $LNG_MANAGE[31].ini_get('upload_max_filesize');

if ($version_auswahl != 255)
{ if ($version_auswahl == HELP_BASE__SET_ID 
   or $version_auswahl == HELP_EXTEN_SET_ID)
  { $v_att['select_language']['titel'] = $LNG_FORM[9];
//  select_box("select_language",$language_all,$language,'',-1,$LNG_FORM[10]);
    select_box("select_language",user_edit_languages(),$language,'',-1,$LNG_FORM[10]);
    if ($language != 255)
    { $filenamen = help_file_namen($version_auswahl);
      select_box('select_file',$filenamen,'','',-2); 
      for ( $x = 1; $x <= 8; $x++) 
      { $v_att['help_table']['line'][$x]['file_name_'] = 'file_name_'.$x;
        $v_att['help_table']['line'][$x]['select_file'] = $v_att['select_file'];
        $v_att['help_table']['line'][$x]['select_file']['help_filename'] = 'help_filename_'.$x;
      }
      $v_att['help_table']['load_up'] = $LNG_HEAD[23];
    }
  } else
  { $path = $webpfad.$version_auswahl;
    if (!is_dir($path))  $v_att['value_message']['messages'][]['message'] = $LNG_MANAGE[23];
    else 
    { $dir_tab = LoadFileList($path,'','dir');
      $dir_tab[] = $LNG_MANAGE[27];
      $_SESSION['dir_tab'] = $dir_tab;
      $v_att['select_box_webdir']['titel'] = $LNG_MANAGE[26];
      select_box('select_box_webdir',$dir_tab,$dir_auswahl,'',-2,$LNG_MANAGE[26]); 
      for ( $x = 1; $x <= 8; $x++) 
      { $v_att['help_table']['line'][$x]['file_name_'] = 'file_name_'.$x;
      }
      $v_att['help_table']['load_up'] = $LNG_HEAD[23];
    }
  }
}

$v_result = $v_template->generate($v_att, 'string');
echo $v_result;
unset($v_att);     

include("./tpl_script/footer.php");

?>
