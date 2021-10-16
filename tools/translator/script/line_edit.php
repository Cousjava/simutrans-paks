<?php
 /* Line Edit 
    allows Text edit in one line
    and multible objects in 20 Lines
  */   

 //header, please no output before this (sends header information)
  //establishes the connection to the db, include dblib
  $title = 'line_edit';
  include ('./tpl_script/header.php');
  include ('./include/obj.php');
  include ('./include/translations.php');
  include ('./include/select.php');

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/line_edit.htm');
  // ----- Prepare data
  $v_att = array();

$lang_text_prof = array ($LNG_FORM[48], // "Objekt"
                         $LNG_FORM[53], // "Referenz"
                         $LNG_EDIT[5],  // "Note",
                         $LNG_FORM[49], // "Text",
                         $LNG_EDIT[28], // "details",
                         $LNG_EDIT[31], // "history",
                         $LNG_EDIT[30], // "links",
                         $LNG_EDIT[5],  // "note",
                         $LNG_FORM[54]); //"kommentar"
 
$col_tab_l =array('o','r','n');
$col_tab_r =array('t','d','h','l','n','k');


function fatal_error($err)
{ global $v_att,$v_template,$LNG_EDIT;
  $v_att['value_message']['messages'][]['message'] = $LNG_EDIT[$err];   
  echo $v_template->generate($v_att, 'string');
  include('./tpl_script/footer.php'); 
  die();
}
  
  
////////////////////////////////////////////////////////////////////////////
//accessible to anyone


  //prints page title
  $v_att['page_title'] = $page_titel[$title];
  if (!isset($_SESSION['edit_lang'])) fatal_error(23);
  $cur_index   = 0;
  if (isset($_GET['index'])  and $_GET['index']!='')  $cur_index   = intval($_GET['index']);
  $v_att['cur_index'] = $cur_index;

  $obj_tab_len = 0;
  if (isset($_SESSION['search_result_len'])) 
  { $obj_tab     = $_SESSION['search_result_tab'];
    $obj_tab_len = $_SESSION['search_result_len'];
  }  
  if ($obj_tab_len <= 0 or $cur_index >= $obj_tab_len) fatal_error(22);

  // $current_ob_version 
  $errg = ob_read($obj_tab[0]);
  if ( $errg === null ) fatal_error(22);
  $current_ob_version    = $errg->version_version_id;

  $no_translate_lang = get_lang_disabled($current_ob_version);
  $user_lng = array(); // get user allowed edit language
  foreach ($_SESSION['edit_lang'] as $l) if (!in_array($l,$no_translate_lang)) $user_lng[$l] = $LNG_LANGUAGE[$l];
  natcasesort($user_lng);

  $ref_lang = $st;
  if (isset($_SESSION['ref_lang'])) $ref_lang = $_SESSION['ref_lang'];
  $disp_lines = 20;
  if (isset($_SESSION['config4']) and $_SESSION['config4'] > 2) $disp_lines = $_SESSION['config4'];
  
  $language = select_box_read_language();
  if ($language == 255)  $language = $ref_lang;
  if (!isset($user_lng[$language])) $language = key($user_lng);
  if (!isset($user_lng[$language])) fatal_error(25);

  $column_left = "o";
  if (isset($_SESSION['col_l'])) $column_left = $_SESSION['col_l'];
  if (isset($_POST['column_l']) and in_array($_POST['column_l'],$col_tab_l)) $column_left = $_POST['column_l'];
  $_SESSION['col_l'] = $column_left;
  
  $column_right = "t";
  if (isset($_SESSION['col_r'])) $column_right = $_SESSION['col_r'];
  if (isset($_POST['column_r']) and in_array($_POST['column_r'],$col_tab_r)) $column_right = $_POST['column_r'];
  $_SESSION['col_r'] = $column_right;
  
  
  
  
  
  // generates html code for "prev" button if there are objects before
  if ($cur_index > 0) 
  { $index_l = max(0,$cur_index - $disp_lines);
    $obj_i_p = $obj_tab[$index_l];
    $errg= ob_read($obj_i_p);
    $obj_p_name = $errg->obj_name;
    $v_att['gen_prev']['value_index']  = $index_l;
    //display object button - ony 40 chars from name and make sure it is in html acceptable form 
    $v_att['gen_prev']['value_obj_name'] = substr (htmlentities($obj_p_name, ENT_QUOTES), 0, 40);
  }

  // generates html code for "next" button if there are objects after
  if ($cur_index < $obj_tab_len- $disp_lines)
  { $index_r = $cur_index + $disp_lines;
    $obj_i_n = $obj_tab[$index_r];
    $errg= ob_read($obj_i_n);
    $obj_n_name = $errg->obj_name;
    $v_att['gen_next']['value_index'] = $index_r;
    //display object button - ony 40 chars from name and make sure it is in html acceptable form 
    $v_att['gen_next']['value_obj_name'] = substr (htmlentities($obj_n_name, ENT_QUOTES), 0, 40);
  }

  select_box("select_column_l",$col_tab_l,$column_left,$lang_text_prof,0);
  select_box("select_language",$user_lng,$language,'',-1);
  select_box("select_column_r",$col_tab_r,$column_right,$lang_text_prof,3);

  $v_att['store_all']['value_store_all'] = $LNG_EDIT[19];
  

  ////////////////////////////////////////////////////////////////////////////
  if (isset($_POST['store_all']))
  { foreach ($_POST as $key => $value) 
    { if (substr($key,0,5)=='line_')
      { $obj=explode('_', $key);
        switch ($obj[1])
        { case 't' :
          case 'd' :
          case 'h' :
          case 'l' : $tr_ok = tr_update($obj[2],$value,"take_from_obj_id",$obj[3],1,$obj[1],$obj[4]);
                    if ($tr_ok > 19)
                    { $object = ob_read($obj[2]);
                      $v_att['value_message']['messages'][]['message'] = $object->obj_name.' Fehler: '.$LNG_LOAD3[$tr_ok];   
                    }
        }

      }
    }  
  } 

  // display lines
  $col1 = array();
  $col2 = array();
  $input_text_name = array();
  $col2_max = 40;
  $i_end = min($cur_index+$disp_lines-1,$obj_tab_len-1);
  for ($i = $cur_index; $i <= $i_end; $i++)
  {  $cur_obj_id = $obj_tab[$i];
     $object = ob_read($cur_obj_id);
     switch ($column_left)
     { case 'o' : $col1[$i] = $object->obj_name;
                  break;
       case 'n' : $col1[$i] = $object->note;
                  break;
       case 'r' : $tranl1 = tr_read($cur_obj_id,$object->version_version_id,$st,'t');
                  if ($tranl1[0] == "") $col1[$i] = $object->obj_name;
                  else                  $col1[$i] = $tranl1[0];
                  break;
     }
     $input_text_name[$i] = "";
     switch ($column_right)
     { case 't' :
       case 'd' :
       case 'h' :
       case 'l' : $tranl2 = tr_read($cur_obj_id,$object->version_version_id,$language,$column_right);
                  $col2[$i] = $tranl2[0];
                  $input_text_name[$i] = "line_".$column_right."_".$cur_obj_id."_".$language."_t";
                  break;
       case 'n' : $col2[$i] = $object->note;
                  break;
       case 'k' : $col2[$i] = $object->comments;
                  break;
     }
     $col2_max = max($col2_max,mb_strlen($col2[$i],'UTF-8'));
  }

  echo $v_template->generate($v_att, 'string');

  echo '<hr>';
  echo ' <table width="100%" cellspacing="0" cellpadding="2" border="1">'."\n";
  for ($i = $cur_index; $i <= $i_end; $i++)
  {  printf('<tr><td align="left">%s</td>
                 <td align="left"><input type="text" style="font-size: 100%%; border:0px;" size="%s" name="%s" value="%s"/></td></tr>',
                    htmlentities($col1[$i], ENT_QUOTES, "UTF-8"),
                    $col2_max,
                    $input_text_name[$i],
                    htmlentities($col2[$i], ENT_QUOTES, "UTF-8"));
  }
  echo '</table>';
  echo '</form>';


////////////////////////////////////////////////////////////////////////////
//footer, nothing after this (closes the page)
include_once ('./tpl_script/footer.php');
?>
