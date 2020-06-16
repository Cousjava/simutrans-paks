<?php


/*
noch einbauen

waren eingangslager kalkulieren

zugkraft wenn ausgewählt werden 2 Felder geöffnet für gewicht wagen und geschwindigkeit
anzeige anzahl wagen möglich
als link zugkraft gewicht und geschindig übergeben bei wagen '?' anzeigen, name in link, un dann in kopf stellen

Mouseover für eingabefelder mit erläuterungen

wenn makie kost dann file schreiben und link anbieten

knopf export als csv
*/


  //list of all user typs allowed on this page
  //header, no output before this (sends header information)
  $title = 'Pak_Info';
  include ('tpl_script/header.php');
  include ('./include/obj.php');
  include ('./include/images.php');
  include ('./include/translations.php');
  include ('./include/select.php');

 
  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/pakset_info.htm');
  // ----- Prepare data
  $v_att = array();

  $c_list = array();
  $c_list['bridge']      = array ('name','image','links','waytype','topspeed','intro','retire','maintenance','max_lenght','pillar_asymmetric','pillar_distance','cost','other','comments' );
  $c_list['vehicle']     = array ('name','image','links','waytype','category','payload', 'speed','power', 'intro','retire','weight','weight_full','weight%','gear','zugkraft','cost', 'runningcost', 'fixed_cost', 'rcost%e');
  $c_list['citycar']     = array ('name','image','links','intro','retire','distributionweight', 'speed', 'copyright','note','other','comments');
  $c_list['pedestrian']  = array ('name','image','links','intro','retire','distributionweight', 'speed', 'copyright','note','other','comments');
  $c_list['factory']     = array ('name','image','links','intro','retire','location','climates','pax_level', 'productivity','range', 'input', 'output',  'demand','boost', 'expand', 'mapcolor', 'smoke', 'dims',  'other' );
  $c_list['field']       = array ('name','image','intro','retire','copyright','note','other','comments' );
  $c_list['building']    = array ('name','image','links','type','waytype','climates','location','level', 'intro','retire', 'dims','chance','passengers','enables','copyright','needs_ground','clusters','dat_file_name','other','comments');
  $c_list['roadsign']    = array ('name','image','links','waytype','intro','retire','cost','copyright','note','other','comments' );
  $c_list['way']         = array ('name','image','links','waytype','system_type','topspeed','intro','retire','cost','maintenance', 'max_weight','copyright','note','other','comments' );
  $c_list['tunnel']      = array ('name','image','links','waytype','topspeed','intro','retire', 'maintenance','max_lenght','cost','other','comments' );
  $c_list['good']        = array ('name','catg','links','metric','weight_per_unit','value','speed_bonus','mapcolor','note','other','comments' );
  $c_list['menu']        = array ('name','image','type','copyright','note','other','comments' );
  $c_list['rest']        = array ('name','image','links','type','climates','level','intro','retire', 'dims','copyright','note','other','comments' );
  
  
$makie_way_multi =array();
$makie_way_multi['way0'] = 1;
$makie_way_multi['way1'] = 10;
$makie_way_multi['bridge0'] = 5;
$makie_way_multi['tunnel0'] = 15;

// bits_per_month Einstellungen in den paks
$bits_p_faktor = array();
$bits_p_faktor['1'] = 4; // pak128.german bits_per_month = 20
$bits_p_faktor['19'] = 2; // pak128.german bits_per_month = 19
$bits_p_faktor['20'] = 4; // pak128.german bits_per_month = 20
  
////////////////////////////////////////////////////////////////////////////////
function read_clusters($vid)
{ $clu_list = array();
  $clu_list[0] = 'none';
  for ( $i = 1; $i < 32; $i++)
  { $o_n = sprintf('Cluster_%02d',$i);
    $t = tr_translate_text($vid,$o_n);
    if ($o_n != $t) $clu_list[$i] = $t;
  }
  return $clu_list;
}


////////////////////////////////////////////////////////////////////////////////
function read_p_list($obj_auswahl,$col_auswahl)
{ global $st;
  $p_list = array();
  if ($obj_auswahl != 'menu_text') $p_list = read_p_list('menu_text',$col_auswahl);
  if ($obj_auswahl == 'translator') return $p_list;
  $tr_t = 'translations_200';
  $q = db_query ("SELECT obj_name, obj, tr_text FROM objects o JOIN $tr_t t ON (o.object_id=t.object_object_id) 
                  WHERE o.version_version_id=200 AND
                        o.obj='$obj_auswahl' AND
                        t.language_language_id='$st'");
  while ($p_e = db_fetch_object($q))
  { $p = $p_e->obj_name;
    $t = $p_e->tr_text;
    if ($t == '') $t = $p.':';
    $pp = strpos($t,'.');
    if (strlen($t) > 30 and $pp > 2) $t = substr($t,0,$pp+1);
    if (($col_auswahl == '0' and ($p == 'name' or  $p == 'obj_name')) or
        ($col_auswahl != '0' and ($p != 'name' and $p != 'obj_name'))) $p_list[$p] = $t;
  }
  asort ($p_list,SORT_STRING | SORT_FLAG_CASE);
  return $p_list;
}

////////////////////////////////////////////////////////////////////////////////
function read_goods ($vid)
{  global $st, $good_tab, $cat_tab;
   $good_tab = array();
   $q1 = db_query ("SELECT obj_name, object_id FROM objects WHERE obj='good' AND version_version_id=$vid");
    while ($good_o = db_fetch_object($q1))
    { $g_entry = array();
      $g_entry['name'] = $good_o->obj_name;
      $q2 = db_query ("SELECT p_name, p_value FROM property WHERE having_obj_id=$good_o->object_id");
      while ($good_p = db_fetch_object($q2))
      { if ($good_p->p_name == 'catg')
        { if ($good_p-> p_value > 0) $g_entry['name'] = sprintf('CATEGORY_%02d',$good_p-> p_value);
        }
        if ($good_p->p_name == 'value[0]') $g_entry['value'] = intval($good_p-> p_value) / 3;
        if ($good_p->p_name == 'value')    $g_entry['value'] = intval($good_p-> p_value) / 3;
        if ($good_p->p_name == 'weight_per_unit') $g_entry['weight'] = intval($good_p-> p_value);
      }
      db_free_result($q2);
      $good_tab[$good_o->obj_name] = $g_entry;
    }
    db_free_result($q1);
   $cat_tab = array();
   foreach ($good_tab as $gentry)
   { $n = $gentry['name'];
     if (isset($cat_tab[$n]))
     { $cat_tab[$n]['value']  = min($cat_tab[$n]['value'], $gentry['value']);
       $cat_tab[$n]['weight'] = max($cat_tab[$n]['weight'],$gentry['weight']);
     }
     else $cat_tab[$n] = $gentry;
   }
}

////////////////////////////////////////////////////////////////////////////////
function print_table_header ($property_list)
{
    GLOBAL $v_att, $LNG_STATS_VEH,$kmh,$csv_t;

    $x = 0;
    foreach ($property_list as $property)
    {
        //special care for long fields
        $style = '';
        $titel = $LNG_STATS_VEH[6]." ".$property;
        $p = tr_translate_text(200,$property);
//       echo $p."text<br>";
        $pe = explode(':',$p);
        $p = $pe[0];
        if (isset($pe[1]) and strlen($pe[1]) > 3) $titel = $pe[1];
        if ($property == 'comments' OR $property == 'other') $style = 'width="300"';
        if ($property == 'zugkraft' and $kmh > 0)
        { $titel =  sprintf($LNG_STATS_VEH[7],$kmh);
          $p =  $p.'=>'.$kmh.'km/h';
        } 

        $v_att['tablehead'][$x]['stylewidth'] =  $style;
        $v_att['tablehead'][$x]['th_title']   =  $titel;
        $v_att['tablehead'][$x]['property']   =  $p;
        $v_att['tablehead'][$x]['id'] =  $x;
        $v_att['tableheadids'][$x]['id'] =  $x;
        $x++;
        if ($csv_t != ''and substr($property,0,5) != 'image') $csv_t .= str_replace('&nbsp;',' ',$p).';';
    }
    if ($csv_t != '') $csv_t .= "\n";
}

function format_b ($betrag)
{ $k = sprintf('%010d',$betrag);
  return (substr($k,0,2).'&nbsp;'.substr($k,2,3).'&nbsp;'.substr($k,5,3).'.'.substr($k,8,2));
} 

function format_l ($links)
{ $t = '';
  $l = explode('\n',$links);
  foreach ($l as $link) if ($link != '')
  { $llow = strtolower($link);
    $icon = "img/icon_link.png";
    if (strpos($llow,".jpg") > 1 or 
        strpos($llow,".png") > 1) $icon = "img/Icon_pictures.png";
    if (strpos($llow,".pdf") > 1) $icon = "img/icon_pdf.png";
    if (strpos($llow,".wikipedia") > 1) $icon = "img/icon_wikipedia.png";     
    if ($t != '') $t .= '&nbsp;';
    $t .= "<a href='".$link."' target='_blank'><img src='".$icon."' height='24' width='24'>";
  }
  return ($t);
} 


function factoryio($p,$io_t,$jv,$jb,$f)
{ global $st,$version_auswahl,$obj_auswahl,$trv,$trb;
  $wert = ''; $b ='';
  foreach ($io_t as $i)
  { $wn = 'nogood'; $wf = 100; $wr = ''; $wrz = '';
    foreach($i as $k => $e)
    { if     ($k == 'capacity') $wc = $e;
      if     ($k == 'good')     $wn = $e;
      elseif ($k == 'factor')   $wf = $e;
      else { $wr .= $wrz.$k.'='.$e; $wrz = ', '; }
    }
    $px='o';
    if (substr($p,0,1) == 'o') $px ='i';
    $wert .= $b."<a href='pakset_info.php?lang=$st&vers=$version_auswahl&obj_auw=$obj_auswahl".
                "&trange=".max($jv,$trv)."-".min($jb,$trb).
                "&good=".$px.'_'.$wn.
                "' target='_blank'>".tr_translate_text($version_auswahl,$wn);
    if ($wf != 100)     $wert .= '&nbsp;%'.$wf;
    if (strlen($p) >10) $wert .= '&nbsp;('.$wr.') '.sprintf('%03.1f',$wc / $f * (100/$wf));
    $wert .= '</a>';
    $b = '<br>';
  }
  return $wert;  
}

function c_disp($collect)
{ global $is_displayed; 
  $wert = ''; $b ='';
  $tz = explode('<br>',$collect);
  foreach ($tz as $z)  if ($z != '') 
  { $p = explode('=',$z);
    $p0 = $p[0];
    $is_displayed[] = $p0;
    $p0 = str_replace('enables_','',$p0);
    $p0 = str_replace('expand_','',$p0);
    $p0 = str_replace('_boost' ,'',$p0);
    $p0 = str_replace('_demand','',$p0);
    $p0 = str_replace('_amount','',$p0);
    $wert .= $b.$p0.'='.$p[1];
    $b = '<br>';
  }
  return $wert;  
}

function print_table_line ($property_list,$ob_id)
{
    GLOBAL $v_att,$st,$x, $obj_tab,$good_tab, $cat_tab,$wfull,$we,$wg,$sub_waytypes,$makie_way_multi,$bits_p_faktor,
           $version_auswahl,$obj_auswahl,$obj_sub_auswahl,$good_auswahl,$engine_auswahl,$clu_list,
           $in_out,$climate_auswahl,$cluster_auswahl,$is_displayed,$kmh,$trv,$trb,$csv_t,$makie_p;

    //fetch some object data stored directly with the object
    $object_properties = db_query2array ("SELECT obj_name, obj_copyright, obj, note, comments, dat_file_name, dat_path  FROM objects WHERE object_id=$ob_id");
    $obj_name = $object_properties['obj_name'];
    
    $object_translate   = tr_read($ob_id,$version_auswahl,$st,'t');
    $obj_name_translate = $object_translate[0];
    $object_translate   = tr_read($ob_id,$version_auswahl,$st,'d');
    $obj_details        = $object_translate[0];
    $object_translate   = tr_read($ob_id,$version_auswahl,$st,'l');
    $obj_links          = $object_translate[0];
    if ($obj_links == '')
    { $object_translate   = tr_read($ob_id,$version_auswahl,'en','l');
      $obj_links          = $object_translate[0];
      if ($obj_links == '')
      { $object_translate   = tr_read($ob_id,$version_auswahl,'de','l');
        $obj_links          = $object_translate[0];
      }
    }
    $object_translate   = tr_read($ob_id,$version_auswahl,$st,'h');
    $obj_history        = $object_translate[0];

    $pp = strpos($obj_name_translate,'\n');
//  if ($obj_auswahl == 'building' and $pp > 0) 
    if (                               $pp > 0) 
    { $pph = $pp +2;
      if (substr($obj_name_translate,$pph,2) == '\n') $pph +=2;
      $obj_details = $obj_details.substr($obj_name_translate,$pph);
      $obj_name_translate = substr($obj_name_translate,0,$pp);
    }
     //now fetch all other properties for this vehicle
    //db_fetch_result_as_table ("");
    $property_q = db_query ("SELECT p_name, p_value FROM property WHERE having_obj_id=$ob_id");
    $raw_properties = array();

    $is_displayed = array();
    if ($obj_auswahl == 'vehicle' and $good_auswahl    != 255) $is_displayed[] = 'freight';
    if ($obj_auswahl == 'vehicle' and $obj_sub_auswahl != 255) $is_displayed[] = 'waytype';
    //copy all properties to an array keyed by property name
    $intro_month=1; $intro_year=1900; $retire_month=12; $retire_year=2999; $fracht=''; $climates=''; $clusters = array();
    $enables=''; $expand='';  $demand =''; $dims='1,1'; $fracht_gewicht=0; $gear=100; 
    $maintenance = 0; $fcost = 0; $topspeed = 0; $system_type = 0;
    $prod = 10; $range = 10; $boost = ''; $boost_e='1000'; $boost_p=0; $boost_m=0; $engine_type=''; $tender1='';
    $waytype=''; $payload=0; $power=0; $speed=0; $weight=0; $cost=0; $rcost=0; $category='None'; $ertrag=0;
    $in_t = array(); $out_t = array(); 
    while ($row = db_fetch_row($property_q))
    {   $raw_properties[$row[0]] = $row[1];
        if ($row[0] == 'intro_month'  and $row[1] >= 1    and  $row[1] <=   12) $intro_month  = $row[1];
        if ($row[0] == 'intro_year'   and $row[1] >= 1500 and  $row[1] <= 2999) $intro_year   = $row[1];
        if ($row[0] == 'retire_month' and $row[1] >= 1    and  $row[1] <=   12) $retire_month = $row[1];
        if ($row[0] == 'retire_year'  and $row[1] >= 1500 and  $row[1] <= 2999) $retire_year  = $row[1];
        if ($row[0] == 'waytype')           $waytype      = strtolower($row[1]);
        if ($row[0] == 'engine_type')       $engine_type  = strtolower($row[1]);
        if ($row[0] == 'constraint[next][0]') $tender1    = $row[1];
        if ($row[0] == 'climates')          $climates     = $row[1];
        if ($row[0] == 'clusters')          $clusters     = explode(',',$row[1]);
        if ($row[0] == 'weight')            $weight       = $row[1];
        if ($row[0] == 'dims')              $dims         = $row[1];
        if ($row[0] == 'power')             $power        = intval($row[1]);
        if ($row[0] == 'speed')             $speed        = intval($row[1]);
        if ($row[0] == 'gear')              $gear         = intval($row[1]);
        if ($row[0] == 'cost')              $cost         = intval($row[1]);
        if ($row[0] == 'runningcost')       $rcost        = intval($row[1]);
        if ($row[0] == 'fixed_cost')        $fcost        = intval($row[1]);
        if ($row[0] == 'productivity')      $prod         = intval($row[1]);
        if ($row[0] == 'range')             $range        = intval($row[1]);
        if ($row[0] == 'electricity_boost') $boost_e      = intval($row[1]);
        if ($row[0] == 'passenger_boost')   $boost_p      = intval($row[1]);
        if ($row[0] == 'mail_boost')        $boost_m      = intval($row[1]);
        if ($row[0] == 'maintenance')       $maintenance  = intval($row[1]);
        if ($row[0] == 'topspeed')          $topspeed     = intval($row[1]);
        if ($row[0] == 'system_type')       $system_type  = intval($row[1]);
        if ($row[0] == 'freight') 
        { $fracht = $row[1];
          if (strtolower($fracht) == 'none') $fracht = '';
          $category = 'None';
          $ertrag = 0;
          $fracht_gewicht = 0;
          if (isset($good_tab[$fracht]))
          { $category = $good_tab[$fracht]['name'];
            $ertrag         = $cat_tab[$category]['value'];
            $fracht_gewicht = $cat_tab[$category]['weight'];
          }; 

        }
        if (substr($row[0],0,7) == 'payload')  $payload += intval($row[1]);
        if (substr($row[0],0,8) == 'enables_') $enables .= $row[0].'='.$row[1].'<br>';
        if (substr($row[0],0,7) == 'expand_')  $expand  .= $row[0].'='.$row[1].'<br>';
        if (substr($row[0],-6) == '_boost')    $boost   .= $row[0].'='.sprintf('%03.1f',$row[1]/10).'%<br>';
        if (substr($row[0],-7) == '_demand')   $demand  .= $row[0].'='.$row[1].'<br>';
        if (substr($row[0],-7) == '_amount')   $demand  .= $row[0].'='.$row[1].'<br>';
        if (substr($row[0],0,5) == 'input')
        { $p = explode('[',substr($row[0],5));
          $in_t[$p[1]][$p[0]] = $row[1];
          unset ($raw_properties[$row[0]]);
        }
        if (substr($row[0],0,6) == 'output')
        { $p = explode('[',substr($row[0],6));
          $out_t[$p[1]][$p[0]] = $row[1];
          unset ($raw_properties[$row[0]]);
        }
    }
    db_free_result($property_q);

    // check if selected
    if ($obj_auswahl == 'vehicle')
    { if ($good_auswahl != 255 and $good_auswahl != $category ) return;
      if ($waytype == 'electrified_track' and $engine_type == '' and $power > 0) $engine_type = 'electric';
      if ($engine_auswahl == $engine_type) ; // ok
      elseif ($engine_auswahl == 255)      ; // ok
      elseif ($engine_auswahl != 'none')   return;
      elseif ($engine_type != '')          return;
      if ($wfull > 0 and $power == 0 and $payload > 0) return;
    }
    if ($obj_auswahl == 'factory' and $good_auswahl != 255 and
       (($in_out == 'i' and !in_array($good_auswahl,array_column($in_t ,'good'))) or
        ($in_out == 'o' and !in_array($good_auswahl,array_column($out_t,'good'))) or
        ($in_out == 255 and !in_array($good_auswahl,array_merge( array_column($in_t,'good'), 
                                                                 array_column($out_t,'good')))))) return;
    if ($retire_year < $trv or $intro_year > $trb) return;
    if ($climate_auswahl != 255 and strlen($climates) > 3 and strpos($climates,$climate_auswahl) === false)  return;
    if ($cluster_auswahl != 255 and (($cluster_auswahl > 0 and !in_array($cluster_auswahl,$clusters))
                                or   ($cluster_auswahl < 1 and count($clusters) > 0)) )  return;

    // Maximale Produktion von Fabriken errechnen aus der summe aller boost 
    if (substr($obj_name,-9) == 'kraftwerk') $boost_e = 0;
    $prod_m = ($prod + $range) * 4 * (1 + (($boost_e + $boost_p + $boost_m) / 1000)); 
    $prod_b = $prod  * 4 * (1 + (($boost_e + $boost_p + $boost_m) / 1000)); 


    if ($weight == 0) $weight = .1;
    $weight_full = $weight + ($fracht_gewicht * $payload/ 1000);
    
    $bits_per_month_faktor = 4;
    if (isset($bits_p_faktor[$version_auswahl])) $bits_per_month_faktor = $bits_p_faktor[$version_auswahl];

    // Berechnen Unterhaltskosten Strecke
    $m_faktor = 1;
    if (isset($makie_way_multi[$obj_auswahl.$system_type])) $m_faktor = $makie_way_multi[$obj_auswahl.$system_type];
    if ($waytype == 'monorail_track') $m_faktor = 2;
    if ($waytype == 'water')          $m_faktor *= 3;
    
    $makie_m = $m_faktor * $topspeed;
        
    // Berechnung der Kaufpreise 
    if ($obj_auswahl == 'vehicle')
    { $makie_c = ($power * $speed * 100) + ($weight * 10000)+ ($payload * $speed * 150 * $ertrag);
      /* 150 ist die von einem Fahrzeug mit Geschwindigkeit 1 km/h in einem Jahr gefahrene Entfernung in Kacheln */
      /* 150 * $speed = vom Fahrzeug in einem Jahr befahrene Anzahl Kacheln */
      /* $payload * $speed * 150 = Beförderte Menge in einem Jahr */
      /* $payload * $speed * 150 * $ertrag = der Maximale Ertrag für ein Jahr */
      /* Wenn man von einer Abschreibung über 10 Jahr ausgeht dann sind die Kosten aus dem Kauf als 10% des Jahres Erlös */
      /* Dazu kommen noch extra individuelle Kosten für Leistung Geschwindigkeit und Gewicht. Schwere, starke oder schnell Fahrzeuge kosten also mehr. */
      if ($waytype == 'air')   $makie_c = $payload * $speed * 2 * 150 * $ertrag;
      /* Da bei Fugzeugen die Leistung nicht genau bestimmt ist oder nicht ermittelbar ist wird hier einfach 20% vom Jahreserlös auf 10 Jahre genommen */
      if ($waytype == 'water') $makie_c = ($power * $speed * 100) + ($payload * $speed * 150 * $ertrag);
      /* Bei Schiffen ist im Pak128.german oft nicht der reale payload sonder ein dem Spiel angemessender Payload angegeben, das führt bei Schiffen zu einer Verzerrung vor allem beim Gewicht.. Das Gewicht wird bei Schiffen deshalb weg gelassen */  
    } elseif (in_array($obj_auswahl, $sub_waytypes))
    { if ($waytype == 'water') $makie_c = $makie_m * 1000;
      else                     $makie_c = $makie_m * 100;
    } else $makie_c = 0;
      
    // Berechnung der Betriebskosten 
    $speed_kor = ($speed * max(30,(2040-$intro_year))) / 100;
    /* Normierte Geschwindigkeit = 100km/h = ein wirklich schnell Reisender */
    /* 1800 ->  40km/h entsprechen 100 km/h */
    /* 1840 ->  50km/h entsprechen 100 km/h */
    /* 1870 ->  60km/h entsprechen 100 km/h */
    /* 1890 ->  66km/h entsprechen 100 km/h */
    /* 1900 ->  72km/h entsprechen 100 km/h */
    /* 1910 ->  76km/h entsprechen 100 km/h */
    /* 1920 ->  83km/h entsprechen 100 km/h */
    /* 1930 ->  90km/h entsprechen 100 km/h */
    /* 1940 -> 100km/h entsprechen 100 km/h */
    /* 1940 -> 100km/h entsprechen 100 km/h */
    /* 1950 -> 110km/h entsprechen 100 km/h */
    /* 1960 -> 125km/h entsprechen 100 km/h */
    /* 1970 -> 140km/h entsprechen 100 km/h */
    /* 1980 -> 160km/h entsprechen 100 km/h */
    /* 1990 -> 200km/h entsprechen 100 km/h */
    /* 2010 -> 300km/h entsprechen 100 km/h */
    /* auf mindestens 30% begrenzt denn ab 2010 läuft die Formel dann schnell aus dem Ruder */
    /* Die Formel bildet die Geschwindgkeit um 1900 nicht ganz korrekt ab, */ 
    /* um 1900 kam es zu einem Geschwindkeitswettbewerb zwischen den Länderbahnen */
    if ($waytype == 'air')   $speed_kor = $speed_kor / 4;
    /* In der Luft sind Geschwindigkeiten bis 800km/h normal. */
    /* 1940 -> 400 km/h entsprechen 100 km/h Normierter Geschwindigkeit */
    /* 1990 -> 800 km/h entsprechen 100 km/h Normierter Geschwindigkeit */
    $makie_rcs = (($speed_kor * $speed_kor) / 5)  / 150;
    if ($waytype == 'air') $makie_rcl = ($power * $speed_kor * $speed_kor ) / 5000000;
    else                   $makie_rcl = ($power * $speed_kor ) / 1000;
    if (strpos(strtolower($obj_name),'treidel') !== false) $makie_rcl *= 100;
    /* Je stärker und schneller die Lok oder Zugmaschine um so mehr Brennstoffverbrauch und Wartungskosten. Die Geschwindigkeit erhöht die Kosten im Quadrat so wie auch der Luftwiderstand quadratisch zu nimmt. */ 
    $makie_rcw = ($weight * $speed_kor * $speed_kor ) / 30000;
    /* Auch Masse wenn beschleunigt wird kostet Brennstoff. Der Verbrauch nimmt ebenfalls quadratisch mit der Geschwindigkeit zu. Tender und Anhänger verursachen hier Kosten schon alleine durch ihr Gewicht. */
    $makie_rcp = $payload * $ertrag * 15 / 100;
    /* Für eine gleichmäßige Kostenstrucktur im Spiel, wird für Fracht 15% vom Ertrag als mindest Beriebskosten genommen. Da inbesondere bei Sonderfracht die Rückfahrt meist leer ist, sind 50% vom Ertrag als maximum für die Betriebskosten anzusehen.*/
    $makie_rc  = $makie_rcs + $makie_rcl + $makie_rcw + $makie_rcp;

    // Berechnen der Fix Kosten
    $makie_fc = ($payload * $speed * 150 * $ertrag * 1 / (12 * 100)) + $makie_rcs* 100 + $makie_rcl* 100 + $makie_rcw* 100; 
    
    // speichern
    if (in_array('cost_makie',$property_list) and abs($cost - $makie_c) > 2)      $makie_p .= $obj_name.'>'.'cost='.intval($makie_c)."\n";
    if (in_array('rcost_makie'  ,$property_list) and abs($rcost - $makie_rc) > 2) $makie_p .= $obj_name.'>'.'runningcost='.intval($makie_rc)."\n";
    if (in_array('fcost_makie'  ,$property_list) and abs($fcost - $makie_fc) > 2) $makie_p .= $obj_name.'>'.'fixed_cost='.intval($makie_fc / $bits_per_month_faktor)."\n"; 
    if (in_array('maint_makie'  ,$property_list) and abs($maintenance - $makie_m) > 2) $makie_p .= $obj_name.'>'.'maintenance='.intval($makie_m)."\n";

    // zugkraft berechnen
    $zugkraft = 0; $zieht = 0; $schwelle = 999; $tekz = 1;
    if ($waytype == 'track' and $engine_type == 'steam' and $tender1 != '') $tekz = 1.7; 
    if ($power > 0 and $kmh <= $speed)
    { $sk = $speed; if ($kmh > 0) $sk = $kmh;
      $zugkraft = ((256*$power*$gear/100)/((pow($sk * 256/50,2) / 256) + 256)) - $weight_full;
      if ($wfull > 0 and $tender1 != 'none') $zieht = intval($zugkraft / $wfull);
      if ($wg > 0 and $zieht > 0)         $schwelle = intval(($wg + (2 * $tekz * $makie_rc /*$rcost */ )) / $wg); 
    }
    /*  zk    = ((    $power*$gear/100)/((pow($speed*1024/80,2) / 6400)+64))*100-$weight_full */

    //start the row
    //determine style (grey every 2nd line)
    //$style = (($x % 2) == 0)?'classic':'classic_grey';
    $style = (($x % 2) == 0)?'odd':'even';
    $v_att['tableline'][$x]['style'] =  $style;


    //now scrol through complete propery list and print table content
    //remeber property_list contains the list of names of all properties in specified order
    //we need to retriev valus from raw propertiesi n the same order...
    $t = 0;
    foreach ($property_list as $p_name)
    {   if ($p_name=='name' or $p_name=='obj_name')
        { if ($p_name=='name' and strlen($obj_name_translate) > 2)
               $n = $obj_name_translate;
          else $n = $obj_name; 
          if (strlen($n) > 40) $n = str_replace('&nbsp;',' ',$n);
          $v_att['tableline'][$x]['object_name'] = $n;
          $v_att['tableline'][$x]['edit_link']   = 'edit.php?obj_id='.$ob_id.'&index='.($x-1);
          if ($csv_t != '') $csv_t .= $n.';';
          continue;
        } 
        //special attention for comments
        if ($p_name=='comments')
        { if ( !$object_properties['comments'] ) { $object_properties['comments'] = '&nbsp;'; }
          $v_att['tableline'][$x]['propertylist'][$t]['object_note'] =  $object_properties['comments'];
          $t++;
          continue;
        }

        //special attention for other (remaining properties)
        if ($p_name=='other' OR (substr($p_name,0,10)=='constraint') or ($p_name=='freightimagetype'))
        { $y = 0;
          //scan the rest of the raw p. list and print it
          foreach ($raw_properties as $p_n => $p_value)
          { if ((substr($p_name,0,10)=='constraint' and substr($p_n,0,10) == substr($p_name,0,10))
               or ($p_name=='freightimagetype' and substr($p_n,0,16) == $p_name)                  
               or ($p_name=='other' and !in_array($p_n,$is_displayed)))
            {  // print_line ("$p_n = $p_value<br />", 3);
               $wert = $p_n.' = '.$p_value;
               if ($p_name=='freightimagetype') $wert = $p_value;
               if ($p_name=='constraint_prev' or $p_name=='constraint_next')
               { $spos = strpos($p_n,'['); 
                 if (substr($p_name,-4) == substr($p_n,$spos+1,4)) $wert = $p_value; 
                 else continue;
               }
               $v_att['tableline'][$x]['propertylist'][$t]['valuelist'][$y]['object_values'] = $wert.'&nbsp;';
               $v_att['tableline'][$x]['propertylist'][$t]['valuelist'][$y]['style'] =  $style;
               $y++;
               $is_displayed[] = $p_n;
            }
          }
          if ($y == 0) $v_att['tableline'][$x]['propertylist'][$t]['valuelist'][$y]['object_values'] = '&nbsp;';
          $t++;
          continue;
        }
        //in generic case
        //display the value        
        $wert  = '';
        if ( isset($raw_properties[$p_name]) ) $wert = $raw_properties[$p_name]; 
        
        if ($p_name=='copyright')     $wert = htmlspecialchars($object_properties['obj_copyright'], ENT_QUOTES, "UTF-8");
        if ($p_name=='note')          $wert = htmlspecialchars($object_properties['note'], ENT_QUOTES, "UTF-8");
        if ($p_name=='dat_file_name') $wert = htmlspecialchars($object_properties['dat_file_name'], ENT_QUOTES, "UTF-8");
        if ($p_name=='dat_path')      $wert = htmlspecialchars($object_properties['dat_path'], ENT_QUOTES, "UTF-8");
        if ($p_name=='obj')           $wert = $object_properties['obj'];
        if ($p_name=='nr')        $wert = $x;
        if ($p_name=='payload')   $wert = $payload;
        if ($p_name=='details' and strlen($obj_details) > 2) $wert = html_format($obj_details);
        
        
        if ($p_name=='image')     $wert = display_image($version_auswahl,$ob_id,$dims,$obj_auswahl,$obj_details);
        if ($p_name=='links')     $wert = format_l($obj_links);
        if ($p_name=='enables')   $wert = c_disp($enables);
        if ($p_name=='expand')    $wert = c_disp($expand);
        if ($p_name=='boost')     $wert = c_disp($boost);
        if ($p_name=='demand')    $wert = c_disp($demand);
        if ($p_name=='input'  or $p_name=='input_verbose')  $wert = factoryio($p_name,$in_t, $intro_year,$retire_year,$prod_b);
        if ($p_name=='output' or $p_name=='output_verbose') $wert = factoryio($p_name,$out_t,$intro_year,$retire_year,$prod_b);
        if ($obj_auswahl == 'good' and $p_name == 'value') $wert = intval($wert) / 3;
        if ($p_name=='good') 
        { $b = ''; $wert = '';
          foreach (array_merge( array_column($in_t,'good'), array_column($out_t,'good')) as $g)
          { $wert .= $b."<a href='pakset_info.php?lang=$st&vers=$version_auswahl&obj_auw=$obj_auswahl".
                        "&good=$g' target='_blank'>".tr_translate_text($version_auswahl,$g)."</a>";
            $b = '<br>';
          }
        }
        
       if ($p_name=='climates')
        { $wert = ''; $b = '';
          foreach(explode(',',$climates) as $w)
          { $w = trim($w);
            if(strlen($w) > 2) 
            { $wert .= $b.tr_translate_text($version_auswahl,$w);
              $b = '<br>';
            }
          }
        }

        if ($p_name=='clusters' and count($clu_list) > 0)
        { $wert = ''; $b = '';
          foreach($clusters as $w)
          { $n = intval(trim($w));
            if($n > 0 and isset($clu_list[$n])) $wert .= $b.$clu_list[$n];
            else $wert .= $b.$w; 
            $b = '<br>';
          }
        }
       
        if ($p_name=='ertrag'   and $ertrag > 0)       $wert = sprintf('%05.2f',$ertrag * $payload / 100);
        if ($p_name=='gewinn50' and $ertrag > 0)       $wert = sprintf('%05.2f',(($ertrag * $payload)-(2*$makie_rc /*$rcost */))/ 100);
        if ($p_name=='fracht'   and $fracht != '')   { $wert = tr_translate_text($version_auswahl,$fracht);   $is_displayed[] ='freight'; }
        if ($p_name=='category' and $category != '') { $wert = tr_translate_text($version_auswahl,$category); $is_displayed[] ='freight'; }
        if ($p_name=='weight_full')                    $wert = sprintf('%03.1f',$weight_full);
        if ($p_name=='weight%'  and $payload > 0)      $wert = intval(100 * $weight / $weight_full);
        if ($p_name=='zugkraft' and $power > 0)        $wert = sprintf('%06.2f',$zugkraft);
        if ($p_name=='zieht')
        { if ($power > 0) $wert = $zieht;
          else
          { $wert = "<a href='pakset_info.php?lang=$st&vers=$version_auswahl&obj_auw=$obj_auswahl";
            if ($obj_sub_auswahl != 255) $wert .= '&obj_sub='.$obj_sub_auswahl;
            $wert .= '&trange='.max($intro_year,$trv)."-".min($retire_year,$trb).
                     "&kmh=$speed&wfull=".intval($weight_full*100)."&we=".($payload*$ertrag).
                     "&wg=".(($payload*$ertrag)-(2*$makie_rc /*$rcost */)).
                     '&name='.str_replace('&nbsp;','_',$obj_name)."' target='_blank'> 0 </a>";
          }
        }
        if ($p_name=='schwelle' and $zieht > 0)        $wert = $schwelle;

        
        if ($p_name=='intro') 
        { $wert = $intro_year.'-'.$intro_month;
          $is_displayed[] = 'intro_month';
          $is_displayed[] = 'intro_year';
        }
        if ($p_name=='retire') 
        { $wert = $retire_year.'-'.$retire_month;
          $is_displayed[] = 'retire_month';
          $is_displayed[] = 'retire_year';
        }

        if ($p_name=='max_prod')        $wert = intval($prod_m);
        if ($p_name=='input_makie_cap') $wert = factoryio($p_name,$in_t, $intro_year,$retire_year,$prod_s);
        
        if ($p_name=='maintenance') $wert = sprintf('%05.2f',$maintenance * $bits_per_month_faktor / 100);
        if ($p_name=='maint_makie')
        { if (abs($maintenance - $makie_m) > 2) $wert = sprintf('%05.2f',$makie_m * $bits_per_month_faktor /100);
          else $wert = 'ok';
        } 

        if ($p_name=='runningcost') $wert = sprintf('%05.2f',$rcost / 100);
        if ($p_name=='rcostpp' and $payload > 0 ) $wert = $wert.sprintf('%05.4f',$rcost / $payload / 100);
        if ($p_name=='rcost_makie')
        { if (abs($rcost - $makie_rc) > 2) $wert = sprintf('%05.2f',$makie_rc/100);
          else $wert = 'ok';
        } 
        if ($p_name=='rcost_makie_einzeln') $wert = sprintf('%03.1f',$makie_rcs)
                                            .'+'.sprintf('%03.1f',$makie_rcl)
                                            .'+'.sprintf('%03.1f',$makie_rcw)
                                            .'+'.sprintf('%03.1f',$makie_rcp); 
        if ($p_name=='fcost_makie')
        { if (abs($fcost - $makie_fc) > 2) $wert = sprintf('%05.2f',$makie_fc/100);
          else $wert = 'ok';
        } 
        if ($p_name=='cost') $wert = format_b($cost);
        if ($p_name=='cost_makie')
        { if ($cost == $makie_c) $wert = '----ok----';
          else $wert = format_b($makie_c); 
        }
        if ($p_name=='cost_makie_einzeln') $wert = ($power * $speed).'+'.($weight * 100).'+'.($payload * $speed * 1.50 * $ertrag); 
        if ($p_name=='costpp' and $payload > 0 ) $wert = $wert.sprintf('%06d',$cost / $payload / 100);

        if ($payload > 0 and $speed > 0 and $ertrag > 0)
        { if ($p_name=='cost%e' )   $wert = intval($cost / ($payload * $speed * 15 * $ertrag));
          if ($p_name=='fcost%en')  $wert = sprintf('%03.1f',(100*12*$makie_fc / (150*$speed)) / ($payload * $ertrag) );
          if ($p_name=='rcost%e')   $wert = sprintf('%03.1f',($rcost    *100) / ($payload * $ertrag));
          if ($p_name=='rcost%en')  $wert = sprintf('%03.1f',($makie_rc *100) / ($payload * $ertrag));
          if ($p_name=='rfcost%en') $wert = sprintf('%03.1f',($makie_rc *100 + 2*100*12*$makie_fc / (150*$speed)) / ($payload * $ertrag));/*."/".intval(100*($makie_rcl*100 /*+ $makie_rcl*100 + $makie_rcw*100)/($payload * $speed * 150 * $ertrag * 2 / (12 * 100)));*/
        } elseif ($we > 0)
        { if ($p_name=='rcost%e'  and $zieht > 0) $wert = intval(($rcost    * 100) / ($zieht * $we));
       /*   if ($p_name=='rcost%en' and $zieht > 0) $wert = intval(($makie_rc * 100 + (2*100*12*$makie_fc / (150*$speed))) / ($zieht * $we)); */

        }
        { // if ($p_name=='rcost%e')  $wert = intval(($rcost    * 100) / 1000);
          // if ($p_name=='rcost%en') $wert = intval(($makie_rc * 100) / 1000);
        }
        
        if ($csv_t != '' and strpos($wert,'<img src=') === false) $csv_t .= str_replace('&nbsp;',' ',$wert).';';
        if (strlen($wert) > 3) $wert .= '&nbsp;';
        $v_att['tableline'][$x]['propertylist'][$t]['object_property'] =  $wert;
        $t++;

        //delete, so that we can later collect those that were not processed
        $is_displayed[] = $p_name;
    }
    if ($csv_t != '') $csv_t .= "\n";
    $obj_tab[$x-1] = $ob_id;
    $x++;
    return;

}

function display_statistics ($property_list)
{   
    global $v_att,$st,$x,$obj_tab,$LNG_MAIN, $LNG_STATS_VEH,$versions_all,
           $version_auswahl, $obj_auswahl,$obj_sub_auswahl,$csv_t,$makie_p;
    //get version name
    $v_name = $versions_all[$version_auswahl];

    //get list of all vehicles - do it now to find count
    //if we have track, we also want to dispaly old ones for electrified track
     $join = ''; $sql_sub_obj = subobject_querry($join,$obj_auswahl,$obj_sub_auswahl);
     $vehicles = db_query("SELECT object_id FROM objects o $join WHERE version_version_id=$version_auswahl AND obj='$obj_auswahl'  $sql_sub_obj");

    $count = db_num_rows($vehicles);
    if ( $obj_sub_auswahl == 255 ) {
      $text = sprintf($LNG_STATS_VEH[4], $v_name, $count);
    } else {
      $text = sprintf($LNG_STATS_VEH[3], $obj_sub_auswahl, $v_name, $count);
    }
    $v_att['page_subtitle_table'] = $text;     
    
    $v_att['table_title'] = $LNG_STATS_VEH[5];
    
    //print header - all properties known for vehicles
    //returns the list of property names (for corret order of values)
    $csv_t = ''; $makie_p = '';
    if (in_array('csv',$property_list)) $csv_t = "sep=;\n";
    $x = 1; $obj_tab = array();
    print_table_header ($property_list);
    while ($row = db_fetch_object($vehicles)) print_table_line ($property_list,$row->object_id);
    db_free_result($vehicles);
    if ($csv_t)
    { $file_name = write_temp_file($v_name.".csv",$csv_t);
      $v_att['csv_file']['txt_csv_file']  = "Datei mit dem Tabelleninhalt als csv"; //$LNG_MAIN[20];
      $v_att['csv_file']['link_csv_file'] = $file_name;
    }
    if ($makie_p != '')
    { $file_name = write_temp_file($v_name.".makie.calc",$makie_p);
      $v_att['makie_file']['txt_makie_file']  = "File Preise nach Formel Makie"; //$LNG_MAIN[20];
      $v_att['makie_file']['link_makie_file'] = $file_name;
    }
    $_SESSION['search_result_tab'] = $obj_tab;
    $_SESSION['search_result_len'] = $x-1;

}

function select_box_read_col()
{ $col_auswahl = '1';
  if ( isset($_POST['col_nr']) and $_POST['col_nr'] != '') $col_auswahl =intval($_POST['col_nr']);
  return $col_auswahl;
}

function select_box_read_good()
{ global $in_out;
  $good_auswahl = 255; $in_out = 255;
  if (isset($_SESSION['good_auswahl']))                   $good_auswahl = $_SESSION['good_auswahl'];
  if (isset($_POST['good']) and $_POST['good'] != '')     $good_auswahl = $_POST['good'];
  elseif ( isset($_GET['good']) and $_GET['good'] != '' ) $good_auswahl = $_GET['good'];  
  if (substr($good_auswahl,1,1) == '_')
  { $iw = explode('_',$good_auswahl);
    $in_out       = $iw[0];
    $good_auswahl = $iw[1];
  }
  if (preg_match('#^[io]{1}$#',$in_out) != 1)  $in_out = 255;
  if (preg_match('#^[a-zA-Z0-9@ \._-]{1,20}$#',$good_auswahl) != 1)   $good_auswahl = 255;
  $_SESSION['good_auswahl'] =  $good_auswahl;
  return $good_auswahl;
}

function select_col($obj_auswahl,$col_auswahl)
{ global $c_list;

  if (isset($c_list[$obj_auswahl])) $col_list = $c_list[$obj_auswahl];
  else $col_list = $c_list['rest'];
  if (isset($_SESSION['col_list'.$obj_auswahl])) $col_list = $_SESSION['col_list'.$obj_auswahl];

  $cnr_list = array();
  foreach ($col_list as $ck => $ci) $cnr_list["$ck"] = $ck.': '.$ci;
  $cnr_list[] = (count($col_list)  ).': empty';
  $cnr_list[] = (count($col_list)+1).': empty';
  select_box('select_col_nr',$cnr_list,$col_auswahl,'',-1,'');


  $p_list = read_p_list($obj_auswahl,$col_auswahl);
  
  if ( isset($_POST['col_content']) and $_POST['col_content'] != '' and
       isset($_SESSION['col_pos'])  and $col_auswahl == $_SESSION['col_pos']) 
  { $col_list[$col_auswahl] = $_POST['col_content'];
  }
    
  $cs = 255; $cd = 'empty';
  if ($col_auswahl < count($col_list)) { $cs = $col_list[$col_auswahl]; $cd =''; }
  select_box('select_col_content',$p_list,$cs,'',-1,$cd);
  
  $_SESSION['col_pos'] = $col_auswahl;
  $_SESSION['col_list'.$obj_auswahl] = $col_list;
  return $col_list;
}


////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// M A I N ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

    $version_auswahl = select_box_read_version();
    // load and translate table 
    $climatrans = array();
    foreach ($climat as $e) $climatrans[$e] = tr_translate_text($version_auswahl,$e);
    $clu_list = read_clusters($version_auswahl);

    $obj_auswahl     = select_box_read_obj($version_auswahl);
    $obj_sub_auswahl = select_box_read_sub_obj($version_auswahl,$obj_auswahl); 
    $col_auswahl     = select_box_read_col();
    $climate_auswahl = select_box_read('select_box_climates',$climatrans,255,-1);
    if ($obj_auswahl == 'building' and count($clu_list) > 0 and 
            ($obj_sub_auswahl == implode(', ',$building_city) or
             in_array($obj_sub_auswahl,$building_city)))
         $cluster_auswahl = select_box_read('select_box_cluster',$clu_list,255,-1);
    else $cluster_auswahl = 255;
    if ($obj_auswahl == 'vehicle')
    { $engine_auswahl  = select_box_read('select_box_engine',load_engine_tab($version_auswahl,$obj_auswahl,$obj_sub_auswahl),255,-1);
    } else $engine_auswahl  = 255;
    $good_auswahl    = select_box_read_good();
    $trange          = select_box_read_trange();

    // Parameter zum berechnen der maximalen Zug länge mit einem gegebenen Wagen
    // und zum berechnen der Gesammtbetriebskosten des Zugs
    $kmh = 0;
    if (isset($_GET['kmh']) and intval($_GET['kmh']) > 0) $kmh = intval($_GET['kmh']);
    $wfull = 0;
    if (isset($_GET['wfull']) and intval($_GET['wfull']) > 0) $wfull = intval($_GET['wfull']) / 100;
    $we = 0;
    if (isset($_GET['we']) and intval($_GET['we']) > 0) $we = intval($_GET['we']);
    $wg = 0;
    if (isset($_GET['wg']) and intval($_GET['wg']) > 0) $wg = intval($_GET['wg']);

    $v_att['page_subtitle'] = $LNG_STATS_VEH[1];
    $v_att['trange'] = $trange;
    //display selection menu
   
    select_box_all ($version_auswahl,$obj_auswahl,$obj_sub_auswahl,$LNG_FORM[16]);
    if ($version_auswahl != 255 and $obj_auswahl != 255)
    { $t = 'lang='.$st.'&vers='.$version_auswahl.'&obj_auw='.$obj_auswahl;
      if ($obj_sub_auswahl != 255) $t .= '&obj_sub='.$obj_sub_auswahl;
      if ($good_auswahl != 255)    $t .= '&good='.$good_auswahl;
      if ($trange != '1800-2099')  $t .= '&trange='.$trange;
      if ($kmh > 0)                $t .= '&kmh='.$kmh;
      $v_att['url_param'] = $t;
      read_goods($version_auswahl);
      $col_list = select_col($obj_auswahl,$col_auswahl);
      if ($obj_auswahl == 'vehicle') 
      { $tcat = array();
        foreach($cat_tab as $ctk => $ctv) $tcat[$ctk] = tr_translate_text($version_auswahl,$ctk);
        select_box('select_box_good',$tcat,$good_auswahl,'',-1,$LNG_STATS_VEH[8]);
        select_box('select_box_engine',load_engine_tab($version_auswahl,$obj_auswahl,$obj_sub_auswahl),$engine_auswahl,'',-1,$LNG_STATS_VEH[11]);
        if ( $obj_sub_auswahl=='track' ) 
        { $v_att['info_box']['filename'] = './tpl/info_box.htm';
          $v_att['info_box']['values']['message_0'] = $LNG_STATS_VEH[2];
        }
      } elseif ($obj_auswahl == 'factory')
      { $gnt = array();
        foreach ($good_tab as $gkey => $gentry) $gnt[$gkey] = tr_translate_text($version_auswahl,$gkey);
        asort ($gnt,SORT_STRING | SORT_FLAG_CASE);
        select_box('select_box_good',$gnt,$good_auswahl,'',-1,$LNG_STATS_VEH[8]);
        select_box('select_box_climates',$climatrans,$climate_auswahl,'',-1,$LNG_STATS_VEH[9]);
      } elseif ($obj_auswahl == 'building')
      { select_box('select_box_climates',$climatrans,$climate_auswahl,'',-1,$LNG_STATS_VEH[9]);
        if (count($clu_list) > 0 and 
            ($obj_sub_auswahl == implode(', ',$building_city) or
             in_array($obj_sub_auswahl,$building_city)))
        { select_box('select_box_cluster',$clu_list,$cluster_auswahl,'',-1,$LNG_STATS_VEH[10]);
        }
      }
      display_statistics ($col_list);
    }


    $v_att['link_main'] = $LNG_MAIN[20];
    $v_att['submenu2_link'] = 'main.php?lang='.$st;

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;

  //footer, nothing after this (closes page)
  include_once ("tpl_script/footer.php");
?>
