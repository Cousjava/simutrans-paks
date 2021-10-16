<?php
$title = 'Direction';
include ('./tpl_script/header.php');
include ('./include/obj.php');
include ('./include/translations.php');
include ('./include/select.php');
/*
   object search by name and keyword
*/

// subobject_querry($join,$obj_auswahl,$obj_sub_auswahl);
// you can fount in select.php

    //displays the links to all objects of given type
function print_obj_links ($versin_id, $obj_auswahl, $obj_sub_auswahl)
{   GLOBAL   $LNG_EDIT, $LNG_ADMIN,$user,$maintainter;
    GLOBAL $v_att;

    if ( $obj_auswahl == 255  ) $obj_search = "";
    else                        $obj_search = " AND o.obj='".$obj_auswahl."' ";
    $join = ''; $sql_sub_obj = subobject_querry($join,$obj_auswahl,$obj_sub_auswahl);
    $sql="SELECT obj_name, obj, object_id  FROM objects o $join 
          WHERE version_version_id =".$versin_id.$obj_search.$sql_sub_obj.
        " ORDER BY obj, obj_name COLLATE utf8_unicode_ci ASC";       

    $res = db_query($sql);
    $obj_tab = array();
    $obj_inx = 0;

    $tbl_count = 0;
    $row_count = 0;
    $col_count = 0;
    $obj_count = 0;

    $ot = "old table, jet no table started";
    while ($object = db_fetch_array($res))
    { if ($ot != $object['obj'])   
        { $ot =  $object['obj'];
          if ( $obj_count > 0 ) { $tbl_count++; }
          $v_att['value_obj_table']['objects'][$tbl_count]['value_object_subtitle'] = $object['obj'];
          
          $row_count = 0;
          $col_count = 0;
          $obj_count = 0;
        }  
        $obj_tab[$obj_inx] = $object['object_id']; 
        
        $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['style'] = 'bg_grey';
        
        $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['value_col'] = "<a href='edit.php?obj_id=" . $object['object_id'] . "&amp;index=" . $obj_inx . "' target='_blank'>" . htmlentities($object['obj_name'], ENT_QUOTES) . "</a>";
        
        if ( in_array($versin_id,$maintainter) ) {
          $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['setadmin']['object_id'] = $object['object_id'];
          $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['setadmin']['edit'] = $LNG_ADMIN[51];
          $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['setadmin']['delete'] = $LNG_ADMIN[52];
        }
        
        if ( $col_count == 3 ) { $col_count = 0; $row_count++; } 
        else { $col_count++; }
        
        $obj_inx++;
        $obj_count++;
        $v_att['value_obj_table']['objects'][$tbl_count]['value_object_count'] = $obj_count;
    } 
    
    switch ($col_count) {
      case 1:
        $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['value_col'] = "";
        $col_count++;
      case 2:
        $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['value_col'] = "";
        $col_count++;
      case 3:
        $v_att['value_obj_table']['objects'][$tbl_count]['rows'][$row_count]['cols'][$col_count]['value_col'] = "";
        break;
    }
            
    if ($obj_inx == 0) { $v_att['value_obj_table']['objects'][$tbl_count]['value_no_objects'] = $LNG_EDIT[22]; }

    $_SESSION['search_result_tab'] = $obj_tab;
    $_SESSION['search_result_len'] = $obj_inx;
    
    if ($obj_inx > 1 and  $user != "") $v_att['set_head']['line_edit']['link_text'] = $LNG_EDIT[37];
}

function like_escape ($s,$r)
{ $s = str_replace($r, $r.$r,$s);
  $s = str_replace('%', $r.'%',$s);
  $s = str_replace('_', $r.'_',$s);
  return $s;
}


////////////////////////////////////////////////////////////////////////////
//accessible to anyone

  //header, please no output before this (sends header information)
  //establishes the connection to the db, include dblib

  $settab = array();
  $settab['all']          = $LNG_FORM[21];
  $settab['all text']     = $LNG_STATS_TRANS[11];
  $settab['translated']   = $LNG_STATS_TRANS[6];
  $settab['show_text']    = $LNG_STATS_TRANS[12];
  $settab['untranslated'] = $LNG_STATS_TRANS[1];
  $settab['suggestion']   = $LNG_STATS_TRANS[0];


    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file   
    $v_template->parseFile('./tpl/direction.htm');  
    // ----- Prepare data
    $v_att = array();

    //prints page title
    $v_att['page_title'] = $page_titel[$title];


  $language        = select_box_read_language();
  $version_auswahl = select_box_read_version();
  $obj_auswahl     = select_box_read_obj($version_auswahl);
  $obj_sub_auswahl = select_box_read_sub_obj($version_auswahl,$obj_auswahl);
  $trans_auswahl   = select_box_read('select_box_translate',$settab,'all',-1);

  $v_att['value_link_1'] = $LNG_MAIN[3];
  $v_att['value_link_2'] = $LNG_MAIN[24];

  $v_att['form_head'] = $LNG_FORM[0]; 
  
  select_box_all($version_auswahl,$obj_auswahl,$obj_sub_auswahl);
  select_box("select_language",$language_all,$language,'',-1,$LNG_FORM[43]);
  select_box('select_box_translate',$settab,$trans_auswahl,'',-1); 

  $sr_string1  = "";
  $sr_string2 = "";
  if (isset($_POST['searchstring']))  $sr_string1 = mb_strtolower($_POST['searchstring'],'UTF-8');
  if (isset($_POST['searchstring2'])) $sr_string2 = mb_strtolower($_POST['searchstring2'],'UTF-8');

  $v_att['titel_search_object'] = $LNG_FORM[48]; 
  $v_att['value_search_object'] = $sr_string1; 

  $v_att['titel_trans_object'] = $LNG_FORM[49]; 
  $v_att['value_trans_object'] = $sr_string2; 

  $v_att['button_submit'] = $LNG_FORM[45]; 

////////////////////////////////////////////////////////////////////////////
if ( isset($_POST['sugaccept']) or isset($_POST['sugdelete' ]))
{ $submit_sug = 1;
  foreach ($_POST as $key => $value) 
  { if (substr($key,0,7)=='object_')
     { $obj=explode('_', $key);
       if ( isset($_POST['sugaccept']) )
            $tr_ok = tr_update($obj[1],'',"take_from_obj_id",$obj[2],5,$obj[3],'a');
       else $tr_ok = tr_update($obj[1],'',"take_from_obj_id",$obj[2],6,$obj[3],'r');
       if ($tr_ok <  10)
       { $e = $LNG_LOAD3[10];                  // successful saved
         if ($tr_ok == 3) $e = $LNG_LOAD3[11]; // written_as_suggestion
       } elseif ($tr_ok <  20) 
       { $e = $LNG_LOAD3[20];                  // text already present
       } else $e = $LNG_LOAD3[$tr_ok];         // all errors
       $v_att['value_message']['messages'][]['message'] = $e;
     }
  } 
} else $submit_sug = 0;  

      
if ((isset($_POST['txt_s']) and $_POST['txt_s'] == $LNG_FORM[45]) or isset($_GET['obj_auw']) or $submit_sug == 1 ) do
{ if (!$sr_string2 and $language == 255 and $trans_auswahl == 'untranslated') // too mutch  
  { // not language selected by not suggestion
    $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[4];
    break;
  }
  
  if ($version_auswahl == 255)
  { //  foreach($versions_all as $vs_id => $vs_a_name) 
    $v_att['value_message']['messages'][]['message'] = $LNG_LOAD3[40];
    break;
  } 
  
  
  // Objekte search => build search string
  // Versionsauswahl  
  $suche = "o.version_version_id=".$version_auswahl;
  $sort  = 'o.version_version_id, o.obj, o.obj_name COLLATE utf8_unicode_ci';
  $searchtable = "objects o ";
  if ($sr_string2 or $language != 255 or $trans_auswahl != 'all')
  { $searchtable .= "JOIN translations_".$version_auswahl." t ON (o.object_id=t.object_object_id) ";
  }
  
  // build text search
  if ( $sr_string2 ) 
  { //  $suche .= "MATCH(t.tr_text) AGAINST('".$sr_string2."')";
    $suche .= " AND ( LCASE(t.tr_text)      LIKE '%".db_real_escape_string(like_escape ($sr_string2,'#'))."%' ESCAPE '#' ";
    $suche .=    " OR LCASE(t.details_text) LIKE '%".db_real_escape_string(like_escape ($sr_string2,'#'))."%' ESCAPE '#' )";
  }
  if ( $sr_string1 ) 
  { $suche .= " AND LCASE(o.obj_name) LIKE '%".db_real_escape_string(like_escape ($sr_string1,'#'))."%' ESCAPE '#' ";
  }

  // build language search
  if ( $language != 255 ) $suche .= " AND t.language_language_id='".$language."'"; 

  // Objektauswahl  
  if ( $obj_auswahl != 255 ) 
  { $suche .= " AND o.obj='".$obj_auswahl."' ";
          $t_o_a = " - ".$obj_auswahl;
  }  else $t_o_a = '';

  // Objekt sub auswahl 
  $suche .= subobject_querry($searchtable,$obj_auswahl,$obj_sub_auswahl); 

  if     ( $trans_auswahl == 'untranslated' ) $suche .= " AND (t.tr_text='' OR (t.tr_text IS NULL)) ";
  elseif ( $trans_auswahl == 'suggestion' )   $suche .= " AND (t.suggestion<>'' or t.details_suggestion<>'') "; 
  elseif ( substr($trans_auswahl,0,3)!='all') $suche .= " AND (t.tr_text<>''    or t.details_text<>'') ";

  $search = db_query("SELECT * FROM ".$searchtable." WHERE ".$suche." ORDER BY ".$sort." ASC");

  // echo " sql:"."SELECT * FROM ".$searchtable." WHERE ".$suche." ORDER BY ".$sort." ASC"."<br>";
  $row_count = db_num_rows($search);
  if ( $row_count == 0 ) 
  { $v_att['value_message']['messages'][]['message'] = $LNG_EDIT[22];
    break;
  } 

  $v_att['set_head']['bez_set'] = $LNG_EDIT[1];
  $v_att['set_head']['value_set'] = $versions_all[$version_auswahl].$t_o_a;
  $v_att['set_head']['count']['obj_count'] = $row_count;  

  $need_buttons = 0;
  $l = -1;
  $old_obj_id = '-1';
  $obj_tab = array();
  $obj_inx = 0;
  while ($sr=db_fetch_array($search)) 
  {  if ( $trans_auswahl == 'untranslated' and in_array($sr['obj'], $object_no_translate )) continue; 
     $obj_id = $sr['object_id'];
     if ($old_obj_id != $obj_id)
     { $old_obj_id = $obj_id;
       $l++;
       $style = (($l % 2) == 0)?'bg_trans':'bg_grey';
       $obj_tab[$obj_inx] = $obj_id;
       $obj_inx++;
       $tt = 0;
     }
     $v_att['res_table']['line'][$l]['style'] = $style;
     $objname = htmlentities($sr['obj_name'], ENT_QUOTES, "UTF-8"); 
     $v_att['res_table']['line'][$l]['obj_name'] = $objname;  
     $v_att['res_table']['line'][$l]['obj_type'] = $sr['obj'];       
     $v_att['res_table']['line'][$l]['obj_id']   = $obj_id;
     $v_att['res_table']['line'][$l]['obj_inx']  = $obj_inx-1;
     if (isset($sr['language_language_id']))
     { $lang  = $sr['language_language_id'];
       $v_att['res_table']['line'][$l]['edit_lang']['browser_lang'] = $st;
       $v_att['res_table']['line'][$l]['edit_lang']['text_lang'] = $lang;

       $tr_status = '';
       foreach ($tr_db_name as $k => $f)
       { $f_tab = explode(',',$f);
         $tr_txt = $sr[trim($f_tab[0])];
         $tr_sug = $sr[trim($f_tab[1])];
         if ($k == 't') $bz = $LNG_EDIT[11];
         if ($k == 'd') $bz = $LNG_EDIT[28];
         if ($k == 'l') $bz = $LNG_EDIT[30];
         if ($k == 'h') $bz = $LNG_EDIT[31];
         if ($tr_sug != '')
         { $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['style'] = $style;
           $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['bez_trans'] = $bz;
           $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['value_Trans'] = htmlentities($tr_txt, ENT_QUOTES, "UTF-8");
           $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['bez_sug'] = $LNG_EDIT[14];
           $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['value_sug'] = htmlentities($tr_sug, ENT_QUOTES, "UTF-8");
           // true so generate checkbox
           if ( isset($_SESSION['role']) and $_SESSION['role'] != 'guest' and
                in_array($sr['language_language_id'],$_SESSION['edit_lang']) ) 
           {  $check_id = $obj_id.'_'.$lang.'_'.$k;
              $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['checkbox']['obj_id'] = $check_id;
              $need_buttons = 1;
           }
           $v_att['res_table']['line'][$l]['lang_line'][$tt]['take_sug']['text_lang'] = $lang;
           $tt++;
         } elseif ($tr_txt != '')
         { if (strpos($trans_auswahl,'text'))
           { if ($k != 't') $v_att['res_table']['line'][$l]['lang_line'][$tt]['show_txt']['txt_bez']['bez_trans'] = $bz;
             $v_att['res_table']['line'][$l]['lang_line'][$tt]['show_txt']['style'] = $style;
             $v_att['res_table']['line'][$l]['lang_line'][$tt]['show_txt']['obj_text'] = htmlentities($tr_txt, ENT_QUOTES, "UTF-8");
             $v_att['res_table']['line'][$l]['lang_line'][$tt]['show_txt']['text_lang'] = $lang;
             $tt++;
           } else
           { if ($k == 't')
             { $v_att['res_table']['line'][$l]['status']['style_trans'] = 'translate';
               $v_att['res_table']['line'][$l]['status']['bez_trans'] = $LNG_STATS_TRANS[6];
             }
             else
             { $tr_status .= $bz.' ';
               $v_att['res_table']['line'][$l]['status']['detail']['bez_detail'] = $tr_status;
             }
           }
         } else
         { if ($k == 't' and $language != 255) 
           { if (in_array($sr['obj'], $object_no_translate ))
             { $v_att['res_table']['line'][$l]['status']['style_trans'] = 'notranslate';
               $v_att['res_table']['line'][$l]['status']['bez_trans'] = $LNG_STATS_TRANS[13];
             } else
             { $v_att['res_table']['line'][$l]['status']['style_trans'] = 'untranslate';
               $v_att['res_table']['line'][$l]['status']['bez_trans'] = $LNG_STATS_TRANS[1];
             }
           }
         }
       }
     }

     // set admin links
     if ( in_array($version_auswahl,$maintainter) ) 
     { $v_att['res_table']['line'][$l]['setadmin']['object_id'] = $obj_id;
       $v_att['res_table']['line'][$l]['setadmin']['edit'] = $LNG_ADMIN[51];
       $v_att['res_table']['line'][$l]['setadmin']['delete'] = $LNG_ADMIN[52];
     }

  }
  $_SESSION['search_result_tab'] = $obj_tab;
  $_SESSION['search_result_len'] = $obj_inx;
  if ($obj_inx > 1 and  $user != "") $v_att['set_head']['line_edit']['link_text'] = $LNG_EDIT[37];
  db_free_result($search);

  if ( $need_buttons == 1 )
  { // buttons for accept and delete
    if ( isset($_SESSION['role']) and $_SESSION['role'] != 'guest' and
         in_array($version_auswahl, $_SESSION['set_enabled']) ) 
    { $v_att['res_table']['value_suggest_button']['lang_id'] = $language;
      $v_att['res_table']['value_suggest_button']['bez_accept'] = $LNG_EDIT[16];
      $v_att['res_table']['value_suggest_button']['bez_delete'] = $LNG_EDIT[26];
    }
  }

} while(0); // $_POST['prev'] == $LNG_FORM[33] || $_POST['next'] == $LNG_FORM[30] || $_GET["rss_feed"] == 1 
elseif ( isset($_POST['obj_s']) and $_POST['obj_s'] == $LNG_FORM[5]) 
{ // list objects
  if ($version_auswahl != 255 ) 
  { $v_att['set_head']['bez_set'] = $LNG_EDIT[1];
    $v_att['set_head']['value_set'] = $versions_all[$version_auswahl];
    print_obj_links ($version_auswahl,$obj_auswahl,$obj_sub_auswahl);        
  }
}

  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;
  unset($v_att);     


////////////////////////////////////////////////////////////////////////////
//footer, nothing after this (closes the page)
include_once ('./tpl_script/footer.php');
?>
