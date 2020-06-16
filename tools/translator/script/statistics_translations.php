<?php
    //will be prefixed by "SimuTranslator - " for window title

  //list of all user typs allowed on this page
  //header will block access for all other users
  //publicly accessible

  //header, no output before this (sends header information)
  $title = 'stats_translation';
  include ('tpl_script/header.php');
  include ('./include/translations.php');
  include ('./include/select.php');

    //prints page title
    //called separately in case you have some special requirements

////////////////////////////////////////////////////////////////////////////////
//globals with constant data - cannot be static in functions - php cannot parse that
//$sets        = db_fetch_result_as_table ("SELECT DISTINCT `version_id` as 'vid', count(*) as 'obj_count' FROM `versions` JOIN `objects` ON (`version_id` = `version_version_id`) GROUP BY `version_version_id`;");
$lang = select_box_read_language();

if ( isset($_POST['list']) ) {
      $set1 = intval($_POST['set1']);
      $set2 = intval($_POST['set2']);
      $set3 = intval($_POST['set3']);
      $set4 = intval($_POST['set4']);
        // set choice
        if ( $set1 != 255 ) { 
          $auswahl = "`version_id`=".$set1; } else { $auswahl = ''; }
        if ( $auswahl == '' && $set2 != 255 ) { 
          $auswahl = "`version_id`=".$set2; } elseif ( $set2 != 255 ) { $auswahl .= " OR `version_id`=".$set2; }
        if ( $auswahl == '' && $set3 != 255 ) { 
          $auswahl = "`version_id`=".$set3; } elseif ( $set3 != 255 ) { $auswahl .= " OR `version_id`=".$set3; }
        if ( $auswahl == '' && $set4 != 255 ) { 
          $auswahl = "`version_id`=".$set4; } elseif ( $set4 != 255 ) { $auswahl .= " OR `version_id`=".$set4; }
        // language choice
        
        if ( $auswahl != '' ) {
          $not_select = 0;
                //echo  "SELECT DISTINCT `version_id` as 'vid', count(*) as 'obj_count' FROM `versions` JOIN `objects` ON (`version_id` = `version_version_id`) WHERE $auswahl GROUP BY `version_version_id`;";
    
          $sets = db_fetch_result_as_table ("SELECT DISTINCT `version_id` as 'vid', count(*) as 'obj_count' FROM `versions` JOIN `objects` ON (`version_id` = `version_version_id`) WHERE $auswahl GROUP BY `version_version_id`;");
        } else {
          $not_select = 1;
        }
  
        if ( $set1 != 255 ) { 
          $auswahl = "`version_version_id`=".$set1; } else { $auswahl = ''; }
        if ( $auswahl == '' && $set2 != 255 ) { 
          $auswahl = "`version_version_id`=".$set2; } elseif ( $set2 != 255 ) { $auswahl .= " OR `version_version_id`=".$set2; }
        if ( $auswahl == '' && $set3 != 255 ) { 
          $auswahl = "`version_version_id`=".$set3; } elseif ( $set3 != 255 ) { $auswahl .= " OR `version_version_id`=".$set3; }
        if ( $auswahl == '' && $set4 != 255 ) { 
          $auswahl = "`version_version_id`=".$set4; } elseif ( $set4 != 255 ) { $auswahl .= " OR `version_version_id`=".$set4; }

  $total_count = db_one_field_query ("SELECT count(*) FROM `objects` WHERE $auswahl");
}




function statistics_for_one_set ($language_id, $set_id)
{
    $tab = 'translations_'.$set_id;
    $translated_counta = array(0, 0);
    // count all translated objects
    $translated_counta[0] = db_one_field_query ("SELECT count(*) FROM `".$tab."` WHERE `language_language_id`='$language_id' AND `tr_text`<>'';");
    // count translated unnecessary_text/dummy_info objects
    $translated_counta[1] = db_one_field_query ("SELECT count(*) FROM `objects` o JOIN `".$tab."` p ON (o.obj='unnecessary_text' AND o.object_id=p.object_object_id) WHERE o.version_version_id=".$set_id." AND o.obj='unnecessary_text' AND p.language_language_id='$language_id' AND p.tr_text<>'';" );

    return $translated_counta;
}


function statistic_for_one_language ($language_id, $lng_name, $r)
{
    //information about sets - done only once!
    global $sets, $total_count,  $LNG_STATS_TRANS, $v_att,$versions_all;

    //counts number of printed lines - for graphical purposes
    //abs total - all translated texts over all languages
    static $line_number = 0, $abs_total = 0;

    //determine style of row (grey every 2nd line)
    $style = (($line_number % 2) == 0)?"classic":"classic_grey";

    //increase line number
    $line_number++;

    //language name
    $v_att['tablerow_language'][$r]['language'] = $lng_name;
    $v_att['tablerow_language'][$r]['style'] = $style;


    $total_translated = array(0, 0);

      $s = 0;
    //stats for each set
    foreach ($sets as $row)
    {
        $vid   = $row['vid'];
        $count = $row['obj_count'];

        $unnecessary_count = db_one_field_query ("SELECT count(*) FROM `objects` WHERE `version_version_id`='$vid' AND `obj`='unnecessary_text' ");
 
        $translated_count = statistics_for_one_set ($language_id, $vid);
        
        $total_translated[0] += $translated_count[0];
        $total_translated[1] += $translated_count[1];
        //$total_translated[2] += $translated_count[2];
        
       //&& $dummyinfo_count == 0
        if ( $unnecessary_count == 0 ) {
          $t = ($translated_count[0]/$count)*100;
          
          if ( $t >= 100 ) { $g = 'stats100'; }
          elseif ( $t > 95 ) { $g = 'stats95'; } 
          elseif ( $t < 50 ) { $g = 'stats-50'; }
          else { $g = ''; }
          
          $string = '<font class="'.$g.'">';

          //check if we are not > 100% (due to orphan texts) or if translation is complete
          $complete = ($translated_count[0] >= $count);

          $string .= (($complete)?"":"") . sprintf("%01.1f %% (%d)" . (($complete)?"":""), (($complete)?100:$t), ($complete?0:($count - $translated_count[0])));
          $string .= '</font>';
        } else {
          
          $r1 = $unnecessary_count;// + $dummyinfo_count); + $translated_count[2])
          $r2 = $translated_count[1];
          
          $t = (($translated_count[0]-$r2)/($count-$r1))*100;

          if ( $t >= 100 ) { $g = 'stats100'; }
          elseif ( $t > 95 ) { $g = 'stats95'; } 
          elseif ( $t < 50 ) { $g = 'stats-50'; }
          else { $g = ''; }
        
          $string = '<font class="'.$g.'">';

          //check if we are not > 100% (due to orphan texts) or if translation is complete
          $complete = (($translated_count[0]-$r2) >= ($count-$r1));
          
          $c = (($count-$r1) - ($translated_count[0]-$r2));
          
          $string .= (($complete)?"":"") . sprintf("%01.1f %% (%d)" . (($complete)?"":""), (($complete)?100:$t), ($complete?0:$c));
          // stats objects unnecessary_text/dummy_info
          $t = ($r2/$r1)*100;
          
          
          if ( $t >= 100 ) { $g = 'stats100'; }
          elseif ( $t > 95 ) { $g = 'stats95'; } 
          elseif ( $t < 50 ) { $g = 'stats-50'; }
          else { $g = ''; }
          $string .= '</font><br /><nobr><font class="'.$g.'">';
          $complete = $r2 >= $r1;
          $c = ($r1 - $r2);
          $string .= (($complete)?"":"") . sprintf("%01.1f %% (%d)" . (($complete)?'':''), (($complete)?100:$t), ($complete?0:$c));
          $string .= '</font> * </nobr>';
          
        }
        
        $v_att['table_head_sets'][$s]['setname'] = $versions_all[$vid];
        $v_att['table_head_sets'][$s]['gui_objets'] = $LNG_STATS_TRANS[5];
        $v_att['table_head_sets'][$s]['obj_count'] = $count;
        
        $v_att['table_head2_sets'][$s]['gui_table_head2'] = $LNG_STATS_TRANS[8];
        $v_att['tablerow_language'][$r]['tablerow_sets'][$s]['fielditem'] = $string;
        $v_att['tablerow_language'][$r]['tablerow_sets'][$s]['style'] = $style;
        $s++;

    }

    //totals
        $t = ($total_translated[0]/$total_count)*100;
        if ( $t > 95 ) { $g = "stats95"; } 
        elseif ( $t >= 100 ) { $g = "stats100"; }
        elseif ( $t < 50 ) { $g = "stats-50"; }
        else { $g = ""; }

    $string = '<font class="'.$g.'">';
    $string .= sprintf("%01.1f %% (%d)", $t, ($total_count - $total_translated[0]));
    $string .= '</font>';

    $v_att['tablerow_language'][$r]['total'] = $string;

    //number of translators
    $t = db_fetch_row(db_query("SELECT count(*) FROM `translate` t JOIN `users` u ON (t.translator_user_id=u.u_user_id) WHERE u.state='active' AND t.lng_tr_language_id='$language_id';"));

    $v_att['tablerow_language'][$r]['translators'] = $t[0];


    $abs_total += $total_translated[0];
    return $abs_total;
}

////////////////////////////////////////////////////////////////////////////////


include('./tpl_script/stats_translations.php');


    //footer, nothing ater this (closes page)
  include_once ("./tpl_script/footer.php");
?>
