<?php
include ('./include/images.php');
include ('./include/select.php');
include ('./include/obj.php');

$title='Objekt-Browser';
include ("tpl_script/header.php");
$user=$_SESSION['userId'];
//painters and admins can browse objects
$u_level=array('admin','gu','painter','pakadmin');

if ( !isset($_SESSION['role']) or !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

// setadmin link liste
include('./tpl_script/setadmin_links.php');

function printobj($obj_id,$obj_num) 
{ GLOBAL $v_att,$user, $LNG_OBJ_BROWSER,$LNG_MANAGE;
  $errg= ob_read($obj_id);
  if ($errg === NUll )  return;

   $v_att['line'][$obj_num]['info']  = sprintf('<a href="obj_info.php?obj_id=%s&index=%s" target="_blank" class="sellink">%s</a>'
    ,$obj_id
    ,$obj_num
    ,htmlentities($errg->obj_name, ENT_QUOTES));

    $v_att['line'][$obj_num]['edit'] = sprintf('<A href="edit.php?obj_id=%s&index=%s" target="_blank">%s</A>'
    ,$obj_id
    ,$obj_num
    ,$LNG_OBJ_BROWSER[1]);

  if ( in_array($user, get_setmaintainter($errg->version_version_id)))
  { $v_att['line'][$obj_num]['obj_edit'] = sprintf('<a href="obj_edit.php?obj_id=%s" target="_blank" class="sellink">%s</a>'
     ,$obj_id
     ,$LNG_MANAGE[5]);

    $v_att['line'][$obj_num]['delete'] = sprintf('<a href="obj_delete.php?obj_id=%s" target="_blank" class="sellink">%s</a>'
     ,$obj_id
     ,"delete"); // $LNG_MANAGE[5]);
  }
 
  $v_att['line'][$obj_num]['nr']  = $obj_num;
  $v_att['line'][$obj_num]['obj'] = $errg->obj;
  $v_att['line'][$obj_num]['img'] = display_image_tile ($obj_id, $errg->version_version_id, "align='top' border='0px'",8,false);
}

/* ----------------------------------------------------------------- */
/* List Objects                                                      */
/* ----------------------------------------------------------------- */

function obj_list_pages ($cur_index,$pagecount) 
{ global $v_att;
  if (!isset($_SESSION['search_result_len'])) return;
  $obj_tab     = $_SESSION['search_result_tab'];
  $obj_tab_len = $_SESSION['search_result_len'];
  if ($obj_tab_len <= 0 or $cur_index > $obj_tab_len) return;

  $page_list = '';
  $i = 0;
  while ($i<=$obj_tab_len) 
  { $i2 = $i+$pagecount-1; if ($i2>$obj_tab_len) $i2=$obj_tab_len-1;
    $errg= ob_read($obj_tab[$i]);
    $n1 = $errg->obj_name;
    $errg= ob_read($obj_tab[$i2]);
    $n2 = $errg->obj_name;

    $link = htmlentities('<'.substr($n1,0,10)."....".substr($n2,0,10).'>', ENT_QUOTES);
    if ($i<=$cur_index && $cur_index<=$i2 ) $link = "<B><U>$link</U></B>";
    else                                    $link = '<a href="?index='.$i.'">'.$link.'</a>';
    $page_list .= " <nobr>&nbsp;".$link."&nbsp;</nobr>";
    $i += $pagecount;
  }
  if ($cur_index>$obj_tab_len) $cur_index=$i-$pagecount;
  $page_objto=$cur_index+$pagecount-1;

  return $page_list;
}

function obj_list ($cur_index,$pagecount)
{ GLOBAL $v_att,$LNG_EDIT, $LNG_FORM, $LNG_OBJ_BROWSER;


  if (!isset($_SESSION['search_result_len'])) return;
  $obj_tab     = $_SESSION['search_result_tab'];
  $obj_tab_len = $_SESSION['search_result_len'];
  if ($obj_tab_len <= 0 or $cur_index > $obj_tab_len) return;

  // generates html code for "prev" button if there are objects before
  if ($cur_index > 0) 
  { $index_l = max(0,$cur_index - $pagecount);
    $errg= ob_read($obj_tab[$index_l]);
    $obj_p_name = $errg->obj_name;
    $v_att['gen_prev']['value_index']  = $index_l;
    //display object button - ony 40 chars from name and make sure it is in html acceptable form 
    $v_att['gen_prev']['value_obj_name'] = htmlentities( substr($obj_p_name,0,40), ENT_QUOTES);
  }

  // generates html code for "next" button if there are objects after
  if ($cur_index < $obj_tab_len- $pagecount)
  { $index_r = $cur_index + $pagecount;
    $errg= ob_read($obj_tab[$index_r]);
    $obj_n_name = $errg->obj_name;
    $v_att['gen_next']['value_index'] = $index_r;
    //display object button - ony 40 chars from name and make sure it is in html acceptable form 
    $v_att['gen_next']['value_obj_name'] = htmlentities( substr($obj_n_name,0,40), ENT_QUOTES);
  }

  $v_att['head_title'] = $LNG_OBJ_BROWSER[10];
  $v_att['head_field1'] = $LNG_OBJ_BROWSER[5];
  $v_att['head_field2'] = $LNG_OBJ_BROWSER[7];
  $v_att['head_field3'] = $LNG_OBJ_BROWSER[8];

  for ($obj_num =     $cur_index; 
       $obj_num < min($cur_index + $pagecount,$obj_tab_len);
       $obj_num++) printobj ($obj_tab[$obj_num],$obj_num);
}

/* ----------------------------------------------------------------- */
/* Main Program                                                      */
/* ----------------------------------------------------------------- */
  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file   
  $v_template->parseFile('./tpl/obj_browser.htm');  
  // ----- Prepare data
  $v_att = array();

  //prints page title
  $v_att['page_title'] = $page_titel[$title];



  $language        = select_box_read_language();
  $version_auswahl = select_box_read_version();
  $obj_auswahl     = select_box_read_obj($version_auswahl);
  $obj_sub_auswahl = select_box_read_sub_obj($version_auswahl,$obj_auswahl);

  select_box_all($version_auswahl,$obj_auswahl,$obj_sub_auswahl);
  $v_att['button_submit'] = $LNG_FORM[45]; 

  if (isset($_POST['obj_s']) and $_POST['obj_s'] == $LNG_FORM[45])
  { // Objekte suchen
    if ($version_auswahl != 255) obj_search($version_auswahl,$obj_auswahl,$obj_sub_auswahl);
  }

  $cur_index   = 0;
  if (isset($_GET['index'])  and $_GET['index']!='')  $cur_index   = intval($_GET['index']);
  if ($cur_index < 0) $cur_index = 0;
  $v_att['cur_index'] = $cur_index;

  $pagecount = 20;
  $user_pagecount = $_SESSION['config4'];
  if ($user_pagecount > 2) $pagecount = $user_pagecount;




  if ($version_auswahl != 255) 
  { $v_att['set_title'] = $versions_all[$version_auswahl];
    if ($obj_auswahl != 255 ) $v_att['obj_title'] = " - ".$obj_auswahl;

    $v_att['jump_line'] =  obj_list_pages($cur_index,$pagecount);

    obj_list ($cur_index,$pagecount);
  }

  echo $v_template->generate($v_att, 'string');
  unset($v_att);     

  include("tpl_script/footer.php");
?>
