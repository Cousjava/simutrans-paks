<?php

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('./tpl/stats_menu.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] =  $page_titel['stats_menu'];

  $v_att['subtitel'] = $LNG_STATS[1];

  // set info page
  $x = 0;
  // set info page
  $v_att['menulist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=setinfo';
  $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[14];
  $v_att['menulist'][$x]['menu_description'] = $LNG_HEAD[22];

  // statistics vehicle
  //$v_att['menulist'][$x]['menutitel'] = $LNG_STATS[2];
  //$v_att['menulist'][$x]['menu_description'] = $LNG_STATS[3];

  // statistics translations
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'statistics_translations.php?lang='.$st;
  $v_att['menulist'][$x]['menutitel'] = $LNG_STATS[4];
  $v_att['menulist'][$x]['menu_description'] = $LNG_STATS[5];


  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;

?>
