<?php
 
function select_box_read($box_name,$tab,$default,$lng_offset=0)
{ $w = $default;
  $box_name = str_replace('select_box_','',$box_name);
  if (isset($_SESSION[$box_name])) $w = $_SESSION[$box_name];
  if (isset($_POST[$box_name])) $w = $_POST[$box_name];
  else if (isset($_GET[$box_name]) and isset($_GET[$box_name]) !='' ) $w = $_GET[$box_name];
  if ($lng_offset != -1) { if (!in_array($w,$tab)) $w = $default; }
  else                   { if (!isset($tab[$w]))   $w = $default; }
//  if (preg_match('#^[a-zA-Z0-9@ \._-]{1,20}$#',$w) != 1) $w = $default;
  $_SESSION[$box_name] = $w;
  return $w;
}

function select_box_read_language()
{ global $language_all;
  $language_auswahl = "255";
  if (isset($_SESSION['language_auswahl']))    $language_auswahl = $_SESSION['language_auswahl'];
  if (isset($_POST["language"]) 
        and (preg_match("#^[a-z]{2,3}\$#", $_POST["language"]) == 1
         or $_POST["language"]  == '255' ))    $language_auswahl = $_POST["language"];
  elseif ( isset($_GET['lang']) and $_GET['lang'] != ''
       and preg_match("#^[a-z]{2,3}\$#", $_GET['lang']) == 1) $language_auswahl = $_GET['lang'];
  if (!isset($language_all[$language_auswahl])) $language_auswahl = "255";
  $_SESSION['language_auswahl'] =  $language_auswahl;
  return $language_auswahl;
}  
  
function select_box_read_version()
{ global $versions_all;
  $version_auswahl = 255;
  if (isset($_SESSION['version_auswahl'])) $version_auswahl = $_SESSION['version_auswahl'];
  $ava = $version_auswahl;
  if ( isset($_POST['version']) && ($_POST['version'] != '') )
  { $version_auswahl = intval($_POST['version']);
  } elseif ( isset($_GET['vers']) && ($_GET['vers'] != '') ) 
  { $version_auswahl = intval($_GET['vers']); 
  } elseif ( isset($_GET['version']) && ($_GET['version'] != '') ) 
  { $version_auswahl = intval($_GET['version']); 
  }

  if (!isset($versions_all[$version_auswahl])) $version_auswahl = 255;
  if ($ava != $version_auswahl) unset($_SESSION['col_pos'],
                                      $_SESSION['obj_typ_tab'], 
                                      $_SESSION['obj_sub_tab_type'],
                                      $_SESSION['obj_sub_tab_waytype'],
                                      $_SESSION['obj_sub_tab_engine_type'],
                                      $_SESSION['cluster'],
                                      $_SESSION['search_result_tab'],
                                      $_SESSION['search_result_len']);
  $_SESSION['version_auswahl'] =  $version_auswahl;
  return $version_auswahl;
}  
  
function select_box_read_obj($version_auswahl)
{ $obj_auswahl = 255;
  if (isset($_SESSION['obj_auswahl'])) $obj_auswahl = $_SESSION['obj_auswahl'];
  $oja = $obj_auswahl;
  if ( isset($_POST['obj_auw']) && ($_POST['obj_auw'] != '') ) 
  { $obj_auswahl = $_POST['obj_auw'];
  } elseif ( isset($_GET['obj_auw']) && ($_GET['obj_auw'] != '') ) 
  { $obj_auswahl = $_GET['obj_auw']; 
  } 
  if (preg_match('#^[a-zA-Z0-9@ \._-]{1,20}$#',$obj_auswahl) != 1) $obj_auswahl = 255;
  if ($oja != $obj_auswahl) unset($_SESSION['col_pos'],
                                  $_SESSION['obj_sub_tab_type'],
                                  $_SESSION['obj_sub_tab_waytype'],
                                  $_SESSION['obj_sub_tab_engine_type'],
                                  $_SESSION['cluster']);
  $_SESSION['obj_auswahl'] =  $obj_auswahl;
  return $obj_auswahl;
}

function select_box_read_sub_obj($version_auswahl,$obj_auswahl)
{ GLOBAL $sub_waytypes;
  $obj_sub_auswahl = 255;
  if (isset($_SESSION['obj_sub_auswahl'])) $obj_sub_auswahl = $_SESSION['obj_sub_auswahl'];
  $oja = $obj_sub_auswahl;
  if    (isset($_POST['obj_sub']) and $_POST['obj_sub'] != '') $obj_sub_auswahl = $_POST['obj_sub'];
  elseif ( isset($_GET['obj_sub']) and $_GET['obj_sub'] != '') $obj_sub_auswahl = $_GET['obj_sub'];  

  if (in_array($obj_auswahl, $sub_waytypes) or $obj_auswahl == 'building')
  { if (!in_array($obj_sub_auswahl,load_sub_typ_tab($version_auswahl,$obj_auswahl))) $obj_sub_auswahl = 255; 
  } else                                                                             $obj_sub_auswahl = 255;
  if (preg_match('#^[a-zA-Z0-9@ \,._-]{1,20}$#',$obj_sub_auswahl) != 1)              $obj_sub_auswahl = 255;
  if ($oja != $obj_sub_auswahl) unset($_SESSION['obj_sub_tab_engine_type']);
  $_SESSION['obj_sub_auswahl'] =  $obj_sub_auswahl;
  return $obj_sub_auswahl;
}

function select_box_read_trange()
{ global $trv,$trb;
  $trange = '1800-2099';
    if (isset($_GET['trange'])  and $_GET['trange'] != '')  $trange = $_GET['trange'];
    if (isset($_POST['trange']) and $_POST['trange'] != '') $trange = $_POST['trange'];
    $tr = explode('-',$trange); $trv = 1800; $trb = 2099;
    if (isset($tr[1])) 
    { $trv = intval($tr[0]); 
      $trb = intval($tr[1]);
      if ($trv < 1800 or $trv > 2099) $trv = 1800;
      if ($trb < 1800 or $trb > 2099) $trb = 2099;
      if ($trv > $trb) $trb = $trv;
      $trange = $trv.'-'.$trb;
      if ($trv < 1800) $trv = 1;
      if ($trb > 2099) $trb = 4000;
    } else $trange = '1800-2099';
  return $trange;
}


function select_box($box_name,$tab,$auswahl,$text,$lng_offset=0,$none_select='',$field_titel=0)
{  global $v_att,$LNG_FORM;
   if ($field_titel > 0) $v_att[$box_name]['field_titel'] = $LNG_FORM[$field_titel];
   $x = 0;
   $sel_tab = array();
   if ($none_select != '')
   { if ( $auswahl == '255' ) $sel_tab[$x]['opt_select'] = ' selected="selected" ';
     $sel_tab[$x]['opt_value']  = '255';
     $sel_tab[$x]['opt_name']   = $none_select;
     $x++;
   }
   foreach ($tab as $t_k => $t_e)
   {  if ($lng_offset == -2) { $value = $t_e; $name = $t_e; }
      if ($lng_offset == -1) { $value = $t_k; $name = $t_e; }
      if ($lng_offset >= 0 ) { $value = $t_e; $name = $text[$lng_offset]; $lng_offset++; }

      if ($value == $auswahl) $sel_tab[$x]['opt_select'] = ' selected="selected" ';
      $sel_tab[$x]['opt_value']  = $value;
      $sel_tab[$x]['opt_name']   = $name;
      $x++;
   }
   if ($box_name == '') return $sel_tab;
   else $v_att[$box_name]['box_entry'] = $sel_tab;
}


function select_box_version($version_auswahl,$head=6) 
{   global $v_att,$versions_all,$LNG_FORM;
    if ($head != '') $v_att['field_titel'] = $LNG_FORM[$head];
    select_box('select_box_version',$versions_all,$version_auswahl,'',-1,$LNG_FORM[7]); //$LNG_FORM[46]
}


function select_box_all($version_auswahl,$obj_auswahl,$obj_sub_auswahl,$button='')
{ GLOBAL $LNG_FORM,$sub_waytypes,$v_att;

  select_box_version($version_auswahl,'');
  select_box('select_box_obj',load_obj_typ_tab($version_auswahl),$obj_auswahl,'',-2,$LNG_FORM[1]);
  
  $head  = $LNG_FORM[0];
  if ( $obj_auswahl == 'building' ) 
  { select_box('select_box_sub_obj',load_sub_typ_tab($version_auswahl,$obj_auswahl),$obj_sub_auswahl,'',-2,$LNG_FORM[47]);
    $head .= ": &nbsp; &nbsp; &nbsp; &nbsp; ".$LNG_FORM[3];
  }
  elseif (in_array($obj_auswahl, $sub_waytypes)) 
  { select_box('select_box_sub_obj',load_sub_typ_tab($version_auswahl,$obj_auswahl),$obj_sub_auswahl,'',-2,$LNG_FORM[2]);
    $head .= ": &nbsp; &nbsp; ".$LNG_FORM[4];
  }
        
  $v_att['field_titel'] = $head;
  if ($button == '') $button = $LNG_FORM[5];
  if ($button != 'no') $v_att['submit'] = $button;
 
}

function load_obj_typ_tab ($vs_id)
{ global $LNG_EDIT;
  if (isset($_SESSION['obj_typ_tab'])) $obj_typ_tab = $_SESSION['obj_typ_tab'];
  else 
  { $obj_typ_tab = array();
    $res = db_query ("SELECT DISTINCT obj  FROM objects WHERE version_version_id=$vs_id ORDER BY obj ASC;");
    while ($o_t = db_fetch_object($res)) $obj_typ_tab[] = $o_t->obj;
    //check if empty or not
    if (count($obj_typ_tab) == 0) $obj_typ_tab[] = $LNG_EDIT[22];
    $_SESSION['obj_typ_tab'] = $obj_typ_tab;
  }
  return $obj_typ_tab;
}

function load_sel_tab($vs_id,$obj_auswahl,$sel_typ)
{  global $LNG_EDIT;
   $s = 'obj_sub_tab_'.$sel_typ;
   if (isset($_SESSION[$s])) $sel_tab = $_SESSION[$s];
   else 
   { $sel_tab = array();
     $res = db_query ("SELECT DISTINCT p_value  FROM property p JOIN objects o 
                       ON ( o.object_id=p.having_obj_id)
                       WHERE o.version_version_id=$vs_id AND o.obj='".$obj_auswahl."' 
                       AND p.p_name='".$sel_typ."' ORDER BY p_value ASC;");
     while ($o_t = db_fetch_object($res)) $sel_tab[] = $o_t->p_value;
     //check if empty or not
     if (count($sel_tab) == 0) $sel_tab[] = $LNG_EDIT[22];
    $_SESSION[$s] = $sel_tab;
   }
   return $sel_tab;
}

function load_sub_typ_tab($vs_id,$obj_auswahl)
{  global $building_city,$building_cur;
   if ($obj_auswahl == 'building') $w_or_type ='type';
   else                            $w_or_type ='waytype';
   $sub_typ_tab = load_sel_tab($vs_id,$obj_auswahl,$w_or_type);
   if ($obj_auswahl == 'building' and count($sub_typ_tab) > 2) 
   { $sub_typ_tab[] = implode(', ',$building_city); // com, ind, res, tow
     $sub_typ_tab[] = implode(', ',$building_cur);  // cur, mon
   }
   return $sub_typ_tab;
}

function load_engine_tab($vs_id,$obj_auswahl,$obj_sub_auswahl)
{  global $LNG_STATS_VEH,$LNG_EDIT;
   $s = 'obj_sub_tab_engine_type';
   if (isset($_SESSION[$s])) $tab = $_SESSION[$s];
   else 
   { $tab = array();
     $t = '';
     if ($obj_sub_auswahl != 255) $t = " AND o.type='".$obj_sub_auswahl."' ";
     $res = db_query ("SELECT DISTINCT p_value  FROM property p JOIN objects o 
                       ON ( o.object_id=p.having_obj_id)
                       WHERE o.version_version_id=$vs_id AND o.obj='".$obj_auswahl."' ".$t." 
                       AND p.p_name='engine_type' ORDER BY p_value ASC;");
     while ($o_t = db_fetch_object($res)) 
     { $pv = strtolower($o_t->p_value);
       if ($o_t->p_value != 'none') $tab[$pv] = tr_translate_text(0,$pv);
     }
     //check if empty or not
    if (count($tab) > 0) 
    { $tab['none']    = $LNG_STATS_VEH[12];
      $tab['unknown'] = $LNG_STATS_VEH[13];
    }
    else                 $tab[255]    = $LNG_EDIT[22];
    $_SESSION[$s] = $tab;
   }
   return $tab;
}

function subobject_querry(&$join,$obj_type,$obj_sub_auswahl)
{ GLOBAL  $sub_waytypes;
  // $join wird als Referenz übergeben, deshalb kann was angehängt werden
  $sql_sub_obj = '';
  if ( $obj_sub_auswahl != 255 )
  { $sql_sub_obj .= ' AND ';
    $join .= " JOIN property p ON o.object_id = p.having_obj_id ";
    if ( $obj_type == 'building' )
    { $sub_t = explode(',',$obj_sub_auswahl);
      $sql_sub_obj .= " p.p_name ='type' and ( ";
      $o = '';
      foreach ($sub_t as $sk =>$suba )
      { $sql_sub_obj .= $o." p.p_value='".trim($suba)."' "; 
        $o = "OR"; 
      }
      $sql_sub_obj .= " ) ";
    } 
           
    if (in_array($obj_type,$sub_waytypes)) 
      if ($obj_sub_auswahl == 'track')
           $sql_sub_obj .= " p.p_name ='waytype' AND (p.p_value='electrified_track' OR p.p_value='track') ";
      else $sql_sub_obj .= " p.p_name ='waytype' AND p.p_value='".$obj_sub_auswahl."' ";
  }
  return $sql_sub_obj;
}

function obj_search($version_auswahl,$obj_auswahl,$obj_sub_auswahl)
{ if ($version_auswahl == 255) return 40; // Searching across multiple sets is currently not supported!
  if ( $obj_auswahl == 255  ) $obj_search = "";
  else                        $obj_search = " AND o.obj='".$obj_auswahl."' ";
  $join = ''; $sql_sub_obj = subobject_querry($join,$obj_auswahl,$obj_sub_auswahl);
  $sql="SELECT obj, object_id  FROM objects o $join 
        WHERE version_version_id =".$version_auswahl.$obj_search.$sql_sub_obj.
      " ORDER BY obj_name COLLATE utf8_unicode_ci ASC";
  $res = db_query($sql);
  $obj_tab = array();
  $obj_inx = 0;
  while ($object = db_fetch_array($res))
  { $obj_tab[$obj_inx] = $object['object_id']; 
    $obj_inx++;
  }
  $_SESSION['search_result_tab'] = $obj_tab;
  $_SESSION['search_result_len'] = $obj_inx;
}


/* 

p_name fehlt in directions

*/

?>
