<?PHP


  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/new_text_object.htm');
  // ----- Prepare data
  $v_att_page = array();             
 
  $v_att_page['page_title'] =  $page_titel[$title];

  /* messages */
    
    foreach ($tr_ok_tab as $tr_ok)
    { if ($tr_ok <  10)
      { $e = $LNG_LOAD3[10];                  // successful saved
        if ($tr_ok == 3) $e = $LNG_LOAD3[11]; // written_as_suggestion
      } elseif ($tr_ok <  20) 
      { $e = $LNG_LOAD3[20];                  // text already present
      } else $e = $LNG_LOAD3[$tr_ok];         // all errors
      $v_att_page['value_message']['messages'][]['message'] = $e;
    }

    
    if ( isset($status) ) {
      if ( $status == 'db_error' ) {
        $v_att_page['message'] = "<b>SQL error</b> (cannot insert object row): ".db_error()." SQL: ".$sql_insert_obj;
        $v_att_page['css_message'] = "err_message";

      } elseif ( $status == 'obj_insert' ) {
        $v_att_page['message'] = "Objekt gespeichert";
        $v_att_page['css_message'] = "ok_message";

        $v_att_page['objectlist'] = $version_auswahl;
      
      } elseif ( $status == 'obj_exists' ) {
        $v_att_page['message'] = "Objekt existiert schon";
        $v_att_page['css_message'] = "err_message";

        $v_att_page['objectlist'] = $version_auswahl;
      
      }  
      
    }



    $v_att_page['gui_th1'] = $LNG_EDIT[7];
    $v_att_page['gui_th2'] = $LNG_EDIT[8];
    $v_att_page['bez_save_object'] = $LNG_ADMIN[53];  

    $i = 0; 
      // Objectname
      $v_att_page['object_data'][$i]['line_css'] = line_css($i);  
            
      $v_att_page['object_data'][$i]['value_pname'] = $LNG_LOAD3[6];
      $v_att_page['object_data'][$i]['text_field']['cols'] = 80;
      $v_att_page['object_data'][$i]['text_field']['rows'] = 4;
      $v_att_page['object_data'][$i]['text_field']['textfield_name'] = "obj_name";
      $i++;
      
      // Typ Textobject     
      $v_att_page['object_data'][$i]['line_css'] = line_css($i);  

      $v_att_page['object_data'][$i]['value_pname'] = $LNG_FORM[48];
      $v_att_page['object_data'][$i]['select_field']['select_name'] = "obj";  
        
      $x = 0;
      foreach ($object_text as $ob_text)
      { if ( $ob_text == $obj_type ) $t = "selected='selected'"; 
        else                         $t = "";
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_value'] = $ob_text;  
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_name']  = $ob_text;  
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_select'] = $t;  
        $x++;
      }


      $i++;   
      // note field
      $v_att_page['object_data'][$i]['line_css'] = line_css($i);  
            
      $v_att_page['object_data'][$i]['value_pname'] = $LNG_EDIT[5];
      $v_att_page['object_data'][$i]['text_field']['cols'] = 80;
      $v_att_page['object_data'][$i]['text_field']['rows'] = 4;
      $v_att_page['object_data'][$i]['text_field']['textfield_name'] = "note_text";
      $i++;
      
      // versions field
      $v_att_page['object_data'][$i]['line_css'] = line_css($i);  
            
      $v_att_page['object_data'][$i]['value_pname'] = $LNG_EDIT[1];  
      $v_att_page['object_data'][$i]['select_field']['select_name'] = "version";  
 
      $x = 0;
      foreach ($maintainter as $id)
      { if ( $id == $version_auswahl ) $t = "selected='selected'"; 
        else                           $t = "";
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_value'] = $id;  
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_name'] = $versions_all[$id];  
        $v_att_page['object_data'][$i]['select_field']['list_options'][$x]['opt_select'] = $t; 
        $x++;
      }
       
      $i++;   
    
    /* Translate for user sort languages */
    $v_att_page['gui_th3'] = $LNG_MAIN[23];
    $v_att_page['gui_th4'] = $LNG_INFO[7];
    $x = 0;
    if ( isset($_SESSION['config2']) && strlen(trim($_SESSION['config2'])) > 1  )  {
      $user_lang_sort = explode(',', $_SESSION['config2'] );
      $count = count($user_lang_sort);                      
      for ( $x; $x < $count; $x++ ) {
        //$any=$any|gen_translate(trim($user_lang_sort[$x]), $user_edit, $c);  
        //if ( $any==true ) { $c++; }
        $v_att_page['object_translate'][$x]['line_css'] = line_css($x);  
            
      
        $v_att_page['object_translate'][$x]['value_pname'] = $LNG_LANGUAGE[$user_lang_sort[$x]];
        $v_att_page['object_translate'][$x]['text_field']['cols'] = 80;
        $v_att_page['object_translate'][$x]['text_field']['rows'] = 4;
        $v_att_page['object_translate'][$x]['text_field']['textfield_name'] = "trtext_".$user_lang_sort[$x];
      }
    } else {
      $v_att_page['object_translate'][$x]['value_pname'] = $LNG_LANGUAGE['en'];
      $v_att_page['object_translate'][$x]['text_field']['cols'] = 80;
      $v_att_page['object_translate'][$x]['text_field']['rows'] = 4;
      $v_att_page['object_translate'][$x]['text_field']['textfield_name'] = "trtext_en";
    }
        
 

  $page_data = $v_template->generate($v_att_page, 'string');
  echo $page_data;



?>
