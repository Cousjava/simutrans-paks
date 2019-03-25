<?php
  include ('./include/images.php');


  global $current_ob_note, $user, $LNG_EDIT, $LNG_ADMIN, $object_text, $st;
  global $current_ob_name, $current_ob_id, $current_ob_version, $current_ob_obj_type, $current_ob_obj_subtype;
  global $imagepfad,  $current_ob_copyright,$versions_all;
 
  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/edit_object_data.htm');
  // ----- Prepare data
  $v_att = array();

  //we need to add the information about version
  $v_att['page']['page_title'] = $LNG_EDIT[0].': ' . htmlentities($current_ob_name);
  $v_att['page']['page_subtitle'] = $LNG_EDIT[1].': '.$versions_all[$current_ob_version].', '.$LNG_EDIT[2].': '.$current_ob_obj_type;

  //if ( in_array($current_ob_obj_type, $object_text) ) { $v_att['page']['page_subtitle'] .= ', '.$LNG_EDIT[3].': '.$current_ob_copyright; }

  if ( $current_ob_obj_type == 'help_file' )
  { $file = $imagepfad.$current_ob_version.'/'.$current_ob_name.'.png';
    if ( file_exists($file) )
    {   $v_att['gen_attr']['show_help_image']['value_img_title'] = $current_ob_name;
        $v_att['gen_attr']['show_help_image']['value_file'] = $file;
    }
  }
  $paks = db_query("SELECT `maintainer_user_id`, `maintainer_user_id2`, `maintainer_user_id3` FROM `versions` WHERE `version_id`=$current_ob_version;");
  $pakset = db_fetch_array($paks);

  if ( in_array($current_ob_obj_type, $object_text) ) 
  {
    $v_att['gen_attr']['noteblock']['gui_note'] = $LNG_EDIT[5];
    $v_att['gen_attr']['noteblock']['value_note'] = htmlentities($current_ob_note, ENT_QUOTES, "UTF-8");

    if ( in_array($user, $pakset) && !empty($user) ) {
             
      $v_att['gen_attr']['dialog_data']['edit'] = $LNG_ADMIN[51];

      include('./tpl_script/edit_object_dialog.php');
      $dialog_data = func_dialog("edit.php");
      $v_att['gen_attr']['dialog_data']['value_dialog'] = $dialog_data;

    }      
  } else 
  { $v_att['gen_attr']['object']['gui_th1'] = $LNG_EDIT[7];
    $v_att['gen_attr']['object']['gui_th2'] = $LNG_EDIT[8];
    $i = 0;
    //copyright
    if ( $current_ob_copyright !== '' ) 
    { $v_att['gen_attr']['object']['parameter'][$i]['value_pname'] = "Copyright";
      $v_att['gen_attr']['object']['parameter'][$i]['value_pvalue'] = $current_ob_copyright;
      $i++;
    }
    $query="SELECT * FROM `property` WHERE `having_obj_id`=".$current_ob_id." AND `having_version_version_id`='".$current_ob_version."'";
    $result = db_query($query);
    while($row=db_fetch_object($result))
    { $v_att['gen_attr']['object']['parameter'][$i]['value_pname'] = $row->p_name;
      if ( $row->p_name == 'type') $current_ob_obj_subtype = $row->p_value;
      if ( $row->p_name == 'dims') $obj_dims = $row->p_value;
      $v_att['gen_attr']['object']['parameter'][$i]['value_pvalue'] = $row->p_value;

      if ( ($i % 2) == 0 ) $v_att['gen_attr']['object']['parameter'][$i]['line_css'] = 'bg_trans';  
      else                 $v_att['gen_attr']['object']['parameter'][$i]['line_css'] = 'bg_grey';  
      $i++;
      if ( $row->p_name == "type" ) $current_ob_obj_subtype = $row->p_value;
    }
    db_free_result($result);

    if ( !isset($obj_dims) || $obj_dims == '' ) $obj_dims = '1,1'; 
    if (set_show_img($current_ob_version))
    {  $v_att['gen_attr']['object']['object_image'] = display_image($current_ob_version,$current_ob_id, $obj_dims, $current_ob_obj_type);
    } else 
    { //display "no image" image
      $v_att['dummy_img'] = $LNG_EDIT[9];
    }

    if ( in_array($user, $pakset) && !empty($user) )
    { $v_att['gen_attr']['dialog_data']['edit'] = $LNG_ADMIN[51];
      include('./tpl_script/edit_object_dialog.php');
      $result2 = db_query($query);
      $dialog_data = func_dialog("edit.php", $result2);
      $v_att['gen_attr']['dialog_data']['value_dialog'] = $dialog_data;
    }
  }
 
 
 echo $v_template->generate($v_att, 'string');
 unset($v_att);
?>
