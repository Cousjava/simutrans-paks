<?php
require_once('./include/parameter.php');
require_once('./include/langpara.php');
      
/*
    SimuTranslator
    General Functions
    Tomas Kubes 2005
*/

//path for temporary files (accessible to outside users - be carful!)


////////////////////////////////////////////////////////////////////////////////
//outputs indented line of html code
function print_line ($line, $indent = 0)
{
    for ($i = 0;  $i < $indent; $i++) echo '    ';
    echo $line;
    echo "\n";
}

function line_css($i) {
      if ( ($i % 2) == 0 ) {
        return 'bg_trans';  
      } else {
        return 'bg_grey';  
      } 
}


////////////////////////////////////////////////////////////////////////////////


function get_set_enabled ($user_id)
{
    $version_q = "SELECT `set_enabled` FROM `users` WHERE `u_user_id`='".$user_id."'";
    $version_r = db_query($version_q);
    
    //result should contain one row with one filed  
    // $name = (db_fetch_row($version_r));  
    $n = db_fetch_row($version_r);
    if ( $n[0] == '' || empty($n[0]) || $n[0] == NULL ) { 
        $name = array('all'); 
    } else { 
        $name = unserialize($n[0]); 
        db_free_result ($version_r);
        if ( !is_array($name) ) { $name = array(); }
    }  
    
    return $name;  
}

function get_user_langs ($user_id)
{ 
    global $LNG_LANGUAGE;

    $version_q = "SELECT `lng_tr_language_id` FROM `translate` WHERE `translator_user_id`='".$user_id."' ORDER BY `lng_tr_language_id`";
    $version_r = db_query($version_q);
   
    //result should contain one row with one filed  
   // $name = (db_fetch_row($version_r));   
    $n = array();
    $t = 0;
    $br = 0;  
    while ($row = db_fetch_array($version_r))
    {
       /* if ( $br == 5 ) {
    $n[$t] = $row[0].'<br />';
    $br = 0;
  } else {
    $n[$t] = $row[0];
    $br++;
  }  */
      $n[$t] = ' '.$row['lng_tr_language_id'];
      $t++;
    }
    db_free_result ($version_r);
    
    if ( count($LNG_LANGUAGE) == $t ) { $f = 'all'; } else { $f = implode(',', $n); } 
    
    return $f;  
}


////////////////////////////////////////////////////////////////////////////////
//dispalys info box with standardized formating
//has 2 optional paragraphs
function info_box ($message1, $message2 = '', $message3 = '', $message4 = '', $css_class = '', $css_style = '')
{
  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/info_box.htm');
  // ----- Prepare data
  $v_att = array();

  if ($css_class != "")
  {
    if ($css_class != "-") 
    {
      $v_att['css_class'] = $css_class; 
    }
  } else {
    $v_att['css_class'] = "width600";
  }
  if ($css_style != '')
  {
    $v_att['css_style'] = $css_style;
  }

  $v_att['message_0'] = $message1;

  if ($message2 != "")
  {
    $v_att['messagebox_1']['message_1'] = $message2;
  }
  if ($message3 != '')
  {
    $v_att['messagebox_1a']['message_1'] = $message3;
  }
  if ($message4 != '')
  {
    $v_att['messagebox_2']['message_2'] = $message4;
  }    
    
  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;
  /*
    print_line ('<div class="width600">');

    print_line ('<p class="justified">', 1);
    print_line ('<img class="info" src="img/excalamtion.gif" width="34" height="44" alt="info" />', 2);
    print_line ($message1, 2);
    print_line ('</p>', 1);
    
    if ($message2 != "")
    {
        print_line ('<p class="justified">'.$message2.'</p>', 1);
    }
    if ($message3 != '')
    {
        print_line ('<p class="justified">'.$message3.'</p>', 1);
    }    
    print_line ('</div>');   
    */
}

////////////////////////////////////////////////////////////////////////////////
//this function compares user level with minimal level
//and returns true, if user level is => minimal
//'guest','tr1','painter','tr2','gu','pakadmin','admin'
function compare_userlevels ($minimal_level, $user_level)  
{
    //for minimal level guest, we succeed always
    //if ($minimal_level == 'guest') return TRUE;
    
   
    //admin can go anywhere...
    if ($user_level == 'admin') return TRUE;
  
    if ( in_array($user_level, $minimal_level) )
    {
        return TRUE;
    } else {
        return FALSE; 
    } 


    //if we got here (wierd) some problem, be secure
    return FALSE; 

}



/* list disabled laguages from set */
function get_lang_disabled($setid) {
  $data = array();
  // select Set
  $sql = "SELECT * FROM `versions` WHERE `version_id`=$setid;";
  $query = db_query($sql);
  $row = db_fetch_array($query);  
  if ( !empty($row['lng_disabled']) && $row['lng_disabled'] != '' && strlen($row['lng_disabled']) > 2 ) {
    $data = explode("|", $row['lng_disabled']);       
  } elseif ( $row['lng_disabled'] != '' ) {
    $data[0] = $row['lng_disabled'];
  } 
  return $data;
}


////////////////////////////////////////////////////////////////////////////////
// write data as a file in the temp dir with a random filename
function write_temp_file($fn_ext,$data)
{ global $tempfilepfad;

  $file_name = $tempfilepfad.date("Y-m-d_H-i-s_", time()).mt_rand(1000, 9999).'_'.$fn_ext;

  $fp=fopen($file_name,"wb");
  fwrite($fp, $data);
  fclose($fp);

  return $file_name;
}

////////////////////////////////////////////////////////////////////////////////
//this function zips content of target directory to given zip file
//and deletes the directory
//returns TRUE on success
function zip_target_directory ($target_directory, $output_zip_file_path, $output_zip_file_name)
{    
    /*
   PhpConcept Library - Zip Module 2.8.2
   --------------------------------------------------------------------------------
   License GNU/LGPL - Vincent Blavet - August 2009
     http://www.phpconcept.net
   --------------------------------------------------------------------------------
    */
  include_once ('include/pclzip/pclzip.lib.php');

  global $LNG_WRAP;
    //user message
    print_line ('<p class="tight">'.$LNG_WRAP[16].'</p>', 1);

    $zip_name = $output_zip_file_name;
    $zip_path = $output_zip_file_path.$zip_name;
    
    //test if the zip file exists (ie from previous export), delete it
    if (is_file($zip_path)) unlink($zip_path);
    // create new PclZip object
    if (!$archive = new PclZip($zip_path)) echo 'Error : '.$archive->errorInfo(true);

    $v_list = $archive->create($target_directory, PCLZIP_OPT_REMOVE_PATH, $target_directory);

    if ($v_list == 0)
    {  //variable above holds return value of the system command, do not continue if else than 0
        echo '<h1>'.$LNG_WRAP[17].'</h1>';
        echo 'Error : '.$archive->errorInfo(true);
        return FALSE;
    } 
    //done
    print_line ('<p class="tight">'.$LNG_WRAP[18].'</p>', 1);
    return TRUE;
}

function verzeichnis_del($verz) {
  if ( file_exists($verz) ) {
    if ($dp = @opendir($verz)) {
      $ds=array();
      while (($file = readdir($dp)) !== false) {
        $pfile=$verz.'/'.$file;
        if (($file !== '.') && ($file !== '..') && ($file !== 'index.php') && is_dir($pfile)) {
          $ds[]=$pfile;
        }
        if ( ($file !== ".") && ($file !== '..') && ($file !== 'index.php') && is_file($pfile) ) {
          unlink($pfile);
        }
      }
      closedir($dp);
      foreach ( $ds as $x) verzeichnis_del($x);
      foreach ( $ds as $x) rmdir($x);
    }          
  }
}

function write_rss($data, $fd)
{ $file = "../data/rss/".$fd;
  $logfile = fopen($file, "a");  // neuen Eintrag an Datei anhängen
  fputs($logfile, $data);
  fclose($logfile);
}

function rss_update($set, $data) 
{ $r = explode("|", $data);
  if (isset($r[4]) and $r[4] <> "xx" ) 
  { $objects = $r[0]."|".$r[2]."|".$r[3]."|".$r[4]."|".$r[5]."|".$set."|".$r[6];
    write_rss($objects, "rss_items_roh");
  }
}


function write_log($set, $data) 
{ global $no_rss;
  $file = '../data/set_'.$set.'.log';
  $logfile = fopen($file, "a");  // neuen Eintrag an Datei anhängen
  fputs($logfile, $data);
  fclose($logfile);

  if ( !in_array($set, $no_rss)) rss_update($set, $data);
}

function web_file_write($version,$obj_name,$language,$trans_txt)
{ global $language_all,$webpfad,$LNG_MANAGE;
  if (!isset($language_all[$language]))       die("web_file_write language_id error");
  if (!is_numeric($version) or $version < 0)  die("web_file_write version_id error");
  if (preg_match('#^[a-zA-Z0-9_/-]{1,20}$#',$obj_name) != 1) { echo $LNG_MANAGE[24]; return; }
  $name = str_replace('.','',$obj_name);
  $dir_tab = explode('/',$obj_name);
  if (count($dir_tab) == 1) $obj_name = $dir_tab[0];
  elseif (count($dir_tab) == 2)
  { if (preg_match('#^[a-zA-Z0-9_-]{1,20}$#',$dir_tab[0]) != 1) { echo $LNG_MANAGE[25]; return; }
    if (is_dir($webpfad.$version.'/'.$dir_tab[0])) $obj_name = $dir_tab[0].'/'.$dir_tab[1];
    else { echo $LNG_MANAGE[25]; return; }
  } else { echo $LNG_MANAGE[25]; return; }
  $trans_txt = str_replace ('\n', "\n", $trans_txt);
  $file_name = $webpfad.$version.'/'.$name.'.html.'.$language;
  if ($version == 200 and $obj_name == 'index') $file_name = '../index.html.'.$language;
  $file = fopen($file_name,'wb');
  fputs($file,$trans_txt);
  fclose($file);
  if ($language == 'en') copy($file_name,$file_name.'-us');
  if ($language == 'de') copy($file_name,$file_name.'-de');
}

// shows the difference between two texts with html colors
function diff_string($alt,$neu)
{ $a = 0; $n = 0; $d ='';
  mb_internal_encoding("UTF-8");
  while (true)
  {
    if ($a >= strlen($alt) and $n < strlen($neu)) { $d .= '<font color="green" >'.substr($neu,$n).'</font>'; break; }
    if ($n >= strlen($neu) and $a < strlen($alt)) { $d .= '<font color="red" >'  .substr($alt,$a).'</font>'; break; }
    if ($a >= strlen($alt) or $n >= strlen($neu)) break;
    $su_a = mb_substr(substr($alt,$a),0,1);
    $su_n = mb_substr(substr($neu,$n),0,1);
    if ($su_a == $su_n) { $d .= $su_a; $a += strlen($su_a); $n += strlen($su_n); }
    else 
    { for ($l = 20; $l > 0; $l--)
      { if (strlen($d) > 100000) return $d."\n<br>d zu lang<br>\n";
        $pa = false; $pn = false;
        for ($dn = 0; $n+$dn+$l <= strlen($neu); $dn++)
        { $su_n = mb_substr(substr($neu,$n+$dn),0,$l);
          $pa = strpos($alt,$su_n,$a);
          if ($pa !== false) break;
        }
        for ($da = 0; $a+$da+$l <= strlen($alt); $da++)
        { $su_a = mb_substr(substr($alt,$a+$da),0,$l);
          $pn = strpos($neu,$su_a,$n); 
          if ($pn !== false) break;
        }
        if ($pa === false and $pn === false)
        { if ( $l > 1) continue;
          $z = mb_substr(substr($neu,$n),0,1);
          $d .= '<font color="orange">'.$z.'</font>';
          $a += strlen(mb_substr(substr($alt,$a),0,1));
          $n += strlen(mb_substr(substr($neu,$n),0,1));
          break;
        }
        if ($pa === false)
        { if ($da > 0) 
          { $d .= '<font color="red">'  .substr($alt,$a,$da).'</font>';
            $a += $da;
          }
          $d .= '<font color="green">'.substr($neu,$n,$pn-$n)."</font>\n"; 
          $n = $pn;
          break;
        }
        if ($pn === false) 
        { $d .= '<font color="red">'  .substr($alt,$a,$pa-$a).'</font>'; 
          $a = $pa;
          if ($dn > 0)
          { $d .= '<font color="green">'.substr($neu,$n,$dn).'</font>'; 
            $n += $dn;
          }
          $d .= "\n";
          break;
        } 
        if ($pn-$n <= $pa-$a)
        { if ($da > 0) 
          { $d .= '<font color="red">'  .substr($alt,$a,$da).'</font>';
            $a += $da;
          }
          if ($pn-$n > 0) $d .= '<font color="green">'.substr($neu,$n,$pn-$n)."</font>\n"; 
          $n = $pn;
          break;
        } else
        { $d .= '<font color="red">'  .substr($alt,$a,$pa-$a).'</font>'; 
          $a = $pa;
          if ($dn > 0)
          { $d .= '<font color="green">'.substr($neu,$n,$dn).'</font>'; 
            $n += $dn;
          $d .= "\n";
          }
          break;
        } 
      }
    }
  }
  return $d;
}

// html format for help file and factory details
function text_format($form,$box_typ,$text,$text_label)
{ if( $form == 'web' && $text != '' )
  { 
    $n = htmlentities($text, ENT_QUOTES, "UTF-8");
    $n = str_replace ('\n', "<br>\n", $n);
    return $n;
  } elseif ( $form == 'link' && $text != '' )
  { $n = '';
    $l = explode('\n',$text);
    foreach ($l as $link) if ($link != '')
    { $l_e = htmlentities($link, ENT_QUOTES, "UTF-8");
      if ( $box_typ != 'E')     $n .= $l_e."</font><br>\n";
      elseif (check_url($link)) $n .= '<font color="green">'.$l_e."</font><br>\n"; 
      else                      $n .= '<font color="red">'  .$l_e."</font><br>\n";
    }
    return $n;
  } elseif ( $form == 'html' && $text != '' ) return html_format($text);
  else return htmlentities($text_label, ENT_QUOTES, "UTF-8");

}

// html format for help file and factory details
function html_format($text) {
  //$t = html_entity_decode($text, ENT_QUOTES, "UTF-8");
  // del \n
  $t = str_replace ('\n', '', $text);
  // replease titel tag 
  $t = str_replace ('<title>', '<div class="title">', $t);
  $t = str_replace ('</title>', '</div>', $t);
  // replease it tag 
  $t = str_replace ('<it>', '<em class="it">', $t);
  $t = str_replace ('</it>', '</em>', $t);
  // replease p tag 
  $t = str_replace ('<p>', '<div class="htmlhelp">', $t);
  $t = str_replace ('</p>', '</div>', $t);
  
  $t = '<div id="help_file">'.$t.'</div>';
  
  return $t;
}

function check_url($url)
{ if (isset($_SESSION['url_is_good'])) $ok_tab  = $_SESSION['url_is_good'];
  else                                 $ok_tab  = array();
  if (isset($_SESSION['url_is_bad']))  $bad_tab = $_SESSION['url_is_bad'];
  else                                 $bad_tab = array();
  if (in_array($url,$ok_tab)) return true;
  if (in_array($url,$bad_tab)) return false;
  $file_headers = @get_headers($url);
  // echo "header link:".$file_headers[0]."<br>\n".$file_headers[1]."<br>\n";
  if(!$file_headers) return false; 
  if ( strpos($file_headers[0], ' 200 OK')
    or strpos($file_headers[0], ' 301 M')) 
  { $ok_tab[] = $url; 
    $_SESSION['url_is_good'] = $ok_tab;
    return true;
  } 
  $bad_tab[] = $url; 
  $_SESSION['url_is_bad'] = $bad_tab;
  return false;
}

function get_langs() {
     $verzeichnis = scandir ('./lang/', SCANDIR_SORT_ASCENDING);
     
     $not_Show = array('.', '..', '.htaccess','index.php');
     $langar = array();
     foreach ($verzeichnis as $dirorfile) 
     { if ( !in_array($dirorfile, $not_Show) ) 
       { $langar[] = $dirorfile;
       }
     }
     return $langar;
}



function LoadFileList($dir, $filter = '',$filetyp = 'all') {
  $verz=scandir($dir, SCANDIR_SORT_ASCENDING);
  
  $files = array();

  $not_Show = array('.', '..', '.htaccess','index.php');
  
  foreach ($verz as $dirorfile) 
  { if ( in_array($dirorfile, $not_Show) ) continue; 
    if ( $filter != '' and strpos($dirorfile, $filter)) continue; 
    if ($filetyp == 'dir'  and !is_dir ($dir.'/'.$dirorfile)) continue;
    if ($filetyp == 'file' and !is_file($dir.'/'.$dirorfile)) continue;

    $files[] = $dirorfile;
  }

  // sortiert nach lowercase läst die upercase aber stehen, es wird $files zurück gegeben
  $array_lowercase = array_map('strtolower', $files);
  array_multisort($array_lowercase, SORT_ASC, SORT_STRING, $files);

  return($files);
} 


?>
