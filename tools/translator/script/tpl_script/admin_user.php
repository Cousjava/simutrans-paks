<?php

function user_info ($user) {
  global $minimal_user_level, $userstates;
  global $LNG_USER, $LNG_ADMIN, $LNG_ADMIN2, $LNG_USER, $LNG_LANGUAGE, $LNG_FORM, $LNG_LOGIN, $LNG_EDIT, $versions_all;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_user.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;
  $GET_mode = "";
  if (isset($_GET['mode'])) $GET_mode = $_GET['mode'];

  $v_att['formuser']['gui_realname'] = $LNG_USER[10];
  $v_att['formuser']['gui_email'] = $LNG_USER[11];
  $v_att['formuser']['gui_sets'] = $LNG_USER[27];
  $v_att['formuser']['gui_langs'] = $LNG_USER[3];
  $v_att['formuser']['gui_role'] = $LNG_USER[4];
  $v_att['formuser']['gui_note'] = $LNG_EDIT[5];
  $v_att['formuser']['gui_state'] = $LNG_USER[5];

  $v_att['formuser']['gui_password'] = $LNG_LOGIN[16];
  $v_att['formuser']['gui_retype_password'] = $LNG_USER[24];

  $v_att['formuser']['gui_create_user'] = $LNG_ADMIN[9];

  $v_att['formuser']['submit_password'] = $LNG_USER[19];

  if ($GET_mode == 'add') {
    $v_att['formuser']['gui_subtitle2'] = $LNG_ADMIN[38];
    $v_att['formuser']['value_mode'] = 'add';
    $v_att['formuser']['newuser'] = $LNG_LOGIN[15];
    $v_att['formuser']['submit_save'] = $LNG_ADMIN2['create'];
  } else {
    $v_att['formuser']['gui_subtitle'] = sprintf($LNG_USER[23], $user);
    $v_att['formuser']['gui_subtitle2'] = sprintf($LNG_USER[6], $user);
    $v_att['formuser']['value_mode'] = 'edit';
    if (!isset($user)) {
      // message: 'No user selected.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[7];
      echo $v_template->generate($v_att, 'string');
      return;
    }
    $sql = sprintf ("SELECT `u_user_id`,`real_name`,`email`,`role`,`note`,`state` ".
      " FROM `users` WHERE `u_user_id`='%s';"
      ,$user
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      // message: 'Cannot get record.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_ADMIN[12];
      $mes++;
      // message: sql string
      $v_att['value_message']['messages'][$mes]['message'] = $sql;
      echo $v_template->generate($v_att, 'string');
      return;
    }

    $v_att['formuser']['value_userid'] = $row['u_user_id'];
    $v_att['formuser']['value_realname'] = $row['real_name'];
    $v_att['formuser']['value_email'] = $row['email'];
    $v_att['formuser']['value_userrole'] = $row['role'];
    $v_att['formuser']['value_note'] = $row['note'];
    $v_att['formuser']['value_userstate'] = $row['state'];

    $v_att['formuser']['submit_save'] = $LNG_ADMIN2['modify'];

    if ($GET_mode != 'add' ) {
        $v_att['formuser']['submit_delete'] = $LNG_ADMIN2['delete'];
    }
  }

  for ( $x = 1; $x < count( $minimal_user_level ); $x++ ) {
    if ($GET_mode != 'add' and $minimal_user_level[$x] == $row['role'] ) { 
      $v_att['formuser']['userrole'][($x - 1)]['value_select'] = 'selected'; 
    }
    $v_att['formuser']['userrole'][($x - 1)]['value_userrole'] = $minimal_user_level[$x];
  }

  for ( $x = 0; $x < count( $userstates ); $x++ ) {

    if ($GET_mode != 'add' and $userstates[$x] == $row['state'] ) { 
      $v_att['formuser']['userstates'][$x]['value_select'] = 'selected'; 
    }
    
    $v_att['formuser']['userstates'][$x]['value_userstate'] = $userstates[$x];
  }
  // create user lang array
  $sql = "SELECT * FROM `translate` WHERE `translator_user_id`='".$user."'";
  $query = db_query($sql);
  $user_lang = array();
  while ($row2=db_fetch_array($query)) {
      $user_lang[] = $row2['lng_tr_language_id'];
  }  
  db_free_result($query);

  $x = 0;
  $y = 0;
  $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_id`;";
  $query = db_query($sql);
  while ($row2=db_fetch_array($query)) {
    $x++;
    if ( $x == 7 ) { 
      $v_att['formuser']['langs'][$y]['value_br'] ='<br>'; 
      $x = 1;
    }
    if ( in_array($row2['language_id'], $user_lang) ) { 
      $v_att['formuser']['langs'][$y]['value_select'] = 'CHECKED'; 
    }
    
    $v_att['formuser']['langs'][$y]['value_langid'] = $row2['language_id'];
    $v_att['formuser']['langs'][$y]['value_langname'] = $LNG_LANGUAGE[$row2['language_id']];
    
    $y++;
  }

  db_free_result($query);
  
  $x = 0;
  $y = 0;
  
  $qry2 = get_set_enabled ($user);
  if ( $qry2[0] == 'all' and $GET_mode != 'add')  $t = 1;  else  $t = 0;   
  foreach($versions_all as $vs_a_id => $vs_a_name) {
    $x++;
    if ( $x == 7 ) { 
      $v_att['formuser']['sets'][$y]['value_br'] = '<br>'; 
      $x = 1; 
    }
    if ( in_array(strval($vs_a_id), $qry2) OR $t == 1 ) { 
      $v_att['formuser']['sets'][$y]['value_select'] = 'CHECKED'; 
    }

    $v_att['formuser']['sets'][$y]['value_setid'] = $vs_a_id;
    $v_att['formuser']['sets'][$y]['value_setname'] = $vs_a_name;

    $y++;
  }

  echo $v_template->generate($v_att, 'string');

}

function user_post () {
  global $LNG_ADMIN, $LNG_ADMIN2, $LNG_USER, $LNG_FORM,$versions_all;

    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file
    $v_template->parseFile('./tpl/admin_user.htm');
    // ----- Prepare data
    $v_att = array();
  
    $mes = 0;
  
  if ( isset($_POST['delete']) and $_POST['delete'] == $LNG_ADMIN2['delete'] ) {
    // message: "Deleting record (user '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[19], $LNG_ADMIN[31], $_POST['user_id']);
    $mes++;

    $sql="DELETE FROM `users` WHERE `u_user_id`='%s';";
    $user=$_POST['user_id'];
    $sql = sprintf ($sql,$_POST['user_id']);
    $deletequery = db_query($sql);
    
    // message: 'OK'
    $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];

    echo $v_template->generate($v_att, 'string');
    
      // Date and Time | User | changed user | 
      $t = date("Y-m-d H:i:s", time());
      $data = $t.'|'.$_SESSION['userId'].'|'.$user.'|delete'."\n";
      write_log('admin_user', $data);

    return;
  }

  if ($_POST['mode'] == 'add') {
    if (empty($_POST['new_user_id'])) {
      // message: 'Enter an user ID.'
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[13], $LNG_ADMIN[31]);  
      echo $v_template->generate($v_att, 'string');
      return;
    }

    $user=$_POST['user_id'];

    $sql = sprintf ("SELECT `u_user_id` FROM `users` WHERE `u_user_id`='%s';", $_POST['new_user_id'] );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
      // message: "Record already exists (user '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[14], $LNG_ADMIN[31], $_POST['new_user_id']);
      echo $v_template->generate($v_att, 'string');
      return;
    }
    db_free_result($query);
    if ($_POST['pass'] != $_POST['pass2']) {
      // message: 'Password does not match.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[22];
      return;
    }
    // message: "Creating record (user '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[15], $LNG_ADMIN[31], $_POST['new_user_id']);
    $mes++;

    $sql="INSERT INTO `users` (`real_name`,`email`,`role`,`note`,`state`,`u_user_id`,`pass_bin`,`last_login`,`last_edit`,`ref_lang`,`user_lang`,`set_enabled`) ".
      " VALUES ( '%s','%s','%s','%s','%s','%s','%s',0,0,'','','' );";
    $user=$_POST['new_user_id'];

  }
  
  if ($_POST['mode'] == 'edit') {
    if (empty($_POST['user_id'])) {
      // message: 'No user selected.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[7];
      echo $v_template->generate($v_att, 'string');
      return;
    }
    $sql = sprintf ("SELECT `u_user_id` FROM `users` WHERE `u_user_id`='%s';", $_POST['user_id'] );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      // message: "Record does not exist (user '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[14], $LNG_ADMIN[31], $_POST['user_id']);
      echo $v_template->generate($v_att, 'string');
      return;
    }
    // message: "Updating record (user '%s')...";
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[18], $LNG_ADMIN[31], $_POST['user_id']);
    $mes++;

    $sql="UPDATE `users` SET `real_name`='%s',`email`='%s',`role`='%s',`note`='%s',`state`='%s', `set_enabled`='%s' ".
      " WHERE `u_user_id`='%s';";
    $user=$_POST['user_id'];

  }
  if ($_POST['mode']=='pass') {
    if (!isset($_POST['user_id'])) {
      // message: 'No user selected.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[7];
      echo $v_template->generate($v_att, 'string');
      user_info ($user);
      return;
    }
    $user=$_POST['user_id'];
    $sql = sprintf ("SELECT `u_user_id` FROM `users` WHERE `u_user_id`='%s';"
        ,$_POST['user_id']
      );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      // message: "User does not exist (user '%s')"
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_USER[17], $_POST['user_id']);
      echo $v_template->generate($v_att, 'string');

      return;
    }
    if ($_POST['pass'] != $_POST['pass2']) {
      // message: 'Password does not match.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[22];
      echo $v_template->generate($v_att, 'string');
      user_info ($user);
      return;
    }
  }
  // Setstatus
  $t = 0;
  $s = array();
  $sc = 0;
  foreach($versions_all as $vs_a_id => $vs_a_name) {
    if (isset($_POST['userset_'.$vs_a_id])) {
        $s[$t] = strval($vs_a_id);
        $t++;
    }
    $sc++;  
  }
  if ( $sc == $t ) { $usersets = array('all'); } else { $usersets = $s; }
  
  if ( $_POST['mode'] == 'add' || $_POST['modifid'] == $LNG_ADMIN2['modify'] ) {

    if ( $_POST['mode']=='add' ) { 
      $sql = sprintf ($sql
        ,$_POST['real_name']
        ,$_POST['email']
        ,$_POST['role']
        ,$_POST['note']
        ,$_POST['state']
        ,$user
        ,password_hash($_POST['pass'], PASSWORD_DEFAULT)
      );
      $log = 'add user';
    } else {
      $sql = sprintf ($sql
        ,$_POST['real_name']
        ,$_POST['email']
        ,$_POST['role']
        ,$_POST['note']
        ,$_POST['state']
        ,serialize($usersets)
        ,$user
      );
      $log = 'change user';
    }
    $updatequery = db_query($sql);
 
      // Date and Time | User | changed user | 
      $t = date("Y-m-d H:i:s", time());
      $data = $t.'|'.$_SESSION['userId'].'|'.$user.'|'.$log."\n";
      $log ="";
      write_log('admin_user', $data);
      

    $sql=sprintf ("SELECT * FROM `translate` WHERE `translator_user_id`='%s'"
        ,$user
      );
    $query = db_query($sql);
    while ($row2=db_fetch_array($query)) {
      $languages[$row2['lng_tr_language_id']]='on';
    }
    db_free_result($query);

    $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_id`;";
    $query = db_query($sql);
    $log_addlang = "";
    $log_dellang = "";
    while ($row2=db_fetch_array($query)) {
      if (isset($_POST['translates_'.$row2['language_id']])) $on=true; else $on=false;
      $modify=false;
      if ( !isset($languages[$row2['language_id']]) && $on ) {
        $sql="INSERT INTO `translate` (`lng_tr_language_id`,`translator_user_id`) VALUES ('%s','%s');";
        $modify=true;
        $log_addlang .= $row2['language_id']." ";
      }
      if ( isset($languages[$row2['language_id']]) && !$on ) {
        $sql="DELETE FROM `translate` WHERE `lng_tr_language_id`='%s' and `translator_user_id`='%s';";
        $modify=true;
        $log_dellang .= $row2['language_id']." ";
      }
      if ($modify) {
        $modsql=sprintf ($sql, $row2['language_id'], $user);
        $qry2 = db_query($modsql);
      }

    }
    
       // Date and Time | User | changed user | 
      $t = date("Y-m-d H:i:s", time());
      if ( $log_addlang <> "" ) {
        $data = $t.'|'.$_SESSION['userId'].'|'.$user.'| add '.$log_addlang.'language'."\n";
        write_log('admin_user', $data);
      }
      if ( $log_dellang <> "" ) {
        $data = $t.'|'.$_SESSION['userId'].'|'.$user.'| delete '.$log_dellang.'language'."\n";
        write_log('admin_user', $data);
      }

    db_free_result($query);

  }

  if ( $_POST['mode'] == 'add' || $_POST['mode'] == 'pass' ) {
    // message: "Setting password (user '%s')"
    $v_att['value_message']['messages'][$mes]['message'] .= sprintf ($LNG_USER[23], $user);
    $mes++;

    $sql=sprintf ("UPDATE `users` SET `pass_bin`='%s' WHERE `u_user_id`='%s';"
        ,password_hash($_POST['pass'], PASSWORD_DEFAULT)
        ,$user
      );
    $updatequery = db_query($sql);

      // Date and Time | User | changed user | 
      $t = date("Y-m-d H:i:s", time());
      $data = $t.'|'.$_SESSION['userId'].'|'.$user.'|set pass'."\n";
      write_log('admin_user', $data);
  }
  
  echo $v_template->generate($v_att, 'string');

  user_info ($user);

}


?>
