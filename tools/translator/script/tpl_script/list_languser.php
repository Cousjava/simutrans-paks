<?PHP

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_languser.htm');
  // ----- Prepare data
  $v_att = array();


  $v_att['listlanguser']['gui_set'] = $LNG_EDIT[1];
  $v_att['listlanguser']['gui_data'] = $LNG_ADMIN[26];

  $v_att['listlanguser']['gui_subtitle'] = $LNG_ADMIN[27];

  $v_att['listlanguser']['gui_create_set'] = $LNG_ADMIN[30];
  $v_att['listlanguser']['value_langid'] = $_GET['lid'];

  $v_att['listlanguser']['value_langname'] = $LNG_LANGUAGE[$_GET['lid']];

  $sql = "SELECT * FROM `lang_maintaint` WHERE `lang_id`='".$_GET['lid']."' ORDER BY `set_id`;";
  $query = db_query($sql);

  $x = 0;
  while ($row=db_fetch_array($query)) {
    if ( $row['data'] == '' ) { $row['data'] = '&nbsp;'; }
    if ( $row['data1'] == '' ) { $row['data1'] = '&nbsp;'; }
    if ( $row['data2'] == '' ) { $row['data2'] = '&nbsp;'; }
  

    $link = sprintf ('<a href="admin.php?action=languser&lid=%s&did=%s">%s</a>'
      ,$_GET['lid']
      ,$row['id']
      ,$versions_all[$row['set_id']]
    );
    $v_att['listlanguser']['activ'][$x]['value_set'] = $link;

    $v_att['listlanguser']['activ'][$x]['value_data'] = $row['data'];
    $v_att['listlanguser']['activ'][$x]['value_data1'] = $row['data1'];
    $v_att['listlanguser']['activ'][$x]['value_data2'] = $row['data2'];

    if ( ($x % 2) == 0 ) {
      $v_att['listlanguser']['activ'][$x]['line_css'] = 'bg_trans';  
    } else {
      $v_att['listlanguser']['activ'][$x]['line_css'] = 'bg_grey';  
    }

    $x++;
  }
  
  db_free_result($query);

  echo $v_template->generate($v_att, 'string');

?>
