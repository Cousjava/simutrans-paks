<?php
// nachfolgende variablen werden benutzt aber nicht definiert
if (!isset($title))  $title="unset";
if (!isset($page))   $page="page unset";

    /*
    SimuTranslator
    Tomas Kubes 2005

    Header which must be printed on all pages.
    This file must be included before any output is sent to std-out.

    Variable $title=
    must be set before including this file (will be used as page title).

    Variable $minimal_user_level (array)
    fill like $minimal_user_level = array("admin", "tr2");
                                     user list
                                    'tr1','tr2','admin','gu','painter','pakadmin'

    must be set before including this file.
    This value determines minimal user role required to view the page.
    If logged user has lower role, execution of the script will be aborted.
    */


    //general databese functions
    //sends some headers, so do not output anything before (also cares for db connection)
    require_once('./include/parameter.php'); 
    require_once('./include/dblib.php');
    require_once('./include/general.php');
    require_once('./include/pcltemplate/pcltemplate.class.php');

// ----- Create the template object
$v_template = new PclTemplate();

// ----- Parse the template file
$v_template->parseFile('./tpl/header.htm');
// ----- Prepare data
$v_att = array();


$user = '';
if (isset($_SESSION['userId'])) $user = $_SESSION['userId'];

$maintainter = array();
if (isset($_SESSION['maintainter'])) $maintainter = $_SESSION['maintainter'];


$langar = get_langs();

  if (isset($_POST['lang']) ) 
  { $st = $_POST['lang'];
    $_SESSION['user_lang'] = $st;
  } elseif (isset($_GET['lang']) ) 
  { $st = $_GET['lang'];
    $_SESSION['user_lang'] = $st;
  } elseif ( isset($_SESSION['user_lang']) ) 
  { $st = $_SESSION['user_lang'];
  } elseif ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) 
  { if ( in_array(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), $langar) ) 
    { $st = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    } else $st = 'en';
    $_SESSION['user_lang'] = $st;
  } 

// fallback language not set   
if ( !isset($st) or strlen($st) < 2 or strlen($st) > 3 or !in_array($st,$langar) )  $st = 'en'; 
// fallback language not exist translate Translator   
if ( !file_exists('./lang/'.$st.'/lng_main.php') ) { $st = 'en'; }

include ('./lang/'.$st.'/lng_main.php');
include ('./lang/'.$st.'/lng_manage.php');
include ('./lang/'.$st.'/lng_preferences.php');

$user_type = 'guest';
if(isset($_SESSION['role'])) $user_type = $_SESSION['role'];

$page_titel = array();
$page_titel['unset'] = "error title unset";
$page_titel['main'] = $LNG_HEAD[0];
$page_titel['login'] = $LNG_HEAD[11];
$page_titel['wrap'] = $LNG_WRAP[0];
$page_titel['setinfo'] = $LNG_INFO[0];
$page_titel['rssinfo'] = $LNG_HEAD[17];
if ( (isset($_GET['lm']) and $_GET['lm'] == '1') || (isset($_POST['lm']) and$_POST['lm'] == '1' )) {
  $page_titel['contact'] = $LANG_CONTACT[0].' Manager Language';
} else {
  $page_titel['contact'] = $LANG_CONTACT[0].' '.$LANG_CONTACT[6];
}
$page_titel['stats_menu'] = $LNG_STATS[0];
$page_titel['stats_translation'] = $LNG_STATS[4];
$page_titel['Direction'] = $LNG_GUIDE[0];
$page_titel['Textedit'] = $LNG_EDIT[0];
$page_titel['line_edit'] = $LNG_EDIT[0];
$page_titel['Gallery'] = 'Gallery'; //$LNG_EDIT[0];
$page_titel['Pak_Info'] = $LNG_EDIT[0];
$page_titel['Statistics'] = $LNG_HEAD[7];
$page_titel['Administration'] = $LNG_HEAD[9];
$page_titel['Preferences_Set'] = $LNG_ADMIN[40];
$page_titel['Objekt-Manager'] = $LNG_MANAGE[2];
$page_titel['Object_Import'] = $LNG_MANAGE[7];
$page_titel['Objekt-Browser'] = $LNG_OBJ_BROWSER[0];
$page_titel['Purge Objects'] = 'Purge Objects';
$page_titel['Preferences_User'] = $LNG_HEAD[8];
$page_titel['Setcompare'] = $LNG_SETCOMPARE[0];
$page_titel['Textimport'] = $LNG_LOAD[0];
$page_titel['ObjectCreator'] = $LNG_HEAD[16];
$page_titel['File_upload'] = $LNG_MANAGE[21];

// ----- Set the values of the simple tokens
// Titel Browser
if (isset($page_titel[$title])) $v_att['page_name'] = 'SimuTranslator - '.$page_titel[$title];
else                            $v_att['page_name'] = 'SimuTranslator - '.$title;
// Titel Page
$v_att['page'] = $page;

  // DSGVO file
    $v_att['Bez_Dsgvo'] = $LNG_HEAD[15];
    $v_att['Link_Dsgvo'] = "main.php?page=dsgvo";

    // search languages
    $langar = get_langs();
  // create items for language selctions list
    for ( $x = 0; $x < count($langar); $x++ ) {
      if ( $langar[$x] == $st ) { $sel = 'selected'; } else { $sel = ''; }

      // ----- Set the values of the list tokens
      $v_att['languages'][$x]['lang_id'] = $langar[$x];
      $v_att['languages'][$x]['select'] = $sel;
      $v_att['languages'][$x]['lang_name'] = $LANG_NAMEN[$langar[$x]];
    
    // create button list for no js
      if ( count($langar) > 1 ) {
            $v_att['languages2'][$x]['lang_id'] = $langar[$x];
            $v_att['languages2'][$x]['language_titel'] = $sel;
          if ( file_exists('./lang_img/'.$langar[$x].'.gif') ) {
              $v_att['languages2'][$x]['lang_name'] = '<IMG BORDER=0 ALT="'.$LANG_NAMEN[$langar[$x]].'" SRC="lang_img/'.$langar[$x].'.gif">';
            } else {
              $v_att['languages2'][$x]['lang_name'] = '[ '.$langar[$x].' ]';
            }
        if ( $langar[$x] == $st ) { $act = 'class="bg_activ"'; } else { $act = ''; }
            $v_att['languages2'][$x]['select'] = $act;


        //if ( $x == 15 ) { echo '</ul><ul id="sprachlinks">'; }
       }
    }

// date from language files
$v_att['language_date'] = date($FORMAT_DATUM, filemtime('./lang/en/lng_main.php'));

  $x = 0;
    //go to main menu - avialable alwyas
    $v_att['linklist'][$x]['link_selector'] = '';
    $v_att['linklist'][$x]['link_file'] = 'main.php?lang='.$st;
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[0];
    
  // Galerie
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'gallery.php?lang='.$st;
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[19];

  // Pakset_Info
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'pakset_info.php?lang='.$st;
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[18];
    $x++;

  // search objekts
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'directions.php?lang='.$st;
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[5];

  //download text files - available alwyas
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=wrap';
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[3];

  //upload texts - should be available to any user of level tr2
    //available to any tr2 and above
    $u_level = array('tr2','admin','gu','painter','pakadmin');
    if (compare_userlevels($u_level, $user_type)) {
      $x++;
      $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
      $v_att['linklist'][$x]['link_file'] = 'load.php?lang='.$st;
      $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[4];
    }
  
  // set compare
    $u_level = array('tr1', 'tr2','admin','gu','painter','pakadmin');
    if (compare_userlevels($u_level, $user_type)) {
      $x++;
      $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
      $v_att['linklist'][$x]['link_file'] = 'setcompare.php?lang='.$st;
      $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[13];
    }

    //available to painters and admins only 
    $u_level = array('pakadmin', 'painter', 'admin');
    if (compare_userlevels($u_level, $user_type)) {
      $x++;
      $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
      if ( $user_type == 'painter' ) {
        $v_att['linklist'][$x]['link_file'] = 'obj_browser.php?lang='.$st;
        $v_att['linklist'][$x]['link_name'] = $LNG_MANAGE[3];
      } else {
        $v_att['linklist'][$x]['link_file'] = 'obj_index.php?lang='.$st;
        $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[6];
      }  
  }

  // statistics
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=stats_menu';
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[7];

  // preferences for registered users
    if ($user_type != 'guest') {
      $x++;
      $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
      $v_att['linklist'][$x]['link_file'] = 'preferences.php?lang='.$st;
      $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[8];
    }

  // administrations page for admins
    if ($user_type == 'admin') {
      $x++;
      $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
      $v_att['linklist'][$x]['link_file'] = 'admin.php?lang='.$st;
      $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[9];
    }

  // login / logout
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    if ($user_type != 'guest') {
      // logout
      $v_att['linklist'][$x]['link_file'] = 'index.php?lang='.$st.'&logout=1';
      $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[10].'&nbsp;'.$_SESSION['userId'];
    } else {
    	// login
    	$v_att['linklist'][$x]['link_file'] = 'index.php?lang='.$st;
    	$v_att['linklist'][$x]['link_name'] = $LNG_HEAD[11];
    }

 	// contact page
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=contact';
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[12];
            
 // rss feed info page
    $x++;
    $v_att['linklist'][$x]['link_selector'] = '&nbsp;- ';
    $v_att['linklist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=rssinfo';
    $v_att['linklist'][$x]['link_name'] = $LNG_HEAD[17];


// ----- Generate result in a string
    $v_result = $v_template->generate($v_att, 'string');

// ----- Display result
    echo $v_result;
?>








