<?PHP
//require_once('./include/pcltemplate/pcltemplate.class.php');  

  $mes = 0;
  
function ver_info ($ver) {
  global $mes, $ar_setsize, $LNG_INFO; 

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_version.htm');
  // ----- Prepare data
  $v_att = array();
  
  if ($_GET['mode']=="add") {  
    $v_att['form_add_version']['page_title2'] = 'Add Version';
    $mode="add";
    $v_att['form_add_version']['value_mode'] = $mode; 
    $v_att['form_add_version']['form_setid'] = ''; 
    $v_att['form_add_version']['input_setid'] = '<input type="text" name="new_version_id" value="" size=20>'; 
    $v_att['form_add_version']['value_ver_name'] = 'version name';
    $v_att['form_add_version']['value_maintainter'] = '';
    
    $v_att['form_add_version']['submit_button'] = 'Create';

    //echo $v_template->generate($v_att, 'string');
  } else { 
    /*
    echo ("Edit Version","h2");
    $mode="edit";
    if (!isset($ver)) {
      echo ("No version selected.");
      return;
    }
    $sql = sprintf ("SELECT * FROM `versions` WHERE `version_id`='%s';"
      ,$ver
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      echo ("Cannot get record.<br>\n".$sql);
      return;
    }  
    $version_id=$row['version_id'];
    $submit_text="Modify";

    //    $delete_form = sprintf (form_version_del,$row['version_id']);  
    */
  }


  $v_att['form_add_version']['bez_setid'] = 'Set ID'; 
  $v_att['form_add_version']['bez_ver_name'] = 'Set Name'; 
  $v_att['form_add_version']['bez_setsize'] = 'Grafics size'; 
  $v_att['form_add_version']['bez_maintainter'] = $LNG_INFO[1]; 
  $v_att['form_add_version']['bez_comaintainter'] = $LNG_INFO[2]; 

  // begin select lists grafics size
  //$v_att['value_setsize'] = $row['tile_size']; 
  for ( $x = 0; $x < count($ar_setsize); $x++ ) {
    //$v_att['form_add_version']['setsize'][$x]['select_size'] = ($row['tile_size']==$ar_setsize[$x]?'SELECTED':' ');
    $v_att['form_add_version']['setsize'][$x]['value_setsize'] = $ar_setsize[$x];
  }
  // end select lists grafics size

  
  //echo '<a href="main.php?lang=de&page=admin_set&set='.$row['version_id'].'">new </a>'; 
  $v_att['form_add_version']['bez_setpreferences'] = 'preferences page for sets';  

  echo $v_template->generate($v_att, 'string');
  unset($v_att);
}

function ver_post () { 
  global $mes; 

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_version.htm');
  // ----- Prepare data
  $v_att = array();
  
  
  if ($_POST['mode']=="add") {
    if (!isset($_POST['new_version_id']) or $_POST['new_version_id'] == "") {
      $v_att['value_message']['messages'][$mes]['message'] = "Enter a version ID.";
      //echo $v_template->generate($v_att, 'string');
      return;
    }
    $ver=intval($_POST['new_version_id']);
    $sql = sprintf ("SELECT `version_id` FROM `versions` WHERE `version_id`='%s';"
      ,$ver);
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ("Version already exists (version '%s')...<br>\n",$ver);
      return;
    }
    db_free_result($query);
    $v_att['value_message']['messages'][$mes]['message'] = printf ("Creating record (version '%s')...<br>\n",$ver);
    $mes++;

    $sql="INSERT INTO `versions` (`v_name`,`tile_size`,`version_id`) VALUES ( '%s',%d,%d );";
    // new Table for Set
    $sql_table = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
      `translation_id` int(11) NOT NULL AUTO_INCREMENT,
      `object_object_id` int(11) NOT NULL,
      `object_obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
      `object_version_version_id` int(11) NOT NULL default '0',
      `language_language_id` varchar(5) collate utf8_bin NOT NULL default '',
      `tr_text` text character set utf8 collate utf8_unicode_ci,
      `suggestion` text collate utf8_unicode_ci,
      `mod_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
      `reservator_user_id` varchar(20) collate utf8_bin default NULL,
      `date_to` datetime default NULL,
      `author_user_id` varchar(20) collate utf8_bin NOT NULL default '',
      `update_lock` tinyint(1) default '0',
      `details_text` text CHARACTER SET utf8,
      `details_suggestion` text CHARACTER SET utf8,
      `history_text` text CHARACTER SET utf8,
      `history_suggestion` text CHARACTER SET utf8,
      `history_link_url` varchar(2000) CHARACTER SET utf8  DEFAULT '',
      `history_link_suggestion` varchar(2000) CHARACTER SET utf8  DEFAULT '',
      PRIMARY KEY (`translation_id`),      
      KEY (`object_object_id`),
      KEY (`object_version_version_id`),
      KEY (`language_language_id`), 
      KEY (`object_obj_name`)
    ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;", 
    'translations_'.$ver );
    db_query($sql_table);
    
  $sql_table = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
      `image_id` int(11) NOT NULL AUTO_INCREMENT,
      `object_obj_id` int(11) NOT NULL,
      `object_obj_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
      `object_version_version_id` int(11) NOT NULL default '0',
      `object_obj_type` varchar(100) collate utf8_bin NOT NULL default '',
      `image_name` varchar(255) collate utf8_bin NOT NULL default 'Image[0]',
      `unzoomable` tinyint(1) NOT NULL default '0',
      `image_order` varchar(100) collate utf8_bin NOT NULL default '',
      `image_data` MEDIUMBLOB NULL,
      `tile_size` int(11) NOT NULL default '64',
      `filename` varchar(255) character  set utf8 collate utf8_unicode_ci NOT NULL,
      `offset_x` int(11) NOT NULL default '0',
      `offset_y` int(11) NOT NULL default '0',
    PRIMARY KEY (`image_id`), 
    KEY (`object_obj_name`),
    KEY (`object_obj_id`),
    KEY (`object_version_version_id`),
    KEY (`image_name`)
  ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
    'images_'.$ver );
    db_query($sql_table);
   
    echo $v_template->generate($v_att, 'string');
    unset($v_att);
  }

  if ($_POST['mode']=="edit") {
    if (!isset($_POST['version_id'])) {
      echo "No version selected.";
      return;
    }
    $sql = sprintf ("SELECT `version_id` FROM `versions` WHERE `version_id`='%s';"
      ,$_POST['version_id']
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ("Version does not exist (version '%s')...<br>\n",$_POST['version_id']); 
      echo $v_template->generate($v_att, 'string');
      unset($v_att);
      return;
    }
    $v_att['value_message']['messages'][$mes]['message'] = printf ("Updating record (version '%s')...<br>\n",$_POST['version_id']);
    $mes++;
    
    $sql="UPDATE `versions` SET `v_name`='%s',`tile_size`=%d WHERE `version_id`='%d';";
    $ver=intval($_POST['version_id']);

    echo $v_template->generate($v_att, 'string');
    unset($v_att);
  }
  
  if ($_POST['mode']=="delete") {
    $v_att['value_message']['messages'][$mes]['message'] = printf ("Deleting record (version '%s')...<br>\n",$_POST['version_id']);
    $mes++;
    
    $sql="DELETE FROM `versions` WHERE `version_id`=%d;";
    $ver=intval($_POST['version_id']);
    $sql = sprintf ($sql,$ver);
    $deletequery = db_query($sql);
    $v_att['value_message']['messages'][$mes]['message'] = "OK";
    $mes++;
    return;

    echo $v_template->generate($v_att, 'string');
    unset($v_att);
  }
  
  $sql = sprintf ($sql
    ,$_POST['ver_name']
    ,$_POST['tile_size']
    ,$ver
  );
  $updatequery = db_query($sql);
  $v_att['value_message']['messages'][$mes]['message'] = "OK";
  $mes++;
  

  ver_info ($ver);
  
 
}

?>
