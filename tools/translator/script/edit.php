<?PHP

//if ( $_SESSION['manage_lang'] ) { $manage_lang = $_SESSION['manage_lang']; } else { $manage_lang = array(); }
//if ( $_SESSION['manage_languser'] ) { $manage_languser = $_SESSION['manage_languser']; } else { $manage_languser = array(); }
/* so wie es programiert ist kann es nicht funktionieren deshalb tot gelegt
  // read exists sets in manage language
  $sql = "SELECT * FROM `lang_maintaint` WHERE `set_id`=".$version_auswahl.";";
  $query = db_query($sql);
  if ( db_num_rows($query) > 0 ) {
    while ( $row=db_fetch_array($query) ) {
      $manage_lang[] = $row['lang_id'];
      $manage_languser[$row['lang_id']] = array($row['data'], $row['data1'], $row['data2'] );
      //echo '<p>return data'.count($manage_lang).'</p>';
    }
  } else {
      $manage_lang = array(); 
      $manage_languser = array();
  }
  db_free_result ($query);

  $_SESSION['manage_lang'] = $manage_lang;
  $_SESSION['manage_languser'] = $manage_languser;

*/

/**********************************************************************
 * GENERATE HEADER AND INCLUDE SECTION
 **********************************************************************/


  //sets page title

  //thisp age is accessible to all users, but will behave differently for different types
  $title = 'Textedit';
  include ('./tpl_script/header.php');
  include ('./include/obj.php');
  include ('./include/translations.php');
  include ('./include/select.php');

// var $user, $st, $user_type is set in header  
  
  $show_tab = array();
  $show_tab['all'] = $LNG_EDIT[29];  // show all
  $show_tab['int'] = $LNG_EDIT[35];  // only show the language of the interface
if ($user != '')
{ $show_tab['emp'] = $LNG_EDIT[32];  // not show empty 
  $show_tab['usr'] = $LNG_EDIT[33];  // only show user language 
  $show_tab['mod'] = $LNG_EDIT[34];  // Show only changeable languages
}

$tr_ok = array();;

// resolve which object to display AND set  object, obj_name, version
// no matter if it is post variable (posted from edit.php) or get variable from other page
$current_ob_id     = 0;
if (isset($_GET['obj_id']) and $_GET['obj_id']!='')   $current_ob_id     = intval($_GET['obj_id']);
if (isset($_POST['obj_id']) and $_POST['obj_id']!='') $current_ob_id     = intval($_POST['obj_id']);
$current_obj_index = 0;
if (isset($_GET['index']))  $current_obj_index = intval($_GET['index']);
if (isset($_POST['index'])) $current_obj_index = intval($_POST['index']);

$show_auswahl = select_box_read('select_box_show',$show_tab,'all',-1);

// update object table
if ( isset($_POST['save_object']) && $_POST['save_object'] == $LNG_ADMIN[53] ) 
{ $new_name = $_POST['new_obj_name'];
  $new_type = $_POST['new_obj'];
  $new_note = NULL;
  $new_copr = NULL;
  if (isset($_POST['note_text'])) $new_note = $_POST['note_text'];
  if (isset($_POST['copyright'])) $new_copr = $_POST['copyright'];
  $tr_ok[] = ob_save_obj_parameter($current_ob_id, $new_name, $new_type,$new_note,$new_copr);
  
  foreach ($_POST as $key => $p_value) 
  { if (substr($key,0,6)=='p_upd_')
    { $p_name = substr($key,6);
      $tr_ok[] = ob_save_obj_property($current_ob_id, $p_name, $p_value);
    }
  }
} 

// read current object and if modifed reread the update
$errg= ob_read($current_ob_id);
if ( $errg === NUll ) 
  {   echo '<p class="red">'.$LNG_EDIT[22]."</p>\n";   
      include('./tpl_script/footer.php'); 
      die();
  }
$current_ob_name       = $errg->obj_name;
$current_ob_version    = $errg->version_version_id;
$current_ob_obj_type   = $errg->obj;
$current_ob_note       = $errg->note;
$current_ob_copyright  = $errg->obj_copyright;



$edit_page = $current_ob_obj_type;
if ($current_ob_obj_type == 'building')
{ //do a query for type - does not fail when no type is found (ie for depot)
  $bldg_type = db_one_field_query ("SELECT p_value FROM property WHERE p_name='type' AND having_obj_id='".$current_ob_id."' ", FALSE);

  if ($bldg_type == FALSE or $bldg_type == '') $bldg_type ='';
  else $edit_page = $bldg_type;
} 

$e_c = $edit_conf; // default if no version spezific found
if (isset($edit_conf_tab[$current_ob_version])) $e_c = $edit_conf_tab[$current_ob_version];
if (isset($e_c[$edit_page])) $current_edit_conf = $e_c[$edit_page];
else                         $current_edit_conf = $e_c['undefind'];
  //GENERATE USER CONFIGURATION
$no_translate_lang = get_lang_disabled($current_ob_version);


////////////////////////////////////////////////////////////////////////////////
//////////////////////////////START OF THE OUTPUT///////////////////////////////
////////////////////////////////////////////////////////////////////////////////

//generate html code for translation of object with specified $lang_id and for one databas field
function gen_translate($lang_id,$box_typ,$col_typ,$c,$f,$htmltxt,$edit_rows,$edit_cols)
{   global $LNG_EDIT, $current_ob_id, $current_ob_version, $current_ob_obj_type;
    global $v_att;
  
    $value_lb = $col_typ.'_'.$lang_id;
    $tr = tr_read($current_ob_id,$current_ob_version,$lang_id,$col_typ);
       
    $tr_text       = $tr[0];
    $suggestion    = $tr[1];
    $tr_text_label = $tr_text;

    if      ($box_typ == 'R') $iw = 22;
    else if ($box_typ == 'U') $iw = 22;
    else                      $iw = 24;
    if ($tr_text_label == '') $tr_text_label = $LNG_EDIT[$iw]; 

    ////////////////////////////////////////////////////////////////////////////
    ////////now display the translation box for the given language//////////////

    //translation text heading
    if      ($col_typ == 'd') $iw = 28;
    else if ($col_typ == 'l') $iw = 30;
    else if ($col_typ == 'h') $iw = 31;
    else if ($box_typ == 'R') $iw = 23;
    else if ($box_typ == 'U') $iw = 20;
    else                      $iw = 11;
    $v_att['lngbox'][$c]['trbox'][$f]['value_heading_1'] = $LNG_EDIT[$iw];

    //display current translation text
    $formattext = text_format($htmltxt,$box_typ,$tr_text,$tr_text_label);
    $formatsugg = text_format($htmltxt,$box_typ,$suggestion,$suggestion);
    if( $htmltxt == 'web' or $htmltxt == 'link' or $current_ob_obj_type == 'help_file')
    { $tr_text    = str_replace ('\n', "\n", $tr_text);
      $suggestion = str_replace ('\n', "\n", $suggestion);
    }

    //take care of html entities and dislay
    $v_att['lngbox'][$c]['trbox'][$f]['value_text'] = $formattext;
    if ( $suggestion != '' and $box_typ != 'U' and $box_typ != 'R') 
    { $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['value_heading'] = $LNG_EDIT[14];
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['value_text'] = $formatsugg;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['value_diff'] = diff_string(htmlentities($tr_text,    ENT_QUOTES, "UTF-8"),
                                                                                  htmlentities($suggestion, ENT_QUOTES, "UTF-8"));
   }

    if ( $box_typ == 'S' ) 
    { if ( $suggestion == '' ) $edit_suggestion = $tr_text; 
      else                     $edit_suggestion = $suggestion;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['unlogged']['value_edittext'] = htmlentities($edit_suggestion, ENT_QUOTES, "UTF-8");
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['unlogged']['value_rows'] = $edit_rows;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['unlogged']['value_cols'] = $edit_cols;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['unlogged']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['unlogged']['gui_submit'] = $LNG_EDIT[15];
      $v_att['lngbox'][$c]['trbox'][$f]['unlogged_button']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['unlogged_button']['gui_submit'] = $LNG_EDIT[15];
    }
    
    if ( $box_typ == 'E' and $suggestion != '' )
    { $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['logged']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['logged']['gui_submit_1'] = $LNG_EDIT[16];
      $v_att['lngbox'][$c]['trbox'][$f]['suggestion']['logged']['gui_submit_2'] = $LNG_EDIT[26];
      $v_att['lngbox'][$c]['trbox'][$f]['logged_button']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['logged_button']['gui_submit_1'] = $LNG_EDIT[16];
      $v_att['lngbox'][$c]['trbox'][$f]['logged_button']['gui_submit_2'] = $LNG_EDIT[26];
    }
    
    if ( $box_typ == 'E' ) //logged user
    { $v_att['lngbox'][$c]['trbox'][$f]['trtext']['value_rows'] = $edit_rows;
      $v_att['lngbox'][$c]['trbox'][$f]['trtext']['value_cols'] = $edit_cols;
      $v_att['lngbox'][$c]['trbox'][$f]['trtext']['value_edittext'] = htmlentities($tr_text, ENT_QUOTES, "UTF-8");
      $v_att['lngbox'][$c]['trbox'][$f]['trtext']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['trtext']['gui_submit'] = $LNG_EDIT[18];

      $v_att['lngbox'][$c]['trbox'][$f]['trtext_button']['value_langid'] = $value_lb;
      $v_att['lngbox'][$c]['trbox'][$f]['trtext_button']['gui_submit'] = $LNG_EDIT[18];
    }
    if ( $box_typ == 'D' ) // message disabled
    { $v_att['lngbox'][$c]['trbox'][$f]['message_langdisabled']['value_message'] = $LNG_EDIT[25];
    }
}

//generate html code for translation of object with specified $lang_id only
function gen_lang_box($lang_id,  $box_typ)
{   global $user, $LNG_LANGUAGE, $LANG_NAMEN, $LNG_EDIT, $current_ob_id, $current_ob_version;
    global $v_att, $current_edit_conf, $c, $current_ob_obj_type, $bldg_type;
   
    if (!isset($c)) $c = 0;
    //border box open
    if ($box_typ != 'U' and $box_typ != 'R')
    { $v_att['lngbox'][$c]['langid']['value_langid'] = $lang_id;
      $v_att['languages'][]['lang_id'] = $lang_id;
      $v_att['languages'][]['lang_name'] = $LANG_NAMEN[$lang_id];
    }
    if ($box_typ == 'E')
    {  // show update all button
       $w ='_'.$bldg_type; if ($w == '_') $w = '';
       $v_att['top_save']['value_helplink'] = $current_ob_obj_type.$w;
       $v_att['top_save']['tr_hint'] = $LNG_EDIT[17];
       $v_att['top_save']['value'] = $LNG_EDIT[19];
       $v_att['bottom_save']['value'] = $LNG_EDIT[19];
    }
       
    $v_att['lngbox'][$c]['value_langname'] = $LNG_LANGUAGE[$lang_id];
    
    if ($user != '')
    { $tr_author = tr_read_author($current_ob_id,$current_ob_version,$lang_id);
      $v_att['lngbox'][$c]['lastuser']['value_heading_2']   = $LNG_EDIT[12];
      $v_att['lngbox'][$c]['lastuser']['value_lastuser']    = $tr_author[0];
      $v_att['lngbox'][$c]['lastuser']['value_lastchange']  = substr($tr_author[1],0,10);
    }
    
    /*
    $show_managelang = 0;
    // check for show suggestion input
    if ( $user == '' || $user_edit == 0 || $no_edit_lang == 1 ) {
      $show_suggestion = 1;
    } elseif ( in_array($lang_id, $manage_lang) ) { 
      $v_att['lngbox'][$c]['lang_manage']['lang_manage_t'] = 'Language is managed';
      // *** add icons for info and message
      $v_att['lngbox'][$c]['lang_manage']['lang_manage_mail'] = 'send a message to language managers';
      $v_att['lngbox'][$c]['lang_manage']['value_langid'] = $lang_id;
      $v_att['lngbox'][$c]['lang_manage']['value_setid'] = $current_ob_version;
      $v_att['lngbox'][$c]['lang_manage']['lang_manage_info'] = 'language is managed from users';
      $show_suggestion = 1; 
      if ( in_array($user, $manage_languser[$lang_id]) ) { $show_managelang = 1; }
     
    } else {
      $show_suggestion = 0; 
    } */
    
    for ($f=0; $f < count($current_edit_conf); $f+=4)
    { if ($f == 0) $col_typ = 't';
      else         $col_typ =$current_edit_conf[$f];
      gen_translate($lang_id, $box_typ,$col_typ,$c,$f,
                    $current_edit_conf[$f+1],  // $htmltxt
                    $current_edit_conf[$f+2],  // $edit_rows 
                    $current_edit_conf[$f+3]); // $edit_cols
    }
    $c++;
}

// generate html code for entire page
function gen_all($tr_ok_tab)
{   global $st, $user, $LNG_EDIT, $LNG_MAIN, $LNG_LOAD3, $LNG_LANGUAGE, $current_ob_id, $current_ob_version;
    global $v_att, $current_edit_conf, $current_obj_index,$language_all,$show_tab,$show_auswahl, $no_translate_lang,$maintainter;  

    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file
    $v_template->parseFile('./tpl/'.$current_edit_conf[0].'.htm');  
     // ----- Prepare data
    $v_att = array();
 
    foreach ($tr_ok_tab as $tr_ok)
    { if (!is_numeric($tr_ok)) $e = $tr_ok;
      elseif ($tr_ok <  11)
      { $e = $LNG_LOAD3[10];                  // successful saved
        if ($tr_ok == 3) $e = $LNG_LOAD3[11]; // written_as_suggestion
      } elseif ($tr_ok <  20) 
      { $e = $LNG_LOAD3[20];                  // text already present
      } else $e = $LNG_LOAD3[$tr_ok];         // all errors
      $v_att['value_message']['messages'][]['message'] = $e;
    }

    if (isset($_SESSION['search_result_tab']))
    // generates html code for "prev" button if there are objects before
    { if ($current_obj_index > 0 and $current_obj_index < $_SESSION['search_result_len']) 
      { $obj_i_p = $_SESSION['search_result_tab'][$current_obj_index-1];
        $errg= ob_read($obj_i_p);
        $obj_p_name = $errg->obj_name;
        $v_att['gen_prev']['value_obj_id'] = $_SESSION['search_result_tab'][$current_obj_index-1];
        $v_att['gen_prev']['value_index'] = $current_obj_index-1;
        //display object button - ony 40 chars from name and make sure it is in html acceptable form 
        $v_att['gen_prev']['value_obj_name'] = substr (htmlentities($obj_p_name, ENT_QUOTES), 0, 40);
      }

      // generates html code for "next" button if there are objects after
      if ($current_obj_index < $_SESSION['search_result_len']-1)
      { $obj_i_n = $_SESSION['search_result_tab'][$current_obj_index+1];
        $errg= ob_read($obj_i_n);
        $obj_n_name = $errg->obj_name;
        $v_att['gen_next']['value_obj_id'] = $_SESSION['search_result_tab'][$current_obj_index+1];
        $v_att['gen_next']['value_index'] = $current_obj_index+1;
        //display object button - ony 40 chars from name and make sure it is in html acceptable form 
        $v_att['gen_next']['value_obj_name'] = substr (htmlentities($obj_n_name, ENT_QUOTES), 0, 40);
      }
    }

    select_box('select_box_show',$show_tab,$show_auswahl,'',-1); 

    $v_att['sublinks']['link1'] = $LNG_MAIN[3];
    $v_att['sublinks']['link2'] = $LNG_MAIN[24];

 
    $v_att['obj_id']      = $current_ob_id;
    $v_att['value_index'] = $current_obj_index;
    
    $user_edit = 0; // user is not allowed edit set, 1 = allowed 
    if ($user != '' and in_array($current_ob_version , $_SESSION['set_enabled']))
    { $set_edit = $_SESSION['set_edit'];
      if ( in_array($current_ob_version, $set_edit) || $set_edit[0] == 'all' ) $user_edit = 1;
    } 
    
    //generate the master translation text (non-editable box)
    if ($user == '')     gen_lang_box('en', 'R'); 
    else
    {  // user reference language
       $res = $_SESSION['ref_lang'];
       if ( $res != '' ) gen_lang_box($res, 'U'); 
       else              gen_lang_box('en', 'R'); 
    }

    //translation boxes
    //display all other regular texts
    if ($user_edit == 1) // user allowed edit set 
    {  //generate regular translations
       $user_lng = array(); // get user allowed edit language
       foreach ($_SESSION['edit_lang'] as $l) if (!in_array($l,$no_translate_lang)) $user_lng[$l] = $LNG_LANGUAGE[$l];
       natcasesort($user_lng);
       // show user sort languages first ( config2 user preferences )
       $user_lang_sort = array($st);
       if ( isset($_SESSION['config2']) 
            and strlen(trim($_SESSION['config2'])) > 1 
            and $show_auswahl != 'int')
       { $w = explode(',', $_SESSION['config2'] );
         $user_lang_sort = array();
         foreach ($w as $e) $user_lang_sort[] = trim($e);
       }
       foreach ( $user_lang_sort as $slk )
       { if (isset($user_lng[$slk])) gen_lang_box($slk, 'E');  
       } 
       // show all other languages for edit
       if ($show_auswahl != 'int' and $show_auswahl != 'usr')
       { foreach ($user_lng as $slk => $sln ) if ( !in_array($slk, $user_lang_sort ) )
         { if ($show_auswahl == 'emp')
           { $empty = tr_test_empty($current_ob_id,$current_ob_version,$slk);
             if (!$empty) gen_lang_box($slk, 'E');
           } else         gen_lang_box($slk, 'E'); 
         }
         // show not edit languages
         foreach ($language_all as $slk => $sln ) if (!isset($user_lng[$slk]))
         {                                                  $box_typ = 'N';
           if (in_array($current_ob_version, $maintainter)) $box_typ = 'E';
           if (in_array($slk,$no_translate_lang))           $box_typ = 'D';
           if       ($show_auswahl == 'emp')
           { $empty = tr_test_empty($current_ob_id,$current_ob_version,$slk);
             if (!$empty)         gen_lang_box($slk, $box_typ);
           } elseif ($show_auswahl == 'mod') 
           { if ($box_typ == 'E') gen_lang_box($slk, $box_typ);
           } else                 gen_lang_box($slk, $box_typ);
         }
       }
    } else // no user is loged in
    { gen_lang_box($st, 'S');
      if ($show_auswahl != 'int')
      { foreach ($language_all as $slk => $sln ) if ($slk != $st) gen_lang_box($slk, 'S');
      }
    }


   echo $v_template->generate($v_att, 'string');
   unset($v_att);
}


/**********************************************************************
 * MAIN PROGRAM SECTION
 **********************************************************************/
/**********************************************************************
 * PARSE PARAMETERS SECTION
 **********************************************************************/
// determine which submit button was pressed
// submit button names has following syntax : "submit_action[_langid]"
// where action is text specifing what was sumbited :
// 'all' - update entrie page
// 'next','prev' - change page to next/prev
// 'suggestion' - update suggestion with specified langid
// 'trtext' - update translation text with specified langid
// 'accept' - accept suggestion with specified langid
// and langid indentifies language which should be modified
  /* save edit object for setadmins */

$action = '';
foreach ($_POST as $key => $value) 
{ if (substr($key,0,7)=='submit_')
  { $acts=explode('_',$key);
    $action=$acts[1];
    break;
  }
} 
switch($action)
{       
  // user submited all changes
  case 'all':
    $lang_index=0;
    // process every submited trtext and suggestion
    foreach ($_POST as $key => $value) 
    { $pt = explode('_',$key);
      if (substr($key,0,7) =='trtext_')     $tr_ok[] = tr_update($current_ob_id,$value,"take_from_obj_id",$pt[2],1,$pt[1],'t');
      if (substr($key,0,11)=='suggestion_') $tr_ok[] = tr_update($current_ob_id,$value,"take_from_obj_id",$pt[2],4,$pt[1],'s');
    }
    break;
  // update suggestion only
  case 'suggestion':
    $updatestring = $_POST['suggestion_'.$acts[2].'_'.$acts[3]];
 //   if ( !strpos($updatestring, 'http:') and !strpos($updatestring, 'https:')) {
       $tr_ok[] = tr_update($current_ob_id,$updatestring,"take_from_obj_id",$acts[3],4,$acts[2],'s');
 //   } else $tr_ok[] = 32;
    break;
  // update translation text only
  case 'trtext':
    $updatestring = $_POST['trtext_'.$acts[2].'_'.$acts[3]];
    $tr_ok[] = tr_update($current_ob_id,$updatestring,"take_from_obj_id",$acts[3],1,$acts[2],'t');
    break;
  // accept suggestion
  case 'accept':
    $tr_ok[] = tr_update($current_ob_id,'',"take_from_obj_id",$acts[3],5,$acts[2],'a');
    break;
  case 'delete':
    $tr_ok[] = tr_update($current_ob_id,'',"take_from_obj_id",$acts[3],6,$acts[2],'r');
    break;
}

  //print: Attributes and  Image
  include('./tpl_script/edit_object_data.php');

  // generate html code for entire page
  gen_all($tr_ok);

  include('./tpl_script/footer.php');

?>
