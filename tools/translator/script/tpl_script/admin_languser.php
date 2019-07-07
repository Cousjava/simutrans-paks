<?php

function languser_info ($set) {
  global $LNG_USER, $LNG_ADMIN, $LNG_ADMIN2, $LNG_LANGUAGE, $LNG_FORM, $LNG_EDIT, $LNG_LOGIN;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_languser.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;

  $v_att['formlanguser']['gui_set'] = $LNG_EDIT[1];
  $v_att['formlanguser']['gui_data'] = $LNG_ADMIN[26];

  $v_att['formlanguser']['gui_create_user'] = $LNG_ADMIN[9];

  if ( isset($_GET['mode']) && $_GET['mode'] == 'add') {
    $v_att['formlanguser']['gui_subtitle'] = $LNG_ADMIN[30].' '.$LNG_LANGUAGE[$_GET['lid']];
    $v_att['formlanguser']['value_lid'] = $_GET['lid'];
    $v_att['formlanguser']['value_mode'] = 'add';
    // read exists sets in manage language
    $sql = "SELECT * FROM `lang_maintaint` WHERE `lang_id`='".$_GET['lid']."' ORDER BY `set_id`;";
    $query = db_query($sql);
    if ( $query ) {
     while ( $row=db_fetch_array($query) ) {
        $exists_set[] = $row['set_id'];
      }
    }
    // set list without exists sets
    $query = get_versions();
    $x = 0;
    foreach($versions_all as $vs_a_id => $vs_a_name) {
      if ( !in_array($vs_a_id, $exists_set) ) {
        $v_att['formlanguser']['newitem']['sets'][$x]['value_setid'] = $vs_a_id;
        $v_att['formlanguser']['newitem']['sets'][$x]['value_setname'] = $vs_a_name;

        $x++;
      }
    }

    $v_att['formlanguser']['newuser'] = $LNG_LOGIN[15];
    $v_att['formlanguser']['submit_save'] = $LNG_ADMIN2['create'];
  } else {

    $v_att['formlanguser']['gui_subtitle'] = $LNG_ADMIN[32].' '.$LNG_LANGUAGE[$_GET['lid']];
    $v_att['formlanguser']['value_mode'] = 'edit';

    if (!isset($set)) {
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[8];
      echo $v_template->generate($v_att, 'string');
      return;
    }
    
    if ( isset($_POST['did']) ) { 
      $sql = "SELECT * FROM `lang_maintaint` WHERE `id`='".$_POST['did']."';";
    } elseif ( $_GET['did'] ) { 
      $sql = "SELECT * FROM `lang_maintaint` WHERE `id`='".$_GET['did']."';";
    } else { 
      $sql = "SELECT * FROM `lang_maintaint` WHERE `set_id`='".$_POST['set']."' AND `lang_id`='".$_POST['lid']."';";
    }
    
    $query = db_query($sql);
    
    if ( $row=db_fetch_array($query) ) {
      $v_att['formlanguser']['value_lid'] = $row['lang_id'];
      $v_att['formlanguser']['value_did'] = $row['id'];
      $v_att['formlanguser']['edititem']['value_setname'] = $versions_all[$row['set_id']];
      $v_att['formlanguser']['edititem']['value_setid'] = $row['set_id'];

      $select = $row['data'];
      $select1 = $row['data1'];
      $select2 = $row['data2'];

      db_free_result($query);
    } else {
      db_free_result($query);
      // message: 'Cannot get record.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[8];
      $mes++;
      // message: sql string
      $v_att['value_message']['messages'][$mes]['message'] = $sql;
      echo $v_template->generate($v_att, 'string');
      return;
    }

    $v_att['formlanguser']['submit_save'] = $LNG_ADMIN2['modify'];

    if ( isset($_GET['mode']) && $_GET['mode'] != 'add' ) {
        $v_att['formlanguser']['submit_delete'] = $LNG_ADMIN2['delete'];
    }
  }

  $sql="SELECT `u_user_id`,`real_name`,`email` FROM `users` o JOIN `translate` t ON (o.u_user_id=t.translator_user_id) WHERE o.state='active' AND (o.email IS NOT NULL) AND t.lng_tr_language_id='".$_GET['lid']."' ORDER BY LOWER(`u_user_id`) ASC;";
  $query = db_unbuffered_query($sql) or die ('SQL error: '.mysql_error().$sql);

  $x = 0;
  while ($usr=db_fetch_array($query)) {
    if ( $usr['email'] <> '' ) {
      // select field: user 1
      $v_att['formlanguser']['user'][$x]['value_userid'] = $usr['u_user_id'];
      $v_att['formlanguser']['user'][$x]['value_user'] = $usr['u_user_id'].' ( '.$usr['real_name'].' )';
      if ( $select == $usr['u_user_id'] ) {
        $v_att['formlanguser']['user'][$x]['value_select'] = 'selected="selected"';
      }
      // select field: user 2
      $v_att['formlanguser']['user1'][$x]['value_userid'] = $usr['u_user_id'];
      $v_att['formlanguser']['user1'][$x]['value_user'] = $usr['u_user_id'].' ( '.$usr['real_name'].' )';
      if ( $select1 == $usr['u_user_id'] ) {
        $v_att['formlanguser']['user1'][$x]['value_select'] = 'selected="selected"';
      }
      // select field: user 3
      $v_att['formlanguser']['user2'][$x]['value_userid'] = $usr['u_user_id'];
      $v_att['formlanguser']['user2'][$x]['value_user'] = $usr['u_user_id'].' ( '.$usr['real_name'].' )';
      if ( $select2 == $usr['u_user_id'] ) {
        $v_att['formlanguser']['user2'][$x]['value_select'] = 'selected="selected"';
      }
    }

    $x++;
  }
  db_free_result($query);

  echo $v_template->generate($v_att, 'string');
}

function languser_post () {
  global $LNG_ADMIN, $LNG_ADMIN2, $LNG_USER, $LNG_FORM, $LNG_EDIT;

    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file
    $v_template->parseFile('./tpl/admin_languser.htm');
    // ----- Prepare data
    $v_att = array();
  
    $mes = 0;
  
  $set  = $_POST['set'];
  $setname = $versions_all[$_POST['set']];

  if ( $_POST['delete'] == $LNG_ADMIN2['delete'] ) {
    // message: "Deleting record (set '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[19], $LNG_EDIT[1], $setname);
    $mes++;

    $sql="DELETE FROM `lang_maintaint` WHERE `id`='%s';";
    $sql = sprintf ($sql,$_POST['did']);
    $deletequery = db_query($sql);
    // message: 'OK'
    $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];
    echo $v_template->generate($v_att, 'string');
    return;
  }

  if ($_POST['mode'] == 'add') {
    $sql = sprintf ("SELECT `set_id` FROM `lang_maintaint` WHERE `set_id`='%s' AND `lang_id`='%s';"
        ,$_POST['set']
        ,$_POST['lid']
      );
    $query = db_query($sql) or die ('SQL error: '.mysql_error());
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
      // message: "Record already exists (set '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[26], $LNG_EDIT[1], $setname);
      echo $v_template->generate($v_att, 'string');
      return;
    }
    db_free_result($query);

    // message: "Creating record (set '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[15], $LNG_EDIT[1], $setname);
    $mes++;

    $sql="INSERT INTO `lang_maintaint` (`set_id`,`lang_id`,`data`,`data1`,`data2`) ".
      " VALUES ( '%s','%s','%s','%s','%s' );";

    // message: 'OK'
    $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];
  }
  
  if ($_POST['mode'] == 'edit') {
    if (!isset($_POST['set'])) {
      // message: 'Cannot get record.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_USER[8];
      echo $v_template->generate($v_att, 'string');
      return;
    }
    $sql = sprintf ("SELECT `id` FROM `lang_maintaint` WHERE `id`='".$_POST['did']."';"
        ,$_POST['did']
      );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      // message: "Record does not exist (set '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[14], $LNG_EDIT[1], $setname);

      //user_list ();
      echo $v_template->generate($v_att, 'string');
      return;
    }
    // message: "Updating record (set '%s')...";
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[18], $LNG_EDIT[1], $setname);
    $mes++;

    $sql="UPDATE `lang_maintaint` SET `set_id`='%s',`lang_id`='%s',`data`='%s',`data1`='%s',`data2`='%s' ".
      " WHERE `id`='%s';";
    $set=$_POST['set'];

    // message: 'OK'
    $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];
  }

  
  if ( $_POST['mode'] == 'add' || $_POST['modifid'] == $LNG_ADMIN2['modify'] ) {

    if ( $_POST['mode']=='add' ) { 
      $sql = sprintf ($sql
        ,$set
        ,$_POST['lid']
        ,$_POST['data']
        ,$_POST['data1']
        ,$_POST['data2']
      );
    } else {
      $sql = sprintf ($sql
        ,$set
        ,$_POST['lid']
        ,$_POST['data']
        ,$_POST['data1']
        ,$_POST['data2']
        ,$_POST['did']
     );
    }
    $updatequery = db_query($sql);

  }
  
  echo $v_template->generate($v_att, 'string');
  languser_info ($_POST['did']);
}


?>
