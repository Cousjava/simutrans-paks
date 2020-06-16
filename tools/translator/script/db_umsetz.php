<?php
    require_once('./include/parameter.php'); 
    require_once('./include/dblib.php');
    require_once('./include/general.php');

    die();
    
    db_query("START TRANSACTION");
    foreach($versions_all as $vs_id => $vs_a_name) 
    { echo "<br><h1>ALTER TABLE for Version ".$vs_id.":".$vs_a_name."</h1><br>";
 
      $o_q = db_query("SELECT * FROM objects WHERE version_version_id=$vs_id");
      while ($o = db_fetch_array($o_q))
      { $t = "";
        if ($o['obj'] == 'building') 
        { $p_q = db_query ("SELECT p_name, p_value FROM property WHERE p_name='type' AND having_obj_id=".$o['object_id']." ");
          while ($p = db_fetch_array($p_q))
          { if ($p['p_name'] ='type') $t = $p['p_value'];
          }
        } elseif (in_array($o['obj'],$sub_waytypes)) 
        { $p_q = db_query ("SELECT p_name, p_value FROM property WHERE p_name='waytype' AND having_obj_id=".$o['object_id']." ");
          while ($p = db_fetch_array($p_q))
          { if ($p['p_name'] ='waytype') $t = $p['p_value'];
          }
        } elseif ($o['type'] != "") echo $o['type'].="falsch in".$o['obj']."<br>\n";
        else continue;
        $t =  strtolower(trim($t));
        if ($o['type'] == $t) 
        { echo "ok\n"; continue;
        }
        echo "update from'".$o['type']."' to '".$t."' <br>\n";
        $u_q = db_query("UPDATE objects SET type='".$t."' WHERE object_id=".$o['object_id']);
        if ($u_q === false) die("obj update db_error".mysqli_error($st_dbi));
        if (db_affected_rows() != 1) die("anzahl ungleich 1");
      }
     // echo $o[type]."=".$t."<br>\n";
    };

//    db_query("START TRANSACTION");

    
    db_query("COMMIT");

    echo "ende Umsetzung alles ok";
    
?>
