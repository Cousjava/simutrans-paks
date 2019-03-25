<?php
//require_once('./include/pcltemplate/pcltemplate.class.php'); 

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_version.htm');
  // ----- Prepare data
  $v_att = array();

  
  $v_att['list_version']['page_title'] = 'Versions';
  $v_att['list_version']['bez_new_set'] = 'Add new version';
  
  $sql = "SELECT * FROM `versions` ORDER BY `version_id`;";
  $query =db_query($sql);
  
  // Spaltenüberschriften
  $v_att['list_version']['bez_setid'] = 'version_id';
  $v_att['list_version']['bez_set_name'] = 'v_name';
  $v_att['list_version']['bez_setsize'] = 'tile_size';
  $v_att['list_version']['bez_maintainter'] = 'maintainer';
  $v_att['list_version']['bez_comaintainter2'] = 'maintainer2';
  $v_att['list_version']['bez_comaintainter3'] = 'maintainer3';

  $x = 0;
  while ($row=db_fetch_array($query)) {
    // Daten Tabellenzeilen
    $v_att['list_version']['tbl_lines'][$x]['value_setid'] = $row['version_id'];
    $v_att['list_version']['tbl_lines'][$x]['value_set_name'] = $row['v_name'];
  
    $v_att['list_version']['tbl_lines'][$x]['value_setsize'] = $row['tile_size'];
    $v_att['list_version']['tbl_lines'][$x]['value_maintainter'] = $row['maintainer_user_id'];
    $v_att['list_version']['tbl_lines'][$x]['value_maintainter2'] = $row['maintainer_user_id2'];
    $v_att['list_version']['tbl_lines'][$x]['value_maintainter3'] = $row['maintainer_user_id3'];
    
    //$style = (($line_number % 2) == 0)?'bg_trans':'bg_grey';
    if ( ($x % 2) == 0 ) {
      $v_att['list_version']['tbl_lines'][$x]['line_css'] = 'bg_trans';  
    } else {
      $v_att['list_version']['tbl_lines'][$x]['line_css'] = 'bg_grey';  
    }
    
    $x++; 
      
    
  }
  
  db_free_result($query);

  echo $v_template->generate($v_att, 'string');
  unset($v_att);

?>
