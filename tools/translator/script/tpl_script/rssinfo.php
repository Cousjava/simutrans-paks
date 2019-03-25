<?PHP

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('./tpl/rssinfo.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] =  $page_titel[$title];


  $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_name`;";
  $query = db_query($sql);    
    
    $x = 0;
    while ($row=db_fetch_array($query)) {

      $v_att['rsslist'][$x]['lang_name'] = $LANG_NAMEN[$row['language_id']];
      $v_att['rsslist'][$x]['lang_id'] = $row['language_id'];
      $v_att['rsslist'][$x]['all_items'] = $LNG_RSS_FEED[12];
      $v_att['rsslist'][$x]['lang_items'] = $LNG_RSS_FEED[13];
      $x++;
    }
    db_free_result($query);


// ----- Generate result in a string
$v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;

?>
