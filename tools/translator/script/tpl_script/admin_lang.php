<?php

function lang_info ($lang) { 
  global $default_lang, $lang_codepage, $v_att, $LANG_CONTACT, $LNG_ADMIN, $LNG_ADMIN2;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_lang.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;

  $v_att['formlang']['gui_lang'] = $LNG_ADMIN[3];
  $v_att['formlang']['gui_lang2'] = $LNG_ADMIN[3]." 2";
  $v_att['formlang']['gui_langname'] = $LNG_ADMIN[4];
  $v_att['formlang']['gui_font1'] = $LNG_ADMIN[5];
  $v_att['formlang']['gui_font2'] = 'font2';
  $v_att['formlang']['gui_codepage'] = $LNG_ADMIN[6];

  $v_att['formlang']['gui_subtitle'] = $LNG_ADMIN[8];

  $v_att['formlang']['gui_create_lang'] = $LNG_ADMIN[9];

  if ( isset($_GET['mode']) && $_GET['mode']=='add') {
    $v_att['formlang']['gui_subtitle2'] = $LNG_ADMIN[9];
    $mode='add';
    $v_att['formlang']['newlang'] = '1';
    $row['language_id']='';
    $language_id='';
    $row['language_name']=$default_lang['name'];
    $row['font1']=$default_lang['font1'];
    $row['font2']=$default_lang['font2'];
    $row['lng_coding']=$default_lang['codepage'];
    $v_att['formlang']['value_fdesc'] = '';
    $v_att['formlang']['gui_submit_save'] = $LNG_ADMIN2['create'];
  } else {
    $v_att['formlang']['gui_subtitle2'] = $LNG_ADMIN[10];
    $mode='edit';
    if (empty($lang)) {
      // message: 'No record selected.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_ADMIN[11];
      echo $v_template->generate($v_att, 'string');

      return;
    }
    $sql = sprintf ("SELECT *  FROM `languages` WHERE `language_id`='%s';"
      ,$lang
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

    $v_att['formlang']['gui_submit_save'] = $LNG_ADMIN2['modify'];
    $v_att['formlang']['gui_submit_delete'] = $LNG_ADMIN2['delete'];
  }
  
  for ( $x = 0; $x < count($lang_codepage); $x++ ) {
    if ( $lang_codepage[$x] == $row['lng_coding'] ) {
      $v_att['formlang']['codepages'][$x]['value_select'] = ' SELECTED';
    }
    $v_att['formlang']['codepages'][$x]['value_opt_codepage'] = $lang_codepage[$x];
  }

  $v_att['formlang']['value_mode'] = $mode;
  $v_att['formlang']['value_lang'] = $row['language_id'];
  $v_att['formlang']['value_langcode2'] = $row['lang_code2'];
  $v_att['formlang']['value_langname'] = $row['language_name'];
  $v_att['formlang']['value_font1'] = $row['font1'];
  $v_att['formlang']['value_font2'] = $row['font2'];
  $v_att['formlang']['value_codepage'] = $row['lng_coding'];

  echo $v_template->generate($v_att, 'string');
}

function lang_post () {
  global $LNG_ADMIN, $LNG_ADMIN2, $LANG_CONTACT, $LNG_FORM;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_lang.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;

  db_query("START TRANSACTION");

  if ( isset($_POST['mode']) && $_POST['mode']=='add') {
    if (empty($_POST['new_language_id'])) {
      // message: 'Enter an language ID.'
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[13], $LNG_ADMIN[28]);
      echo $v_template->generate($v_att, 'string');

      return;
    }
    $lang=$_POST['new_language_id'];
    $sql = sprintf ("SELECT * FROM `languages` WHERE `language_id`='%s';"
      ,$lang
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
      // message: "Record already exists (language '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[14], $LNG_ADMIN[28], $lang);
      echo $v_template->generate($v_att, 'string');

      lang_info ($lang);
      return;
    }
    db_free_result($query);
    // message: "Creating record (language '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[15], $LNG_ADMIN[28], $lang);
    $mes++;

    $sql="INSERT INTO `languages` (`language_name`,`font1`,`font2`,`lng_coding`,`lang_code2`,`language_id`) ".
      " VALUES ( '%s','%s','%s','%s','%s','%s' );";
  }

  if ( $_POST['submit']==$LNG_ADMIN2['modify'] ) {
    if (empty($_POST['language_id'])) {
      // message: 'No record selected.'
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_ADMIN[11];
      echo $v_template->generate($v_att, 'string');

      return;
    }
    $lang=$_POST['language_id'];
    $sql = sprintf ("SELECT * FROM `languages` WHERE `language_id`='%s';", $lang);
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      // message: "Record does not exist (language '%s')..."
      $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[17], $LNG_ADMIN[28], $lang);
      echo $v_template->generate($v_att, 'string');

      return;
    }
    // message: "Updating record (language '%s')..."
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[18], $LNG_ADMIN[28], $lang);
    $mes++;

    $sql="UPDATE `languages` SET `language_name`='%s',`font1`='%s',`font2`='%s',`lng_coding`='%s',`lang_code2`='%s' ".
      " WHERE `language_id`='%s';";
  }

  if ($_POST['submit']==$LNG_ADMIN2['delete']) {
    // message: "Deleting record (language '%s')..." 
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_ADMIN[19], $LNG_ADMIN[28], $_POST['language_id']);
    $mes++;

    $lang=$_POST['language_id'];
    $sql = sprintf ("DELETE FROM `languages` WHERE `language_id`='%s';", $lang);
    $deletequery = db_query($sql);
    // message: 'OK'
    $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];
    
    // delete language from tables translation
    $query= "SELECT * FROM `versions`";
    $result = db_query($query);

    while($row=db_fetch_object($result))
    {      
      $tab = 'translations_'.$row->version_id; 
      $qy = sprintf ("DELETE FROM `%s` WHERE `language_language_id`='%s';", $tab, $lang);
      $deletequery = db_query($qy);
      $mes++;
      $v_att['value_message']['messages'][$mes]['message'] = "delete language ".$lang." from set ".$versions_all[$row->version_id];
    }

    db_free_result($result);
  } else
  { if ( !isset($_POST['lang_font2'] )) { $_POST['lang_font2'] = ''; }

    $sql = sprintf ($sql
      ,db_real_escape_string($_POST['language_name'])
      ,$_POST['lang_font1']
      ,$_POST['lang_font2']
      ,db_real_escape_string($_POST['lang_coding'])
      ,db_real_escape_string($_POST['language_code2'])
      ,$lang);     
    $updatequery = db_query($sql); 

    if ( $_POST['submit']==$LNG_ADMIN2['create'] && !empty($_POST['new_language_id']) ) {
    // new language write to table

      $query= "SELECT * FROM `objects`  ORDER BY `object_id` ASC";
      $result = db_query($query);

      while($row=db_fetch_object($result))
      {      
        $tab = 'translations_'.$row->version_version_id; 
        $qy = "INSERT INTO `".$tab."` (`object_object_id`, `object_obj_name`, `object_version_version_id`, `language_language_id`) ".
          "VALUES ($row->object_id, '".db_real_escape_string($row->obj_name)."', $row->version_version_id, '$lang');";
        db_query($qy);
      }
      db_free_result($result);
    }
  }

  db_query("COMMIT");
  // message: 'OK' 
  $v_att['value_message']['messages'][$mes]['message'] = $LNG_FORM[41];

  echo $v_template->generate($v_att, 'string');

  
  lang_info ($lang);
}

?>
