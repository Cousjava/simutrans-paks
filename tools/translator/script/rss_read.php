<?php  
require_once('./include/dblib.php');
include ('./include/obj.php');
include ("include/translations.php");

error_reporting(0);

function get_langs() {
     $verzeichnis = scandir ('./lang/', SCANDIR_SORT_ASCENDING);
     
     $not_Show = array('.', '..', '.htaccess','index.php');
     $langar = array();
     foreach ($verzeichnis as $dirorfile) 
     { if ( !in_array($dirorfile, $not_Show) ) 
       { $langar[] = $dirorfile;
       }
     }
     return $langar;
}

function format_text($text,$lng_nr)
{ GLOBAL $LNG_EDIT;
  return  "<p style='background-color: #88C4FF;'><strong>".$LNG_EDIT[$lng_nr].
          "</strong><br />".htmlentities($text, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")."</p>";
}

$langar = get_langs();

if     ( isset($_POST['lang']) )                   $st = $_POST['lang'];
elseif ( isset($_GET['lang']) )                    $st = $_GET['lang'];
elseif ( isset($_SESSION['user_lang']) )           $st = $_SESSION['user_lang'];
elseif ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) $st = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

// fallback language not set   
if ( !isset($st) or strlen($st) < 2 or strlen($st) > 3 or !in_array($st,$langar) )  $st = 'en'; 
// fallback language not exist translate Translator   
if ( !file_exists('./lang/'.$st.'/lng_main.php') )  $st = 'en'; 
$_SESSION['user_lang'] = $st;

include ('./lang/'.$st.'/lng_main.php');

include('./include/rss/feedcreator.class.php'); 

$serverlink = 'https://'.getenv('SERVER_NAME').'/'; 
$scriptpath = explode('/',getenv('REQUEST_URI'));
if ($scriptpath[2] == 'script') $serverlink .= $scriptpath[1].'/';

$rss = new UniversalFeedCreator(); 
$rss->useCached(); 
$rss->language = $st;
$rss->title = $LNG_RSS_FEED[0]; 
$rss->description = $LNG_RSS_FEED[1]; 
$rss->link = $serverlink.'script/'; 
$rss->syndicationURL = $serverlink.'data/'; //$_SERVER["PHP_SELF"]; 

$rss->cssStyleSheet = $serverlink.'script/css/style_rssfeed.css';
//$rss->xslStyleSheet = 'http://feedster.com/rss20.xsl';
//$rss->xslStyleSheet = $serverlink.'script/include/rss/rss_lang.xsl';

$image = new FeedImage(); 
$image->title = $LNG_RSS_FEED[0]; 
$image->url = $serverlink.'script/img/logo_sm.png'; 
$image->link = $serverlink.'script/'; 
$image->description = $LNG_RSS_FEED[2]; 
$rss->image = $image; 
  
  $data = file('../data/rss/rss_items');
  $data_day = array();
  $data_sets = array();
  foreach ($data as $z)
  { $r = explode('|', $z);
    $d = substr($r[0], 0, 10);
    $data_day[$d][$r[5]][$r[1].$r[2].'  '.$r[3].'  '.$r[4]] = $r;
  }

  // $data_day[Datum][Set][Eintrag][Eintrag-Daten]

  // Beginn Schleife Datum
  ksort($data_day);
  foreach ( $data_day as $day => $v_tab) 
  { $day_as_time = strtotime($day);
    // Beginn Schleife Sets
    ksort($v_tab);
    foreach ($v_tab as $set => $l_tab)
    { $set_name = $versions_all[$set];
      $set_link = $serverlink.'script/directions.php?vers='.$set.'&rss_feed=1#'.$day;
      $objects_translate = '';   //  translate     
      $objects_suggestion = '';  //  suggestion     
      $text_import = '';         //  import text
      $objects_sug_accept = '';  //  suggestion accept
      $objects_import = '';      //  import object
      $objects_update = '';      //  import object
      $objects_delete = '';      //  delete object
      // Beginn Schleife Sets Eintraege
      ksort($l_tab);
      foreach ( $l_tab as $log_entry)
      { //var_dump($log_entry);
        $log_time = $log_entry[0];
        $obj_typ  = $log_entry[1];
        $obj_name = $log_entry[2];
        $language = $log_entry[3];
        $log_typ  = $log_entry[4];
        $version  = $log_entry[5];
        $obj_id   = trim($log_entry[6]);
        
        $lt = explode(' ',$log_typ,2);
        $field_typ = array_search($lt[0],$log_field_typ);
        if ($field_typ !== false)
        { $log_typ = $lt[1];
          $typ_text = '<b> '.$lt[0].'</b>';
          $tr = tr_read($obj_id,$version,$language,$field_typ);
        } else $tr = array('nix_text','nix sugest');
        
        $obj_link = '<span style="color:green"><b>'.$obj_typ.'</span>'
                   .' - <a href="'.$serverlink.'script/edit.php?obj_id='.$obj_id.'#'.$language.'" target="_blank">'
                   .htmlentities($obj_name, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8").'</a></b> ';
        
        if ($language > '  ') $lng_text = ' '.$LNG_RSS_FEED[3].' <b>'.$language.' - '.$LNG_LANGUAGE[$language].'</b>';
        else $lng_text = 'no language';
        
        $textentry = $typ_text.' '.$log_typ.' <b>'.$language.'</b> '.htmlentities($tr[0], ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")."<br />\n";
        
        if ( $log_typ == 'translate' ) {
          $objects_translate .= $obj_link.$typ_text.$lng_text.'<br />'
                             .format_text($tr[0],11)."<br />\n";
        } 
        if ( $log_typ == 'suggestion accept' ) {
          $objects_sug_accept .= $obj_link.$typ_text.$lng_text.'<br />'
                              .format_text($tr[0],11)."<br />\n";
        } 

        if ( $log_typ == 'suggestion' ) {
          $objects_suggestion .= $obj_link.$typ_text.$lng_text.'<br />'
                              .format_text($tr[0],11)
                              .format_text($tr[1],14)."<br />\n";
        } 

        if ( $log_typ == 'import' or $log_typ == 'move' ) {
          if     ($obj_imp_alt == $obj_name) $objects_import .= $textentry;
          elseif ($obj_upd_alt == $obj_name) $objects_update .= $textentry;
          else  $text_import .= $obj_link.$textentry;
        }

        if ( $log_typ == 'object import' ) {
          $objects_import .= $obj_link."<br />\n";
          $obj_imp_alt = $obj_name;
        }

        if ( $log_typ == 'object update' ) {
          $objects_update .= $obj_link."<br />\n";
          $obj_upd_alt = $obj_name;
        }

        if ( $log_typ == 'object delete' ) {
          $objects_delete .= $obj_link."<br />\n";
          $obj_upd_alt = $obj_name;
        }

      } // Schleife Eintraege Ende
           
      $test = 'b';
      if ( $objects_translate != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[10]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[11].' <br />'.$objects_translate; 
        $item->date = $day_as_time;
        $item->guid = $set_link.'tr'.$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $objects_suggestion != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[4]; 
        $item->link = $serverlink.'script/has_suggestion.php'; 
        $item->description = $LNG_RSS_FEED[5].' <br />'.$objects_suggestion; 
        $item->date = $day_as_time;
        $item->guid = $set_link.'su'.$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $text_import != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[14]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[16].' <br />'.$text_import; 
        $item->date = $day_as_time; 
        $item->guid = $set_link."ti".$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $objects_sug_accept != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[8]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[9].' <br />'.$objects_sug_accept; 
        $item->date = $day_as_time; 
        $item->guid = $set_link.'sa'.$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $objects_import != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[6]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[7].' <br />'.$objects_import; 
        $item->date = $day_as_time; 
        $item->guid = $set_link."oi".$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $objects_update != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[15]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[15].' <br />'.$objects_update; 
        $item->date = $day_as_time; 
        $item->guid = $set_link."ou".$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

      if ( $objects_delete != '' ) {
        $item = new FeedItem(); 
        $item->title = $LNG_EDIT[1] .' "'.$set_name.'" '. $LNG_RSS_FEED[17]; 
        $item->link = $serverlink.'script/'; 
        $item->description = $LNG_RSS_FEED[17].' <br />'.$objects_delete; 
        $item->date = $day_as_time; 
        $item->guid = $set_link."od".$test;
        $item->category = $set_name;
        $rss->addItem($item);
      }

    } // Schleife Sets Ende
  } // Schleife Datum

  $rss->saveFeed('RSS2.0', '../data/rss/'.$st.'_translator_rssfeed.xml'); 
  
?>
