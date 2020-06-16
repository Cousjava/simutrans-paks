<?PHP

$minimal_user_level=array('admin','pakadmin');
$user_type = trim($_SESSION['role']);

if ( !compare_userlevels($minimal_user_level, $user_type) ) {
    include('tpl_script/main.php');
} else {

    // ----- Create the template object
    $v_template = new PclTemplate();

    // ----- Parse the template file
    $v_template->parseFile('tpl/admin_set.htm');

  if ( isset($_GET['set']) ) {
    $set_id = intval($_GET['set']);
    
    show_set($set_id);
  } elseif ( $_POST['mode'] == "submit" ) {
    $set_id = intval($_POST['version_id']);
    version_edit();
    show_set($set_id);
  }
  
}

function version_edit() {
  global $set_id;

    $sql = sprintf ("SELECT `version_id` FROM `versions` WHERE `version_id`='%s';"
      ,$set_id
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      echo sprintf ("Version does not exist (version '%s')...<br>\n",$_POST['version_id']);
      return;
    }
    printf ("Updating record (version '%s')...<br>\n",$_POST['ver_name']);

  // update Set
    $sql_string="UPDATE `versions` SET `v_name`='%s',`tile_size`=%d, `activ`=%s," .
      " `maintainer_user_id`='%s',  `maintainer_user_id2`='%s', `maintainer_user_id3`='%s'," .
      " `maintainer_user_id4`='%s', `maintainer_user_id5`='%s', `maintainer_user_id6`='%s'," .
      " `open_source`=%s, `open_source_link`='%s', `license`='%s', `htmllink`='%s', `lng_disabled`='%s', `show_images`='%s'  " .
      " WHERE `version_id`='%s';";
  
    if ( $_POST['set_maintainer2'] == 'none' ) { $u2 = NULL; } else { $u2 = $_POST['set_maintainer2']; }
    if ( $_POST['set_maintainer3'] == 'none' ) { $u3 = NULL; } else { $u3 = $_POST['set_maintainer3']; }
    if ( $_POST['set_maintainer4'] == 'none' ) { $u4 = NULL; } else { $u4 = $_POST['set_maintainer4']; }
    if ( $_POST['set_maintainer5'] == 'none' ) { $u5 = NULL; } else { $u5 = $_POST['set_maintainer5']; }
    if ( $_POST['set_maintainer6'] == 'none' ) { $u6 = NULL; } else { $u6 = $_POST['set_maintainer6']; }
    if ( $_POST['license'] == 'none' )         { $lc = NULL; } else { $lc = $_POST['license']; }

    if ( $_POST['showimages'] == '0' ) { $showimg = NULL; } else { $showimg = $_POST['showimages']; }

  // languages disabled
    $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_id`;";
    $query = db_query($sql);
    $langlist = array();
    while ($row2=db_fetch_array($query)) {
      if ( isset($_POST['translates_'.$row2['language_id']]) ) { 
         $langlist[$row2['language_id']] = $row2['language_id']; 
      }
    }
    $setlang = implode("|", $langlist);

  
    $sql = sprintf ($sql_string
      ,db_real_escape_string($_POST['ver_name'])
      ,db_real_escape_string($_POST['tile_size'])
      ,db_real_escape_string($_POST['setstatus'])
      ,db_real_escape_string($_POST['set_maintainer'])
      ,db_real_escape_string($u2)
      ,db_real_escape_string($u3)
      ,db_real_escape_string($u4)
      ,db_real_escape_string($u5)
      ,db_real_escape_string($u6)
      ,db_real_escape_string($_POST['opensource'])
      ,db_real_escape_string(trim($_POST['opensource_link']))
      ,db_real_escape_string($lc)
      ,db_real_escape_string(trim($_POST['homepage']))
      ,db_real_escape_string($setlang)
      ,db_real_escape_string($showimg)
      ,$set_id
    );
    
    $updatequery = db_query($sql);

}

function show_set($setid) {
  global $ar_setsize, $v_template, $LNG_INFO, $LNG_MANAGE, $LNG_FORM, $LNG_LANGUAGE, $LNG_ADMIN, $user_type;

  // ----- Prepare data
  $v_att = array();


  $v_att['bez_setid'] = 'Set ID'; 
  $v_att['bez_setname'] = 'Set Name'; 
  $v_att['bez_setstatus'] = $LNG_INFO[4]; 
  $v_att['bez_setsize'] = $LNG_FORM[8]; 
  $v_att['none'] = 'none'; 
  $v_att['bez_maintainter'] = $LNG_INFO[1]; 
  $v_att['bez_comaintainter'] = $LNG_INFO[2];
  $v_att['bez_show_images'] = $LNG_ADMIN[55];   
  $v_att['bez_opensource'] = $LNG_ADMIN[41]; 
  $v_att['bez_lizenz'] = $LNG_ADMIN[42]; 
  $v_att['bez_homepage'] = $LNG_ADMIN[43]; 
  $v_att['bez_disabled_lang'] = $LNG_MANAGE[18]; 
  $v_att['enabled'] = $LNG_MANAGE[0]; 
  $v_att['disabled'] = $LNG_MANAGE[1]; 
  $v_att['yes'] = $LNG_FORM[37]; 
  $v_att['no'] = $LNG_FORM[38]; 
  $v_att['submit_button'] = $LNG_FORM[42]; 

  // select Set
  $sql = "SELECT * FROM `versions` WHERE `version_id`=$setid;";
  $query = db_query($sql);
  $row = db_fetch_array($query);  
    
  $v_att['page_title'] = $LNG_ADMIN[40].' - '.$row['v_name'];//$page_titel[$title];

  $v_att['value_setid'] = $row['version_id']; 
  $v_att['value_ver_name'] = $row['v_name']; 

  if ( $row['activ'] == 1 ) {
    $v_att['value_setstatus'] = $LNG_MANAGE[0];
    $v_att['value_enabled_yes'] = "SELECTED";
    $v_att['value_disabled_yes'] = " ";
  } else {
    $v_att['value_setstatus'] = $LNG_MANAGE[1];
    $v_att['value_enabled_yes'] = " ";
    $v_att['value_disabled_yes'] = "SELECTED";
  }

  // begin select lists grafics size
  $v_att['value_setsize'] = $row['tile_size']; 
  for ( $x = 0; $x < count($ar_setsize); $x++ ) {
    $v_att['setsize'][$x]['select_size'] = ($row['tile_size']==$ar_setsize[$x]?'SELECTED':' ');
    $v_att['setsize'][$x]['value_setsize'] = $ar_setsize[$x];
  }
  // end select lists grafics size

  // show fields different role admin and pakadmin-maintainter
  if ( $user_type == 'admin' or  
      ($user_type == 'pakadmin' and $_SESSION['userId'] == $row['maintainer_user_id'] ))
  { $v_att['display_maintainter'] = 'display:block;';
    $v_att['display_size']        = 'display:block;';
    $v_att['display_name']        = 'display:block;';
    $v_att['display_opensource']  = 'display:block;';
    $v_att['display_license']     = 'display:block;';
  } else
  { $v_att['display_maintainter'] = 'display:none;';
    $v_att['display_size']        = 'display:none;';
    $v_att['display_name']        = 'display:none;';
    $v_att['display_opensource']  = 'display:none;';
    $v_att['display_license']     = 'display:none;';
  }
  
  // begin select lists maintainter user
  $sql="SELECT `u_user_id`,`real_name` FROM `users` WHERE `state`='active' AND (`role`='pakadmin' OR `role`='admin') ORDER BY LOWER(`u_user_id`) ASC;";
  $query = db_query($sql);
  $x = 0;
  while ($usr=db_fetch_array($query)) 
  { // name maintainter
    if ( $usr['u_user_id'] == $row['maintainer_user_id'] )
    { $v_att['maintainter'][$x]['select_m'] = 'SELECTED';
      $v_att['value_maintainter'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    // selected Maintainter 2
    if ( $usr['u_user_id'] == $row['maintainer_user_id2'] )
    { $v_att['maintainter'][$x]['select_m2'] = 'SELECTED';
      $v_att['value_maintainter2'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    // selected Maintainter 3
    if ( $usr['u_user_id'] == $row['maintainer_user_id3'] ) 
    { $v_att['maintainter'][$x]['select_m3'] = 'SELECTED';
      $v_att['value_maintainter3'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    // selected Maintainter 4
    if ( $usr['u_user_id'] == $row['maintainer_user_id4'] ) 
    { $v_att['maintainter'][$x]['select_m4'] = 'SELECTED';
      $v_att['value_maintainter4'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    // selected Maintainter 5
    if ( $usr['u_user_id'] == $row['maintainer_user_id5'] ) 
    { $v_att['maintainter'][$x]['select_m5'] = 'SELECTED';
      $v_att['value_maintainter5'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    // selected Maintainter 6
    if ( $usr['u_user_id'] == $row['maintainer_user_id6'] ) 
    { $v_att['maintainter'][$x]['select_m6'] = 'SELECTED';
      $v_att['value_maintainter6'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    }
    $v_att['maintainter'][$x]['value_maintainter'] = $usr['u_user_id'];
    $v_att['maintainter'][$x]['name_maintainter'] = $usr['u_user_id'].' ('.$usr['real_name'].')';
    $x++;
  }
  db_free_result($query);
  // end select lists maintainter user
  
  /* show images
     1 - public 
     2 - registered ( tr1, tr2, painter, pakadmin, admin )
     3 - developer ( painter, pakadmin, admin )
  */
  if ( $row['show_images'] == 1 ) {
    $v_att['value_images'] = $LNG_ADMIN[56];
    $v_att['select_all'] = 'SELECTED';
  } elseif ( $row['show_images'] == 2 ) {
    $v_att['value_images'] = $LNG_ADMIN[57];
    $v_att['select_registered'] = 'SELECTED';    
  } elseif ( $row['show_images'] == 3 ) {
    $v_att['value_images'] = $LNG_ADMIN[58];
    $v_att['select_dev'] = 'SELECTED';    
  } else {
    $v_att['value_images'] = $LNG_ADMIN[58];
    $v_att['select_dev'] = 'SELECTED';    
  }
  $v_att['value_image_all'] = $LNG_ADMIN[56];    
  $v_att['value_image_registered'] = $LNG_ADMIN[57]." ( tr1, tr2, painter, pakadmin, admin )";    
  $v_att['value_image_dev'] = $LNG_ADMIN[58]." ( painter, pakadmin, admin )";    

  // open source
  if ( $row['open_source'] == 1 ) {
    $v_att['value_opensource'] = $LNG_FORM[37];
    $v_att['value_opensource_yes'] = "SELECTED";
    $v_att['value_opensource_no'] = " ";
    $v_att['value_opensource_link'] = $row['open_source_link'];
  } else {
    $v_att['value_opensource'] = $LNG_FORM[38];
    $v_att['value_opensource_yes'] = " ";
    $v_att['value_opensource_no'] = "SELECTED";
  }
  
  // lizenz
  // begin select lists licenses
  $sql = 'SELECT * FROM `licenses`;';
  $query = db_query($sql);
  $x = 0;
  while ($usr=db_fetch_array($query)) {
    if ( $usr['license_id'] == $row['license'] ) {
      $v_att['value_license_name'] = $usr['license_name'];
      $v_att['value_license_link'] = db_real_escape_string($usr['license_link']);
    }
    $v_att['license_list'][$x]['license_name'] = ' ';
    if ( $usr['license_id'] == $row['license'] ) {
      $v_att['license_list'][$x]['select'] = 'SELECTED';
    }
    $v_att['license_list'][$x]['license_id'] = $usr['license_id'];
    $v_att['license_list'][$x]['license_name'] = $usr['license_name'];
    $x++;
  }
  db_free_result($query);
  // end select lists licenses

  // begin disabled languages
  $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_id` ASC;";
  $query = db_query($sql);
  $x = 0;
  while ($lng=db_fetch_array($query)) {
    
    if ( empty($row['lng_disabled']) ) {
      $qry2 = array();
    } else {
      $qry2 = explode("|", $row['lng_disabled']);
    }
  
    $v_att['langlist'][$x]['lang_id'] = $lng['language_id'];
    $v_att['langlist'][$x]['lang_name'] = $LNG_LANGUAGE[$lng['language_id']];
    if ( in_array($lng['language_id'], $qry2) ) {
      $v_att['langlist'][$x]['lang_checked'] = " CHECKED";
    }
    $x++;
  }
  db_free_result($query);
  // end disabled languages

  $v_att['value_homepage'] = ' ';
  if ( $row['htmllink'] != NULL ) { 
    $v_att['value_homepage'] = $row['htmllink']; 
  }

  echo $v_template->generate($v_att, 'string');
}

?>
