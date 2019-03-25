<?php

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_licenses.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['listlicenses']['gui_subtitle'] = $LNG_ADMIN[7];     
  
  $v_att['listlicenses']['gui_licenseid'] = "id";     
  $v_att['listlicenses']['gui_license_name'] = $LNG_ADMIN[42];     
  $v_att['listlicenses']['gui_license_link'] = $LNG_ADMIN[16];     
  
  $v_att['listlicenses']['gui_new_licenses'] = $LNG_ADMIN[45];     
 
  $sql = "SELECT * FROM `licenses` ORDER BY `license_name`;";
  $query =db_query($sql);
 
  $x = 0;
  while ($row=db_fetch_array($query)) {
    $v_att['listlicenses']['lineslicenses'][$x]['value_licenseid'] = $row['license_id'];     
    $v_att['listlicenses']['lineslicenses'][$x]['value_license_name'] = $row['license_name'];     
    $v_att['listlicenses']['lineslicenses'][$x]['value_license_link'] = $row['license_link'];  

    if ( ($x % 2) == 0 ) {
      $v_att['listlicenses']['lineslicenses'][$x]['line_css'] = 'bg_trans';  
    } else {
      $v_att['listlicenses']['lineslicenses'][$x]['line_css'] = 'bg_grey';  
    }
    $x++;
  }
  db_free_result($query);

  echo $v_template->generate($v_att, 'string');
?>
