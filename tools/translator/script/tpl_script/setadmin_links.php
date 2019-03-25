<?PHP

// ----- Create the template object
$v_template = new PclTemplate();

// ----- Parse the template file
$v_template->parseFile('./tpl/setadmin_links.htm');
// ----- Prepare data
$v_att = array();


    $x = 0;
    
    // object browser
    $v_att['linklist'][$x]['link_file'] = "./obj_browser.php";
    $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[3];
    $x++;

    // new object 
    $v_att['linklist'][$x]['link_file'] = "./new_object.php";
    $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[19];
    $x++;

    // object import
    $v_att['linklist'][$x]['link_file'] = "./obj_import.php";
    $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[7];
    $x++;

    // file upload
    $v_att['linklist'][$x]['link_file'] = 'file_upload.php';
    $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[21];
    $x++;


   $u_level = array('admin','pakadmin');
   if (compare_userlevels($u_level, $user_type)) {
 
      // logs
      $v_att['linklist'][$x]['link_file'] = "./logs.php";
      $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[17];
      $x++;

      // object purge
      $v_att['linklist'][$x]['link_file'] = "./obj_purge.php";
      $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[11];
      $x++;

      // clean temp folder
      $v_att['linklist'][$x]['link_file'] = "./clean_temp.php";
      $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[15];
      $x++;
    }

  // ----- Generate result in a string
    $v_result = $v_template->generate($v_att, 'string');

  // ----- Display result
    echo $v_result;   

?>
