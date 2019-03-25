<?php
$title="Textimport";
include("./tpl_script/header.php");
//uploading from dat files is accessible to admins and top level tr2 only!
$user=$_SESSION['userId'];
$u_level = array("admin", "tr2", "pakadmin");

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

include_once ('include/pclzip/pclzip.lib.php');
include ('./include/select.php');
include ('./include/obj.php');
include ("include/translations.php");

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file   
  $v_template->parseFile('./tpl/load.htm');  
  // ----- Prepare data
  $v_att = array();
  //prints page title
  $v_att['page_title'] = $LNG_LOAD[1];;

  $settab = array();
  $settab['none']          = $LNG_LOAD2[3];
  $settab['file']          = $LNG_FORM[35];
  $settab['text']          = $LNG_FORM[36];
  
/*
  $userId      = $_SESSION['userId'];
  //access check
  $translate = db_fetch_array(db_query("SELECT count(*) FROM translate WHERE lng_tr_language_id='$language' AND translator_user_id='$userId'"));
  //$user_role = db_one_field_query ("SELECT role FROM users WHERE u_user_id='$userId';");
  $user_role = $_SESSION['role'];
  if (($translate[0]==0) and ($user_role != "admin"))
  {   printf("<b>$LNG_LOAD3[2]<br /></b>", $userId, $language);
        include("include/footer.php");
        die();
    }

*/

  $version_auswahl = select_box_read_version();
  $language        = select_box_read_language();
  $inputtype       = select_box_read('select_box_inputtype',$settab,'none',-1);

  select_box_version($version_auswahl);

  $v_att['lang_titel'] = $LNG_FORM[9];
  select_box("select_language",$language_all,$language,'',-1,$LNG_FORM[10]);

  $v_att['input_titel'] = $LNG_FORM[34];
  select_box('select_box_inputtype',$settab,$inputtype,'',-1); 

  $v_att['submit'] = $LNG_FORM[39];

// entferne Zeilenumbrüche \n   $LNG_LOAD[2];    $LNG_FORM[37];      $LNG_FORM[38]; 

if ($inputtype != 'none')
{ if ($language == '255')      $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[4];
  if ($version_auswahl == 255) $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[5];

  if ($version_auswahl != 255 and $language != '255')
  { $v_att['pak_name']['name'] = $LNG_LOAD2[2].": ".$versions_all[$version_auswahl].", ".$LNG_MAIN[23].": ".$language_all[$language];

    if ($inputtype == "file")
    { $v_att['input_file']['titel'] = $LNG_LOAD2[6];
    } elseif ($inputtype == "text")
    { $v_att['input_text']['titel'] = $LNG_LOAD2[0];
    } else $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[7];
    
    $v_att['info_box2']['filename'] = './tpl/info_box.htm';
    $v_att['info_box2']['values']['css_class'] = "width600";
    $v_att['info_box2']['values']['message_0'] = $LNG_LOAD2[8];

    $v_att['upload_button']['load_up'] = $LNG_LOAD2[1];
    
    if (isset($_POST['load_up']) and $_POST['load_up'] == $LNG_LOAD2[1])
    {

       ///////////////////////////////////////////////////////////////////////////
   //fetching input data and storing them into an array
   //all fetched lines with translations will be stored in an array

   //prepare input
    //INPUT TYPE IS FILE
      if ($inputtype == "file")
      { //get current location of uploaded file (some temp)
        $file_name=$_FILES['uploadfile']['name'];
        echo "File name:".$file_name."<br>\n";
        if ( substr($file_name,-4) == ".zip" )
        { echo "Extracting the ".$file_name." file...<br>\n";

          $tmp = TMP_DIRECTORY.$version_auswahl.'-'.$language;
          $archive = new PclZip($_FILES['uploadfile']['tmp_name']);
          $v_list = $archive->extract(PCLZIP_OPT_PATH,$tmp );
          if ( $v_list == 0) echo $LNG_OBJ_IMPORT[16]."<br>\n";
          foreach ($v_list as $v_entry)
          { $pfile=$v_entry['filename'];
            $file_name = basename($pfile);
            if ($file_name == "_objectlist.txt" or $file_name == "_translate_users.txt") continue;
            echo "<hr>file:".$file_name."<br>";
            if (substr($file_name,-4) == ".tab" and substr($file_name,0,2) != $language)
              printf($LNG_LOAD3[0],$file_name);
            else tr_parsetab($file_name,$pfile,$language,$version_auswahl,3,0,NULL);
          }
          verzeichnis_del($tmp);
        } elseif (substr($file_name,-4) == ".tab" and substr($file_name,0,2) != $language)
           printf($LNG_LOAD3[0],$file_name);
        else tr_parsetab($file_name,$_FILES['uploadfile']['tmp_name'],$language,$version_auswahl,3,0,NULL);
      } elseif ($inputtype == "text")   //INPUT TYPE IS TEXTAREA
      { $inputtext=$_POST["inputtext"];
        // add BOM tag for UTF-8 encoding
        $inputtext = "\xEF\xBB\xBF".nl2br($inputtext);    //insert temporarily <br /> befor end of lines in textarea
        $inp_lines = explode("<br />",$inputtext); //one token will be one line from textarea
        tr_parsetab("screen",$inp_lines,$language,$version_auswahl,3,0,NULL); 
      } else $v_att['value_message']['messages'][]['message'] = $LNG_LOAD2[7];
    }
  }
}

  $v_att['info_box']['filename'] = './tpl/info_box.htm';
  $v_att['info_box']['values']['css_class'] = "width600";

  $v_att['info_box']['values']['message_0'] = $LNG_LOAD[3];
  $v_att['info_box']['values']['messagebox_1']['message_1'] = $LNG_LOAD[4];
  $v_att['info_box']['values']['messagebox_1a']['message_1'] = $LNG_LOAD[5];

  $v_att['go_back'] = $LNG_MAIN[20];

  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;


//end
include("tpl_script/footer.php");
?>
