<?PHP

function func_dialog($formlink, $data = '') {

  global $LNG_EDIT, $LNG_ADMIN;
  global $current_ob_copyright, $current_ob_name, $current_ob_obj_type, $current_ob_note, $current_ob_id, $current_ob_version, $current_obj_index;
  global $building_city, $building_player, $building_cur, $way_type, $object_text;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/edit_object_dialog.htm');
  // ----- Prepare data
  $v_att_dialog = array();             
    
  $v_att_dialog['formlink'] = $formlink;
  $v_att_dialog['gui_th1'] = $LNG_EDIT[7];
  $v_att_dialog['gui_th2'] = $LNG_EDIT[8];
  $v_att_dialog['bez_save_object'] = $LNG_ADMIN[53];
  
  $v_att_dialog['object_id'] = $current_ob_id;
  $v_att_dialog['value_index'] = $current_obj_index;
  
  $i = 0; 
  // Objectname
  $v_att_dialog['object_data'][$i]['value_pname'] = "Objectname";
  $v_att_dialog['object_data'][$i]['value_pvalue'] = $current_ob_name;
  $text_len = strlen($current_ob_name); 
  if ( $text_len > 50 ) 
  {  $v_att_dialog['object_data'][$i]['text_field']['cols'] = 50;
     $v_att_dialog['object_data'][$i]['text_field']['rows'] = 3;
     $v_att_dialog['object_data'][$i]['text_field']['textfield_name'] = "new_obj_name";
     $v_att_dialog['object_data'][$i]['text_field']['value_textfield'] = $current_ob_name;
  }  else
  {  $v_att_dialog['object_data'][$i]['input_field']['size'] = max(30,$text_len);        
     $v_att_dialog['object_data'][$i]['input_field']['input_name'] = "new_obj_name";
     $v_att_dialog['object_data'][$i]['input_field']['value_pvalue'] = $current_ob_name;
  }
  $i++;
  
  $sel_obj = array();
  $sel_obj['value_pname']  = "Obj";
  $sel_obj['value_pvalue'] = $current_ob_obj_type;
  $sel_obj['select_field']['select_name'] = "new_obj";  
  $sel_obj['select_field']['list_options'] = select_box('',load_obj_typ_tab($current_ob_version),$current_ob_obj_type,'',-2);

  if ( $data == '' )
  { $v_att_dialog['show_property'] = 'style="display:none;"';

    $v_att_dialog['object_data'][$i] = $sel_obj;
    $v_att_dialog['object_data'][$i]['line_css'] = 'bg_grey';  
    $i++;

    // note field
    $v_att_dialog['object_data'][$i]['line_css'] = 'bg_trans';
    $v_att_dialog['object_data'][$i]['value_pname'] = $LNG_EDIT[5];
    $v_att_dialog['object_data'][$i]['value_pvalue'] = $current_ob_note;  
    $v_att_dialog['object_data'][$i]['text_field']['cols'] = 54;
    $v_att_dialog['object_data'][$i]['text_field']['rows'] = 3;
    $v_att_dialog['object_data'][$i]['text_field']['textfield_name'] = "note_text";
    $v_att_dialog['object_data'][$i]['text_field']['value_textfield'] = $current_ob_note;
    $i++;
  } else 
  { //copyright
    $v_att_dialog['object_data'][$i]['line_css'] = 'bg_grey';  
    $v_att_dialog['object_data'][$i]['value_pname'] = "Copyright";
    $v_att_dialog['object_data'][$i]['value_pvalue'] = $current_ob_copyright;
    $v_att_dialog['object_data'][$i]['input_field']['input_name'] = "copyright";
    $v_att_dialog['object_data'][$i]['input_field']['value_pvalue'] = $current_ob_copyright;
    $i++;

    $v_att_dialog['object'] = $current_ob_obj_type;  

    $i = 0;  
    $v_att_dialog['prop_table_left']['parameter_data'][$i]= $sel_obj;
    $i++;

    $p_half_count = (db_num_rows($data) +2) / 2;
    while($row=db_fetch_object($data))
    { if ( $row->p_name == 'type') $current_ob_obj_subtype = $row->p_value; 
    
      if ( $i < $p_half_count ) $table_pos = 'prop_table_left';
      else                      $table_pos = 'prop_table_right';

      $v_att_dialog[$table_pos]['gui_th1'] = $LNG_EDIT[7];
      $v_att_dialog[$table_pos]['gui_th2'] = $LNG_EDIT[8];

      $v_att_dialog[$table_pos]['parameter_data'][$i]['value_pname']  = $row->p_name;
      $v_att_dialog[$table_pos]['parameter_data'][$i]['value_pvalue'] = $row->p_value;

      if ( ($i % 2) == 0 ) $v_att_dialog[$table_pos]['parameter_data'][$i]['line_css'] = 'bg_trans';  
      else                 $v_att_dialog[$table_pos]['parameter_data'][$i]['line_css'] = 'bg_grey';  
        
      $input_p_name = 'p_upd_'.bin2hex($row->p_name); // send $p_name with bin2hex because constraint[prev][0] -> php read this as array 
      $sel_tab = '';
      if ( $row->p_name == 'waytype' ) $sel_tab = $way_type;
      if ( $row->p_name == 'type' && $current_ob_obj_type =="building" ) $sel_tab = array_merge($building_city,$building_cur,$building_player);
      if ($sel_tab != '')
      { $v_att_dialog[$table_pos]['parameter_data'][$i]['select_field']['select_name'] = $input_p_name;
        $v_att_dialog[$table_pos]['parameter_data'][$i]['select_field']['list_options'] = select_box('',$sel_tab,$row->p_value,'',-2);
      } else
      { $v_att_dialog[$table_pos]['parameter_data'][$i]['input_field']['size'] = 15;
        $v_att_dialog[$table_pos]['parameter_data'][$i]['input_field']['input_name'] = $input_p_name;
        $v_att_dialog[$table_pos]['parameter_data'][$i]['input_field']['value_pvalue'] = $row->p_value;
      }

      $i++;
    }
  }

  $dialog_data = $v_template->generate($v_att_dialog, 'string');
  return $dialog_data;
}

?>
