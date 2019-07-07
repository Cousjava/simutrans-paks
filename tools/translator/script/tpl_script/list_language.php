<?php
//require_once('./include/pcltemplate/pcltemplate.class.php'); 

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_lang.htm');
  // ----- Prepare data
  $v_att = array();


  $v_att['listlang']['gui_lang'] = $LNG_ADMIN[3];
  $v_att['listlang']['gui_lang2'] = $LNG_ADMIN[3]." 2";
  $v_att['listlang']['gui_langname'] = $LNG_ADMIN[4];
  $v_att['listlang']['gui_font1'] = $LNG_ADMIN[5];
  $v_att['listlang']['gui_font2'] = 'font2';
  $v_att['listlang']['gui_codepage'] = $LNG_ADMIN[6];
  $v_att['listlang']['gui_fdesc'] = $LNG_ADMIN[7];

  $v_att['listlang']['gui_subtitle'] = $LNG_ADMIN[8];

  $v_att['listlang']['gui_create_lang'] = $LNG_ADMIN[9];

  $sql = "SELECT * FROM `languages` ORDER BY `language_id`;";
  $query = db_query($sql);

  $x = 0;
  while ($row=db_fetch_array($query)) {
    if ( $row['font1'] == '' ) { $row['font1'] = '&nbsp;'; }
    if ( $row['font2'] == '' ) { $row['font2'] = '&nbsp;'; }
    //if ( $row['f_desc'] == '' ) { $row['f_desc'] = '&nbsp;'; }
    if ( $row['language_name'] == '' ) { $row['language_name'] = '&nbsp;'; }
    if ( $row['lang_code2'] == '' ) { $row['lang_code2'] = '&nbsp;'; }
      else { $v_att['listlang']['languages'][$x]['value_language_code2'] = $row['lang_code2']; }
  
    $v_att['listlang']['languages'][$x]['value_nr'] = $x + 1;

    $link = sprintf ('<a href="admin.php?action=language&id=%s">%s</a>'
      ,$row['language_id']
      ,$row['language_id']
    );
    $v_att['listlang']['languages'][$x]['value_language_id'] = $link; 

    $link = sprintf ('<a href="admin.php?action=language&id=%s">%s</a>'
      ,$row['language_id']
      ,$row['language_name']
    );
    $v_att['listlang']['languages'][$x]['value_language_name'] = $link;

    $link = sprintf ('<a href="admin.php?action=languser&lid=%s">%s</a>'
      ,$row['language_id']
      ,$LNG_ADMIN[2]
    );
    $v_att['listlang']['languages'][$x]['value_language_manage'] = $link;

    $v_att['listlang']['languages'][$x]['value_font1'] = $row['font1'];
    $v_att['listlang']['languages'][$x]['value_font2'] = $row['font2'];
    //$v_att['listlang']['languages'][$x]['value_fdesc'] = substr($row['f_desc'],0,150);
    $v_att['listlang']['languages'][$x]['value_codepage'] = $row['lng_coding'];

    if ( ($x % 2) == 0 ) {
      $v_att['listlang']['languages'][$x]['line_css'] = 'bg_trans';  
    } else {
      $v_att['listlang']['languages'][$x]['line_css'] = 'bg_grey';  
    }

    $x++;
  }
  
  db_free_result($query);

  echo $v_template->generate($v_att, 'string');
?>
