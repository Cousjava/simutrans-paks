<?php

/*

*/
  $img_sel = array();
  $img_sel['bridge']      = array ('backimage[ns][0]', 'backimage[ew][0]');
  $img_sel['building']    = array ('backimage[0][0][0][0][0][0]','backimage[4][0][0][0][0][0]');
  $img_sel['citycar']     = array ('image[s]','image[sw]');
  $img_sel['crossing']    = array ('openimage[ew][0]','closedimage[ns][0]');
  $img_sel['tree']        = array ('image[0][0]','image[1][0]');
  $img_sel['ground']      = array ('image[0][0]','image[1][0]');
  $img_sel['ground_obj']  = array ('image[0][0]','image[1][0]','image[0][1]','image[s][0]');
  $img_sel['vehicle']     = array ('emptyimage[s]','emptyimage[sw]');
  $img_sel['pedestrian']  = array ('image[s]','image[sw]');
  $img_sel['factory']     = array ('backimage[0][0][0][0][0][0]');
  $img_sel['field']       = array ('image[0]');
  $img_sel['smoke']       = array ('image[0]');
  $img_sel['tunnel']      = array ('frontimage[n][0]','backimage[n][0]','frontimage[w][0]','backimage[w][0]');
  $img_sel['roadsign']    = array ('image[0]','image[1]','image[2]','image[3]');
  $img_sel['way-object']  = array ('frontimage[ns]','frontimage[sw]');
  $img_sel['way']         = array ('image[ns][0]','image[sw][0]');

////////////////////////////////////////////////////////////////////////////////
// check if allowed to show image
function set_show_img($vid) 
{
   // setting show images from set
  $sql = "SELECT `show_images` FROM `versions` WHERE `version_id`=$vid;";
  $row = db_fetch_array(db_query($sql));  

  $show['registered'] = array( 'tr1', 'tr2', 'painter', 'pakadmin', 'admin' );
  $show['developer'] = array( 'painter', 'pakadmin', 'admin' );   
  
  if ( $row['show_images'] == 1 )                                 return true;    
  elseif ( $row['show_images'] == 2 ) 
  { if ( in_array(trim($_SESSION['role']), $show['registered']) ) return true;
    else                                                          return false;
  } elseif ( $row['show_images'] == 3 ) 
  { if ( in_array(trim($_SESSION['role']), $show['developer']) )  return true;
    else                                                          return false;
  }
  return false;
}

////////////////////////////////////////////////////////////////////////////////
// weil imagecropauto nicht funtioniert mit alpha canal müssen wir selbst suchen
function such_image($im,$im_x,$im_y)
{ $xl=$im_x; $xr=0; $yo=$im_y; $yu=0;
  $background = imagecolorallocatealpha($im, 220, 220, 220, 126);
  $sim_tr = imagecolorallocate($im, 231, 255, 255);

  for ($y = 0; $y<$im_y; $y++)
  { for ($x = 0; $x<$im_x; $x++)
    { $pixel = imagecolorat($im,$x,$y);
      if ($pixel < $background and $pixel != $sim_tr)
      { if ($x < $xl) $xl = $x;
        if ($x > $xr) $xr = $x;
        if ($y < $yo) $yo = $y;
        if ($y > $yu) $yu = $y;
        if ($x < $xr) $x =$xr; // beschleunigen den uninterssanten bereich überspringen
      }
    }
  }
//  echo $xl.'-'.$xr.'/'.$yo.'-'.$yu.'<br>';
  return array($xl,$xr,$yo,$yu);
}

////////////////////////////////////////////////////////////////////////////////
function display_one_image($file,$setid,$img_name,$obj_id)
{ do
    { $tab = 'images_'.$setid;
      $query = "SELECT tile_size, image_data FROM $tab WHERE object_obj_id='$obj_id' AND image_name='$img_name'";
      $img = db_fetch_array(db_query($query));
      if (!$img or $img['image_data'] == '') 
        if (substr($img_name,-3) == '[0]') $img_name = substr($img_name,0,-3);
        else return array(0,0);
      else 
      { $image_size = $img['tile_size'];
        // $im = imageCreateTrueColor($image_size,$image_size);
        $im = imagecreatefromstring($img['image_data']);
        if ($im == false) {echo "img:+".$img."+<br>"; return array(0,0);}
        ImageAlphaBlending($im, false); 
        imagesavealpha($im, true);
        $sim_tr = imagecolorallocate($im, 231, 255, 255);
        /* $cropped = imagecropauto($im, IMG_CROP_THRESHOLD,0.5,$sim_tr);
        // $cropped = imagecropauto($im, IMG_CROP_DEFAULT);
        if ($cropped !== false) 
        { imagedestroy($im);  
          $im = $cropped;      
          ImageAlphaBlending($im, false); 
          imagesavealpha($im, true);
        } */
        $b = such_image($im,$image_size,$image_size);
        imagecolortransparent($im, $sim_tr); // Make the background transparent
        $im_nx = $b[1] - $b[0];
        $im_ny = $b[3] - $b[2];
        if ($im_nx < 1 or $im_ny <1) return array(0,0);
        $im_n = imageCreateTrueColor($im_nx+2,$im_ny+2);
        $background = imagecolorallocatealpha($im_n, 220, 220, 220, 127);
        imagefill($im_n, 0, 0, $background);
        // imagefilledrectangle($im_n, 0, 0, $im_nx+2,$im_ny+2,$background);

        ImageAlphaBlending($im_n, false); 
        imagesavealpha($im_n, true);

        imagecopy($im_n,$im,1,1,$b[0],$b[2],$im_nx,$im_ny);

        imagepng($im_n, $file);
        imagedestroy($im);
        imagedestroy($im_n);
        return array($im_nx+2,$im_ny+2);
      }
    } while (1);
  echo "error while processing img<br>";
  return array(0,0);
}



////////////////////////////////////////////////////////////////////////////////
function display_multicachel($file,$setid,$img_name,$obj_id,$set_tile,$tile_size,$d_b,$d_c,$d_h)
{ $pos_err = $tile_size - $set_tile;
  $q = max($d_b,$d_c);
  $xc = intval($tile_size * $q / 2) + ($tile_size / 4);
  $im_cx = $xc*2;
  $im_cy = ($tile_size*$d_h) + ($tile_size*$q/2) + ($tile_size / 4);
  $im_c = imageCreateTrueColor($im_cx,$im_cy);
  $background = imagecolorallocatealpha($im_c, 220, 220, 220, 127);
  imagefill($im_c, 0, 0, $background);
  ImageAlphaBlending($im_c, false); 
  imagesavealpha($im_c, true);

  $tab = 'images_'.$setid;
  $p = strpos ($img_name,']')+1;
  
  for ($i_h=0; $i_h<$d_h; $i_h++)
  { for ($i_b=0; $i_b<$d_b; $i_b++)
    { for ($i_c=0; $i_c<$d_c; $i_c++)
      { $i = substr($img_name,0,$p).'['.$i_b.']['.$i_c.']['.$i_h.'][0][0]';
        do
        { $query = "SELECT offset_x, offset_y, image_data FROM $tab WHERE object_obj_id='$obj_id' AND image_name='$i'";
          $img = db_fetch_array(db_query($query));
          if ((!$img or $img['image_data'] == '') and substr($i,-3) == '[0]') { $i = substr($i,0,-3); $n = true;}
          else $n = false;
        } while ($n);
        if ($img and $img['image_data'] != '')
        { $im = imagecreatefromstring($img['image_data']);
          $o_x = $img['offset_x'] + ($pos_err * $i_b / 2) - ($pos_err * $i_c / 2);
          $o_y = $img['offset_y'] + ($pos_err * $i_h) - ($pos_err * $i_b / 4) - ($pos_err * $i_c / 4);
          ImageAlphaBlending($im, false); 
          imagesavealpha($im, true);
          $sim_tr = imagecolorallocate($im, 231, 255, 255);
          imagecolortransparent($im, $sim_tr); // Make the background transparent
          $x = $xc - ($i_b * $tile_size / 2) + ($i_c * $tile_size / 2) - ($tile_size / 2) + $o_x;
          $y = (($d_h - $i_h - .5) * $tile_size) + ($i_b * $tile_size / 4) + ($i_c * $tile_size / 4) + $o_y;
          $cut = imagecreatetruecolor($tile_size, $tile_size);
          $background = imagecolorallocatealpha($cut, 220, 220, 220, 127);
          imagefill($cut, 0, 0, $background);
          // fragt mich bitte nicht wiso das so funktioniert und anders nicht
          imagecopy($cut,$im_c,0,0,$x,$y,$tile_size,$tile_size);
          imagecopy($cut,$im,0,0,0,0,$tile_size,$tile_size);
          imagecopy($im_c,$cut,$x,$y,0,0,$tile_size,$tile_size);
          imagedestroy($im);
        } 
      }
    }
  }
  $b = such_image($im_c,$im_cx,$im_cy);
  $im_nx = $b[1] - $b[0];
  $im_ny = $b[3] - $b[2];
  if ($im_nx < 1 or $im_ny <1) return array(0,0);
  $im_n = imageCreateTrueColor($im_nx+2,$im_ny+2);
  $background = imagecolorallocatealpha($im_n, 220, 220, 220, 127);
  imagefill($im_n, 0, 0, $background);
  ImageAlphaBlending($im_n, false); 
  imagesavealpha($im_n, true);
  imagecopy($im_n,$im_c,1,1,$b[0],$b[2],$im_nx,$im_ny); 
  imagepng($im_n, $file);
  imagedestroy($im_c);
  imagedestroy($im_n);

  return (array($im_nx+2,$im_ny+2));
}

function not_in_cache($file,$setid,$obj_id,$img_name,$dims)
{   global $set_title_tab;
    $d = explode(',',$dims);
    $d_c = intval($d[0]); if ($d_c < 2) $d_c = 1;
    $d_b = 1; if (isset($d[1])) $d_b = intval($d[1]); if ($d_b < 2) $d_b = 1;
    $d_h = 1; $d_x = 1; $d_y = 1; $tile_size = 0;
    // die Höhe suchen weil die Höhe nicht in dims steht
    $tab = 'images_'.$setid;
    $q = db_query("SELECT tile_size, image_name FROM $tab WHERE object_obj_id='$obj_id' ");
    while ($img = db_fetch_array($q))
    { $i = explode('[',$img['image_name']);
      if (isset($i[1]) and $i[1] == '0]')
      { if (isset($i[2])) $d_x = max($d_x,1 + intval($i[2]));
        if (isset($i[3])) $d_y = max($d_y,1 + intval($i[3]));
        if (isset($i[4])) $d_h = max($d_h,1 + intval($i[4]));
      }
      if ($tile_size == 0) $tile_size = $img['tile_size'];
      if ($tile_size != $img['tile_size']) echo "tile_size".$tile_size.'!='.$img['tile_size'].'<br>';
    
    }
    if (isset($set_title_tab[$setid])) $set_tile = $set_title_tab[$setid];
    else
    { if (!isset($set_title_tab)) $set_title_tab = array();
      $set_q = db_query("SELECT tile_size FROM versions WHERE version_id=".$setid);
      $set_r = db_fetch_row($set_q);
      $set_tile = $set_r[0];
      $set_title_tab[$setid] = $set_tile;
    }
    if ($tile_size < 20 or $tile_size > 600) { /*echo $obj_id." tile_size:".$tile_size.'<br>';*/ $tile_size = $set_tile;}
    if ($d_b != $d_x or $d_c != $d_y) echo $obj_id.' dims '.$d_c.','.$d_b.'nach img'.$d_y.','.$d_x.'<br>';
    
    if ($d_x == 1 and $d_y == 1 and $d_h ==1) return display_one_image($file,$setid,$img_name,$obj_id);
    else return display_multicachel($file,$setid,$img_name,$obj_id,$set_tile,$tile_size,$d_x,$d_y,$d_h);
}


////////////////////////////////////////////////////////////////////////////////
function display_image($setid,$obj_id, $dims, $obj_type, $alt_text='', $re_sizes=0)
{ global $img_sel,$set_title_tab,$LNG_EDIT;
  $setid  = intval($setid);
  $obj_id = intval($obj_id);
  $img_cache = array();
  if (isset($_session['img_in_cache'])) $img_cache = $_session['img_in_cache'];
  $wert ='';
  if (isset($img_sel[$obj_type])) foreach ($img_sel[$obj_type] as $img_name)
  { if ($alt_text == '') $a_text = htmlentities($img_name, ENT_QUOTES);
    else                 $a_text = htmlentities($alt_text, ENT_QUOTES);
    $file = "./tpl_cache/".$setid."_".$obj_id.$img_name.'c';
    if (isset($img_cache[$file])) $img_p = $img_cache[$file];
    elseif ( file_exists($file))
    { $size = getimagesize($file);
      $img_p = array($size[0],$size[1]);
      $img_cache[$file] = $img_p;
    } 
    else 
    { /*if ( count($img_sel[$obj_type]) > 1) $img_p = display_one_image($file,$setid,$img_name,$obj_id);
      else */ $img_p = not_in_cache($file,$setid,$obj_id,$img_name,$dims);
      $img_cache[$file] = $img_p;
    }
    $x = $img_p[0];
    $y = $img_p[1];
    if ($x > 1 and $y > 1)
    { if ($re_sizes > 0)
      { $m = max($x,$y);
        if ($m > $re_sizes)
        { $x = intval($x / ($m / $re_sizes));
          $y = intval($y / ($m / $re_sizes));
        }
      } 
      $wert .= "<img src='".$file."' alt='$a_text' title='$a_text'"
              ." height='$y' width='$x' />\n";
    }
  }
  if ($wert == '') $wert .= $LNG_EDIT[9]."\n";
  $_session['img_in_cache'] = $img_cache;
  return $wert;
}

////////////////////////////////////////////////////////////////////////////////
//
function cacheImage($version, $obj, $name,&$img) {

  $file = "tpl_cache/".$version."_".$obj.$name;
  if ( file_exists($file) ) return $file;

  $im = imagecreatefromstring($img);
  if ($im == false) {echo "img:+".$img."+<br>"; return "error.pnp";}
  ImageAlphaBlending($im, false); 
  imagesavealpha($im, true);
  imagepng($im, $file);
  imagedestroy($im);

  return $file;
}

function display_image_tile ($obj_id, $obj_ver_id, $tag_prop = '', $image_show_count=999,$wrap=true)
{   global $LNG_EDIT;
    $tab = 'images_'.$obj_ver_id;
    $query = "SELECT `image_name`, `tile_size`, `image_data` FROM `$tab` WHERE `object_obj_id`='$obj_id' ORDER BY `image_name` ASC";
    $result = db_query($query);
    $image_count = db_num_rows($result);

    if ($image_count == 0)
    {   db_free_result($result);
//      return '<img src="../images/dummy.png" width="64" height=264"/>\n';
        return $LNG_EDIT[9];
    }

    if ($wrap) $ausgabe = "";
    else       $ausgabe = "<nobr>";
    
    if ( $image_count > $image_show_count ) $image_count = $image_show_count;
    
    for ($i=0; $i < $image_count; $i++)
    { $img_n = db_fetch_array($result);
      $image_size = $img_n['tile_size'];
      if ( $img_n['image_data'] == '') continue;
      $ausgabe .= "<img src='".cacheImage($obj_ver_id, $obj_id, $img_n['image_name'],$img_n['image_data'])."' $tag_prop alt='" . htmlentities($img_n['image_name'], ENT_QUOTES) . "' title='" . htmlentities($img_n['image_name'], ENT_QUOTES) . "' width='".$image_size."' height='".$image_size."'/>\n";
    }

    if (!$wrap) $ausgabe .= "</nobr>";

    db_free_result($result);    
    return $ausgabe;
}

function cacheImageClear() {
  global $tpl_cache_time;

  $t = time() - $tpl_cache_time;
  $verzeichnis = scandir ('./tpl_cache/',1);
  foreach ($verzeichnis as $dateiname)
  { if ( filemtime('tpl_cache/'.$dateiname) < $t ) 
    { if ( ($dateiname != '.') && ($dateiname != '..') ) 
      { unlink('./tpl_cache/'.$dateiname);
      }
    }
  } 
}

?>
