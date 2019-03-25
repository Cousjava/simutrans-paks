<?php
require_once("./include/parameter.php");
require_once("./include/quotes.inc.php");
include ('./include/obj.php');
include ('./include/translations.php');
include ('./include/select.php');
include ('./include/images.php');
  
////////////////////////////////////////////////////////////////////////////
//accessible to anyone

  //header, please no output before this (sends header information)
  //establishes the connection to the db, include dblib
  $title = 'Gallery';
  require_once ('./tpl_script/header.php');

  $settab = array();
  $settab['0']   = 'Original Größe';
  $settab['64']  = 'auf 64 verkleiner';
  $settab['128'] = 'auf 128 verkleiner';
  $settab['256'] = 'auf 256 vergrößern';


    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file   
    $v_template->parseFile('./tpl/gallery.htm');  
    // ----- Prepare data
    $v_att = array();

    //prints page title
    $v_att['page_title'] = $page_titel[$title];


  $version_auswahl = select_box_read_version();
  // load and translate table 
  $climatrans = array();
  foreach ($climat as $e) $climatrans[$e] = tr_translate_text($version_auswahl,$e);


  $obj_auswahl     = select_box_read_obj($version_auswahl);
  $obj_sub_auswahl = select_box_read_sub_obj($version_auswahl,$obj_auswahl);
  $re_size         = select_box_read("select_box_grafic_size",$settab,'0',-1);
  $climate_auswahl = select_box_read('select_box_climates',$climatrans,255,-1);
  $trange          = select_box_read_trange();

  $v_att['form_head'] = $LNG_FORM[0]; 
  
  select_box_all($version_auswahl,$obj_auswahl,$obj_sub_auswahl);
  if ($obj_auswahl == 'factory' or $obj_auswahl == 'building') 
  { select_box('select_box_climates',$climatrans,$climate_auswahl,'',-1,$LNG_STATS_VEH[9]);
  }
  $v_att['trange'] = $trange;
  select_box("select_box_grafic_size",$settab,$re_size,'',-1);

  $v_att['button_submit'] = $LNG_FORM[45]; 

 
      
  if (isset($_POST['txt_s']) and $_POST['txt_s'] == $LNG_FORM[45])
  { // Objekte suchen
    $set_allowed = set_show_img($version_auswahl);
    if ($version_auswahl != 255 and $obj_auswahl != 255 and $set_allowed) 
    { $v_att['bez_set'] = $LNG_EDIT[1];
      $v_att['value_set'] = $versions_all[$version_auswahl];
      if ( $obj_auswahl == 255  ) $obj_search = "";
      else                        $obj_search = " AND o.obj='".$obj_auswahl."' ";
      $join = ''; $sql_sub_obj = subobject_querry($join,$obj_auswahl,$obj_sub_auswahl);
      $sql="SELECT obj_name, obj, object_id  FROM objects o $join 
            WHERE version_version_id =".$version_auswahl.$obj_search.$sql_sub_obj.
          " ORDER BY obj, obj_name COLLATE utf8_unicode_ci ASC";       
      $res = db_query($sql);
      $obj_tab = array();
      $obj_inx = 0;

      
 
      while ($object = db_fetch_array($res))
      { $current_ob_id = $object['object_id'];
        $dims = '1,1'; $intro_year=1900; $retire_year=2999; $climates='';
        $property_q = db_query ("SELECT p_name, p_value FROM property WHERE having_obj_id=$current_ob_id");
        while ($row = db_fetch_row($property_q))
        { if ($row[0] == 'intro_year')        $intro_year   = $row[1];
          if ($row[0] == 'retire_year')       $retire_year  = $row[1];
          if ($row[0] == 'climates')          $climates     = $row[1];
          if ($row[0] == 'dims')              $dims         = $row[1];
        }
        if ($dims == '') $dims = '1,1,1';
      
        if ($retire_year < $trv or $intro_year > $trb) continue;
        if ($climate_auswahl != 255 and strlen($climates) > 3 and strpos($climates,$climate_auswahl) === false) continue;

         $obj_tab[$obj_inx] = $current_ob_id; 
         
         $img = display_image($version_auswahl,
                              $current_ob_id,
                              $dims,
                              $obj_auswahl,
                              $object['obj_name'],
                              $re_size);
         
         $v_att['gallery_table']['objects'][$obj_inx]['object_id'] = $current_ob_id;
         $v_att['gallery_table']['objects'][$obj_inx]['obj_inx']   = $obj_inx;
         $v_att['gallery_table']['objects'][$obj_inx]['obj_name']  = $img;
         
        $obj_inx++;

         
      } 
               
      if ($obj_inx == 0) { $v_att['no_objects']['value_no_objects'] = $LNG_EDIT[22]; }

      $_SESSION['search_result_tab'] = $obj_tab;
      $_SESSION['search_result_len'] = $obj_inx;

    } else
    { if ($version_auswahl == 255) $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[5];
      if ($obj_auswahl == 255)     $v_att['value_message']['messages'][]['message'] = $LNG_STATS_VEH[1];
      if (!$set_allowed)           $v_att['value_message']['messages'][]['message'] = $LNG_EDIT[27];
    }
  }
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;
  unset($v_att);     

  
////////////////////////////////////////////////////////////////////////////
//footer, nothing after this (closes the page)
include_once ('./tpl_script/footer.php');
?>
