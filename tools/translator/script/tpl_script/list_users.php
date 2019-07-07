<?php
//require_once('./include/pcltemplate/pcltemplate.class.php'); 

function print_user($obj, $id, $strlist) {
  global $v_att;

  if ( $obj['real_name'] == '' ) { $obj['real_name'] = '&nbsp;'; }
  if ( $obj['email'] == '' ) { $obj['email'] = '&nbsp;'; }
  if ( $obj['note'] == '' ) { $obj['note'] = '&nbsp;'; }
  
  $v_att['listuser'][$strlist][$id]['value_nr'] = $id + 1;
  
  $v_att['listuser'][$strlist][$id]['value_userid'] = $obj['u_user_id'];
  $v_att['listuser'][$strlist][$id]['value_realname'] = $obj['real_name'];
  $v_att['listuser'][$strlist][$id]['value_email'] = $obj['email'];
  
  $t = get_set_enabled($obj['u_user_id']);    
  if ( count($t) > 0 ) { 
    $v_att['listuser'][$strlist][$id]['value_sets'] = implode(', ', $t);
  } else {
    $v_att['listuser'][$strlist][$id]['value_sets'] = '&nbsp;';
  }    
  $v_att['listuser'][$strlist][$id]['value_langs'] = get_user_langs($obj['u_user_id']);
  $v_att['listuser'][$strlist][$id]['value_role'] = $obj['role'];
  $v_att['listuser'][$strlist][$id]['value_note'] = $obj['note'];
  //$v_att['listuser'][$strlist][$id]['value_state'] = $obj['state'];
  $v_att['listuser'][$strlist][$id]['value_edit_objects'] = $obj['user_points'];
  $v_att['listuser'][$strlist][$id]['value_uploads_translating'] = $obj['user_points_upload'];
  $v_att['listuser'][$strlist][$id]['value_lastedit_upload'] = $obj['last_edit'];
  $v_att['listuser'][$strlist][$id]['value_lastlogin'] = $obj['last_login'];
  
  if ( ($id % 2) == 0 ) {
    $v_att['listuser'][$strlist][$id]['line_css'] = 'bg_trans';  
  } else {
    $v_att['listuser'][$strlist][$id]['line_css'] = 'bg_grey';  
  }
   
}

function show_logfile() {
  global $v_att;


  $file = '../data/set_admin_user.log';
    if ( file_exists($file) ) { 
      $data = file($file);

      rsort($data); 

      if ( count($data) < 10 ) { $r = count($data); } else { $r = 10; }

      for ( $x = 0; $x < $r; $x++ ) {
        $data2 = explode('|', $data[$x]);
        if ( $data2[3] == 'delete' ) {
          $link = $data2[2];
        } else {
          $link = '<a href="admin.php?action=user&id='.$data2[2].'">'.$data2[2].'</a>';
        }
        $v_att['value_message']['messages'][$x]['message'] = $data2[0].' - '.$data2[1].' - '.$link.' - '.$data2[3];
      }
    }

}

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_user.htm');
  // ----- Prepare data
  $v_att = array();

  if ( !isset($_POST['modifid']) && !isset($_POST['delete']) ) { show_logfile(); }

  $v_att['listuser']['gui_head_activ'] = $LNG_ADMIN[22];
  $v_att['listuser']['gui_head_suspended'] = $LNG_ADMIN[23];
  $v_att['listuser']['gui_head_removed'] = $LNG_ADMIN[24];

  $v_att['listuser']['gui_userid'] = $LNG_ADMIN[25];
  $v_att['listuser']['gui_realname'] = $LNG_USER[10];
  $v_att['listuser']['gui_email'] = $LNG_USER[11];
  $v_att['listuser']['gui_sets'] = $LNG_USER[27];
  $v_att['listuser']['gui_langs'] = $LNG_USER[3];
  $v_att['listuser']['gui_role'] = $LNG_USER[4];
  $v_att['listuser']['gui_note'] = $LNG_EDIT[5];
  $v_att['listuser']['gui_state'] = $LNG_USER[5];
  $v_att['listuser']['gui_edit_objects'] = $LNG_ADMIN[33];
  $v_att['listuser']['gui_uploads_translating'] = $LNG_ADMIN[34];
  $v_att['listuser']['gui_lastedit_upload'] = $LNG_ADMIN[35];
  $v_att['listuser']['gui_lastlogin'] = $LNG_ADMIN[36];
  $v_att['listuser']['gui_servertime'] = $LNG_ADMIN[37];

//  $v_att['listuser']['gui_'] = '';
//  $v_att['listuser']['gui_'] = '';

  $v_att['listuser']['gui_subtitle'] = $LNG_ADMIN[20];

  $v_att['listuser']['gui_create_user'] = $LNG_ADMIN[21];


  $sql = "SELECT * FROM `users` ORDER BY  `state`, LOWER(`u_user_id`) ASC;";
  $query = db_query($sql);

  $d = array( 0, 0, 0);
  $old_ancor = '';
  $letter = array( 0, 0, 0);
  while ($row=db_fetch_array($query)) {
    //echo $row['u_user_id'].'</br>';  
       
    $first_letter = strtolower(substr($row['u_user_id'], 0, 1));
    if ( $old_ancor != $first_letter ) {
      $old_ancor = $first_letter;
      $new_ancor = 1;
    } else {
      $new_ancor = 0;
    }
    
    if ( $row['state'] == 'active' ) { 
      print_user($row, $d[0], 'activusers');
      if ( $new_ancor == 1 ) {
        $v_att['listuser']['activusers_ancors'][$letter[0]]['letter'] = $first_letter;
        if ( $d[0] == 0 ) { 
          $v_att['listuser']['value_ancor_a'] = $first_letter;
        } else {
          $v_att['listuser']['activusers'][$d[0] - 1]['value_ancor'] = $first_letter;
        }
        $letter[0]++;
      }
      $d[0]++;
    } elseif ( $row['state'] == 'suspended' ) { 
      print_user($row, $d[1], 'suspendedusers');
      if ( $new_ancor == 1 ) {
        $v_att['listuser']['suspendedusers_ancors'][$letter[1]]['letter'] = $first_letter;
        if ( $d[1] == 0 ) { 
          $v_att['listuser']['value_ancor_s'] = $first_letter;
        } else {
          $v_att['listuser']['suspendedusers'][$d[1] - 1]['value_ancor'] = $first_letter;
        }
        $letter[1]++;
      }
      $d[1]++;
    } elseif ( $row['state'] == 'removed' ) { 
      print_user($row, $d[2], 'removedusers');
      if ( $new_ancor == 1 ) {
        $v_att['listuser']['removedusers_ancors'][$letter[2]]['letter'] = $first_letter;
        if ( $d[2] == 0 ) { 
          $v_att['listuser']['value_ancor_r'] = $first_letter;
        } else {
          $v_att['listuser']['removedusers'][$d[2] - 1]['value_ancor'] = $first_letter;
        }
        $letter[2]++;
      }
      $d[2]++;
    }   

  }

  $v_att['listuser']['value_activusers'] = $d[0];
  $v_att['listuser']['value_suspendedusers'] = $d[1];
  $v_att['listuser']['value_removedusers'] = $d[2];

  $v_att['listuser']['value_usercount'] = ($d[0] + $d[1] + $d[2]);

  echo $v_template->generate($v_att, 'string');
?>
