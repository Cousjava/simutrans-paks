<?php

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/stats_translations.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] = $page_titel[$title];

  select_box("select_language",$language_all,$lang,'',-1,$LNG_FORM[43]);

  // gui texts
  $v_att['gui_setselect'] = $LNG_STATS_TRANS[10];
  $v_att['gui_translateinfo'] = $LNG_STATS_TRANS[6];
  $v_att['gui_translateinfo2'] = 'unnecessary_text';
  $v_att['gui_table_head_language'] = $LNG_MAIN[23];
  $v_att['gui_table_head_translators'] = $LNG_STATS_TRANS[7];
  $v_att['gui_table_head_total'] = $LNG_STATS_TRANS[4];
  $v_att['gui_table_head_obj'] = $LNG_STATS_TRANS[5];
  $v_att['gui_table_head2'] = $LNG_STATS_TRANS[8];
  
  $v_att['gui_submit'] = $LNG_FORM[16];
 
  // default select
  if ( empty($set1) ) { $set1 = 0; }
  if ( empty($set2) ) { $set2 = 10; }
  if ( empty($set3) ) { $set3 = 1; }
  if ( empty($set4) ) { $set4 = 19; }

  $version_sql = 'SELECT `v_name`,`version_id` FROM `versions` ORDER BY `version_id` ASC';
  $db_result = db_query($version_sql);
 
  $srt = 0;
  // not select set
    $v_att['set1'][$srt]['opt_value'] = '255';
    $v_att['set1'][$srt]['opt_name'] = $LNG_FORM[7];

    $v_att['set2'][$srt]['opt_value'] = '255';
    $v_att['set2'][$srt]['opt_name'] = $LNG_FORM[7];

    $v_att['set3'][$srt]['opt_value'] = '255';
    $v_att['set3'][$srt]['opt_name'] = $LNG_FORM[7];

    $v_att['set4'][$srt]['opt_value'] = '255';
    $v_att['set4'][$srt]['opt_name'] = $LNG_FORM[7];
  
  while ($row=db_fetch_array($db_result))
  {   
    if ( $row['version_id'] != 10 ) {
    $srt++;
  // Set 1
      $v_att['set1'][$srt]['opt_value'] = $row['version_id'];
      if ( $row['version_id'] == $set1 ) {
        $v_att['set1'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set1'][$srt]['opt_name'] = $row['v_name'];
  // Set 2
      $v_att['set2'][$srt]['opt_value'] = $row['version_id'];
      if ( $row['version_id'] == $set2 ) {
        $v_att['set2'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set2'][$srt]['opt_name'] = $row['v_name'];
  // Set 3
      $v_att['set3'][$srt]['opt_value'] = $row['version_id'];
      if ( $row['version_id'] == $set3 ) {
        $v_att['set3'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set3'][$srt]['opt_name'] = $row['v_name'];
  // Set 4
      $v_att['set4'][$srt]['opt_value'] = $row['version_id'];
      if ( $row['version_id'] == $set4 ) {
        $v_att['set4'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set4'][$srt]['opt_name'] = $row['v_name'];


   }

 // Help files
   if ( $row['version_id'] == 0 ) { 
      $srt++;
  // Set 1
      $v_att['set1'][$srt]['opt_value'] = '10';
      if ( $set1 == 10 ) {
        $v_att['set1'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set1'][$srt]['opt_name'] = 'Help files';
  // Set 2
      $v_att['set2'][$srt]['opt_value'] = '10';
      if ( $set2 == 10 ) {
        $v_att['set2'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set2'][$srt]['opt_name'] = 'Help files';
  // Set 3
      $v_att['set3'][$srt]['opt_value'] = '10';
      if ( $set3 == 10 ) {
        $v_att['set3'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set3'][$srt]['opt_name'] = 'Help files';
  // Set 4
      $v_att['set4'][$srt]['opt_value'] = '10';
      if ( $set4 == 10 ) {
        $v_att['set4'][$srt]['opt_select'] = 'selected="selected"';
      }
      $v_att['set4'][$srt]['opt_name'] = 'Help files';
   }

      $setname[$row['version_id']] = $row['v_name'];
   
  }
  db_free_result($db_result);

  $v_att['listtable'] = 'none';

  if (isset( $_POST['list']) ) {
    $v_att['listtable'] = 'block';

    $total = 0;
    $lngs = 0;

    $aw_lang = '';
    if ( $lang != '255' ) {
      $aw_lang = "Where `language_id`='en' OR `language_id`='$lang' ";
      if (isset($_SESSION['ref_lang'] )) { $aw_lang .= "OR `language_id`='".$_SESSION['ref_lang']."' "; }
    }
    $query = db_query ("SELECT DISTINCT `language_id`, `language_name` FROM `languages` $aw_lang ORDER BY `language_name` ASC;");

    $lng_sort = array();
    while ($lang_row = db_fetch_array($query))
    {
        //total is cumulatively counted, we are interested in last value
        $lng_sort[$lang_row['language_id']] = $LNG_LANGUAGE[$lang_row['language_id']];
    }
    natcasesort($lng_sort);

    for ( $x = 0; $x < count( $lng_sort ); $x++ )
    {    
        //total is cumulatively counted, we are interested in last value
        
        $total = statistic_for_one_language (key($lng_sort), current($lng_sort), $x);
        next($lng_sort);
        $lngs ++;
    }
    db_free_result($query);

    $total_objs = $total_count * $lngs; 
    
    $z = ( $total * 100 ) / $total_objs;
    $total = $total.' ( '.round($z,2).' % )';

    
    $v_att['total_obj_count'] = $total_count;
    $v_att['full_stats'] = sprintf($LNG_STATS_TRANS[2], $total, $total_objs );
    
  }

  // ----- Include token
  // The token is not a single value but an array with :
  // - The filename to include ('filename').
  // - The values of the tokens for the included file ('values').
  $v_att['info_box']['filename'] = './tpl/info_box.htm';
  $v_att['info_box']['values']['message_0'] = $LNG_STATS_TRANS[3];

  $v_att['submenu1'] = $LNG_MAIN[21];
  $v_att['submenu1_link'] = 'main.php?lang='.$st.'&page=stats_menu';
  $v_att['submenu2'] = $LNG_MAIN[20];
  $v_att['submenu2_link'] = 'main.php';

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  // ----- Display result
  echo $v_result;


?>
