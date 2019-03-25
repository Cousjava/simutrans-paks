<?PHP

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/setinfo.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] =  $page_titel[$title];


    $lang_count = db_one_field_query ("SELECT count(*) FROM `languages`;");
    // Sets
    $query = db_query("SELECT * FROM `versions` ORDER BY `version_id` ASC;");
    
    
    $x = 0;
    while ($row=db_fetch_array($query)) {

      $v_att['setlist'][$x]['bez_set'] = $LNG_EDIT[1];
      $v_att['setlist'][$x]['bez_maintainter'] = $LNG_INFO[1];
      $v_att['setlist'][$x]['bez_objects'] = $LNG_INFO[3];
      $v_att['setlist'][$x]['set_status']['bez_status'] = $LNG_INFO[4];
      $v_att['setlist'][$x]['bez_translated'] = $LNG_INFO[7];
      $v_att['setlist'][$x]['bez_opensource'] = $LNG_ADMIN[41]; 
      $v_att['setlist'][$x]['bez_lizenz'] = $LNG_ADMIN[42]; 
      //$v_att['bez_homepage'] = $LNG_ADMIN[43]; 
          
      $v_att['setlist'][$x]['set_name'] = $row['v_name'];
    
      if ( ($row['version_id'] < 90 && $row['version_id'] != HELP_BASE__SET_ID) || $row['version_id'] == EXTE_TEXTS_SET_ID ) {
        $v_att['setlist'][$x]['set_id']['bez_id'] = "ID";
        $v_att['setlist'][$x]['set_id']['id'] = $row['version_id']; 
      }
    
      if ( $row['htmllink'] != NULL ) { 
        $v_att['setlist'][$x]['set_homepage'] = $row['htmllink'];  
        if ( (strlen($row['htmllink']) > 25) && (!strpos($row['htmllink'], '/') === false) ) {
          $r = explode("/", $row['htmllink']);
          $v_att['setlist'][$x]['homepage_name'] = $r[0]." ... ".$r[count($r)-1];
        } else {
          $v_att['setlist'][$x]['homepage_name'] = $row['htmllink'];
        }
        
      }
      $v_att['setlist'][$x]['set_maintainter'] = $row['maintainer_user_id'];
      if ( $row['maintainer_user_id2'] != NULL ) { 
        $v_att['setlist'][$x]['set_comaintainter']['bez_comaintainter'] = $LNG_INFO[2];
        $v_att['setlist'][$x]['set_comaintainter']['set_maintainter2'] = $row['maintainer_user_id2']; 
      }
      if ( $row['maintainer_user_id3'] != NULL ) { 
        $v_att['setlist'][$x]['set_comaintainter']['set_maintainter3'] = ", ".$row['maintainer_user_id3']; 
      }

      $objs = db_one_field_query ("SELECT count(*) FROM `objects` WHERE `version_version_id`=".$row['version_id']);
      $v_att['setlist'][$x]['set_objects'] = $objs;
       
      if ( $row["activ"] == 1 ) { 
        $v_att['setlist'][$x]['set_status']['set_activ'] = $LNG_INFO[5];
      } else {
        $v_att['setlist'][$x]['set_status']['set_noactiv'] = $LNG_INFO[6];
      }
        
      $tab = 'translations_'.$row['version_id'];
      $translated_count = db_one_field_query ("SELECT count(*) FROM `".$tab."` WHERE `object_version_version_id`='".$row['version_id']."' AND `tr_text` <> '';");
    
      if ( $objs != 0 ) {
        $z = ( $translated_count * 100 ) / ($objs * $lang_count);
      } else {
        $z = 0;
      }
      $v_att['setlist'][$x]['set_tranlated'] = round($z,2);
      
      if ( $row['open_source'] == 0) {
          $v_att['setlist'][$x]['set_opensource'] = $LNG_FORM[38];
      } else {
          //$opensource = $LNG_FORM[37];
          if ( $row['open_source_link'] == NULL ) {
            $v_att['setlist'][$x]['set_opensource'] = $LNG_FORM[37];
          } else {
            $r = explode("/", $row['open_source_link']);
            $v_att['setlist'][$x]['set_opensource'] = '<a href="http://'.$row['open_source_link'].'" target="_blank" >'.$r[0]." ... ".$r[count($r)-1].'</a>'; 
          }
      }
      //$v_att['setlist'][$x]['set_opensource'] = $opensource; 
      
      if ( $row['license'] == 0 ) {
        $license = $LNG_ADMIN[44];
      } else {
        $sql1 = "SELECT * FROM `licenses` WHERE `license_id`='".$row['license']."';";
        $query1 = db_query($sql1);
        $row1=db_fetch_array($query1);
        if ( $row1['license_link'] == NULL ) {
          $license = $row1['license_name'];
        } else {
          $license = '<a href="'.$row1['license_link'].'" target="_blank">'.$row1['license_name'].'</a>';
        }
      }
      $v_att['setlist'][$x]['set_lizenz'] = $license; 
    
      $x++;
    }
    db_free_result($query);


// ----- Generate result in a string
$v_result = $v_template->generate($v_att, 'string');
echo $v_result;

?>
