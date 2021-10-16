<?php

   
  $v_att_t['gui_title'] = $LNG_USER[20];
  $v_att_t['gui_subtitle'] = $LNG_USER[19];

  $v_att_t['gui_password'] = $LNG_LOGIN[16];
  $v_att_t['gui_retype_password'] = $LNG_USER[24];

  $v_att_t['gui_subtitle2'] = $LNG_HEAD[8];

  $v_att_t['submit_pass'] = $LNG_ADMIN[0];
  $v_att_t['submit_user'] = $LNG_EDIT[19];
    
    /* user data read from db */
    $sql = sprintf ("SELECT * FROM `users` WHERE `u_user_id`='%s';"
      ,$user
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att_t['value_message']['messages'][$err]['message'] = $LNG_USER[8];
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      $err++; 
      $v_att_t['value_message']['messages'][$err]['message'] = $sql; 
      return;
    }
    $user_id=$row['u_user_id'];

    $v_att_t['value_userid'] = $user;

    $v_att_t['gui_realname'] = $LNG_USER[10];
    $v_att_t['value_realname'] = $row['real_name'];

    $v_att_t['gui_email'] = $LNG_USER[11];
    $v_att_t['value_email'] = $row['email'];
    
    /* user language */
    $langar =  get_langs();
    $v_att_t['gui_userlang'] = $LNG_MAIN[23];
    $v_att_t['value_userlang'] = $row["user_lang"];
    for ( $x = 0; $x < count($langar); $x++ ) {
        //display option
        $t = $langar[$x];
        if ( $t == $row["user_lang"] ) { 
          $v_att_t['userlang'][$x]['value_select'] = "selected='selected'";
        } else { 
          $v_att_t['userlang'][$x]['value_select'] = "";            
        }
        
        $v_att_t['userlang'][$x]['value_userlang'] = $t;
        $v_att_t['userlang'][$x]['option_userlang'] = $t." - ".$LANG_NAMEN[$t];
    }
    
    /* user note */
    $v_att_t['gui_note'] = $LNG_EDIT[5];
    $v_att_t['value_note'] = $row['note'];

    /* 
      config1 - prev/next
      config2 - language sort
      config3 - unused
      config4 - obj per page
    */
    $v_att_t['gui_config1'] = $LNG_USER[13];
    $v_att_t['gui_config2'] = $LNG_USER[14];
    $v_att_t['gui_config3'] = $LNG_USER[15];
    $v_att_t['gui_config4'] = $LNG_FORM[17];

    $v_att_t['value_config1'] = $row['config1'];
    $v_att_t['value_config2'] = $row['config2'];
    $v_att_t['value_config3'] = $row['config3'];
    $v_att_t['value_config4'] = $row['config4'];

    /* user reference language */
    $v_att_t['gui_userreflang'] = $LNG_EDIT[20];
    $v_att_t['value_userreflang'] = $row["ref_lang"];
    for ( $x = 0; $x < count($langar); $x++ ) {
        //display option
        $t = $langar[$x];
        if ( $t == $row['ref_lang'] ) { 
          $v_att_t['userreflang'][$x]['value_select'] = "selected='selected'";
        } else { 
          $v_att_t['userreflang'][$x]['value_select'] = "";            
        }
        
        $v_att_t['userreflang'][$x]['value_userreflang'] = $t;
        $v_att_t['userreflang'][$x]['option_userreflang'] = $t." - ".$LANG_NAMEN[$t];
    }

    /* pref for languages */  
    $v_att_t['gui_languages'] = $LNG_USER[3];
    $sql="SELECT `language_id`,`language_name` FROM `translate` T JOIN `languages` L ON T.lng_tr_language_id=L.language_id WHERE `translator_user_id`='".$user."' ORDER BY `language_id`;";
    $x = 0;
    $query = db_query($sql);
    while ($row2=db_fetch_array($query)) {
      $sql2=sprintf ("SELECT * FROM `translate` "
        ." WHERE `lng_tr_language_id`='%s' and `translator_user_id`='%s'"
        ,$row2['language_id']
        ,$user
      );
      $qry2 = db_query($sql2);
      
      $v_att_t['editlang'][$x]['value_langid'] = $row2['language_id'];
      $v_att_t['editlang'][$x]['value_langname'] = $LNG_LANGUAGE[$row2['language_id']];
      $x++;
    }
    db_free_result($query); 
  

    /* pref for sets */
    $v_att_t['gui_sets'] = $LNG_USER[27];
    $qry2 = get_set_enabled ($user);  
    $x = 0;
    if ( $qry2[0] != 'all' ) {
      for ($x = 0; $x < count($qry2); $x++ ) {
        $v_att_t['editsets'][$x]['value_set'] = $versions_all[$qry2[$x]];
      }
    } else {
      foreach($versions_all as $vs_a_name) {
        $v_att_t['editsets'][$x]['value_set'] = $vs_a_name;
        $x++;
      }
    }
    
    /* user role */
    $v_att_t['gui_role'] = $LNG_USER[4];
    $v_att_t['value_role'] = $row['role'];
    
    /* account status */
    $v_att_t['gui_state'] = $LNG_USER[5];
    $v_att_t['value_state'] = $row['state'];


?>
