<?php
require_once("./include/parameter.php");
require_once("./include/dblogin.php");

// always do first
   session_start();


//connection to database

if (!isset($db_host_simu)) die("nanu parameter.php nicht geladen?");

   global $db_host_simu, $db_user_simu, $db_pass_simu, $db_s_simu,$st_dbi,$language_all,$versions_all; 

//   if (error_reporting() != 0) mysqli_report(MYSQLI_REPORT_INDEX);
 
   $st_dbi = mysqli_connect($db_host_simu, $db_user_simu, $db_pass_simu, $db_s_simu);
   if (!$st_dbi) {   echo "Error: Unable to connect to MySQL." . PHP_EOL;
                     echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
                     echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
                 }
//                      echo 'Success... ' . mysqli_get_host_info($st_dbi) . "\n";
 
   if (!isset($_SESSION['language_all']))
   { $language_all =array();
     $sql = "SELECT language_id, language_name FROM languages ORDER BY language_name ASC";
     $lng = mysqli_query($st_dbi,$sql) or die ("SQL error: ".mysqli_error($st_dbi));
     while ($row=mysqli_fetch_array($lng)) $language_all[$row['language_id']]=$row['language_name'];
     mysqli_free_result($lng);
     $_SESSION['language_all'] = $language_all;
   }
   else $language_all = $_SESSION['language_all'];
   
   if (!isset($_SESSION['versions_all']))
   { $versions_all =array();
     $set_enabled = array();
     $set_disabled = array();
     $sql = "SELECT v_name, version_id, activ FROM versions WHERE activ = 1 ORDER BY version_id ASC";
     $vsr = mysqli_query($st_dbi,$sql) or die ("SQL error: ".mysqli_error($st_dbi));
     while ($row=mysqli_fetch_array($vsr))
     { $versions_all[$row['version_id']]=$row['v_name'];
       if     ( $row['activ'] == 1 ) $set_enabled[] = $row['version_id'];
       elseif ( $row['activ'] == 0 ) $set_disabled[] = $row['version_id'];
     }
     mysqli_free_result($vsr);
     $_SESSION['versions_all'] = $versions_all;
     $_SESSION['set_enabled']  = $set_enabled;
     $_SESSION['set_disabled'] = $set_disabled;
   }
   else $versions_all = $_SESSION['versions_all'];
 
 
/* sql query  */
function db_query($query)
{
  global $st_dbi; 
 
  $data = mysqli_query($st_dbi, $query);
  if ($data === false)
  { if (error_reporting() != 0)
    { echo "SQL error: ".mysqli_error($st_dbi).'<br><p>'.$query.'</p>';
      foreach (debug_backtrace() as $backtra)
      { echo $backtra['file']."<br>";
        echo "Zeile : ".$backtra['line']." Funktion : ".$backtra['function']."<br>";

      };
    }
   die ("SQL error <br>");
  } 
  return $data;
}

/* reads result to variable[index] */
function db_fetch_row($queryid) {
  return mysqli_fetch_row($queryid);
}

/* reads result to array variable[index] */
function db_fetch_array($queryid) {
  $data = mysqli_fetch_array($queryid, MYSQLI_BOTH);
  return $data;
}

/* return the current row result set as an object */
function db_fetch_object($queryid) {
  $data = mysqli_fetch_object($queryid);
  return $data;
}

/* return number of rows */
function db_num_rows($queryid) {
  return mysqli_num_rows($queryid);
}

/* number of last affected rows at insert, update, delete*/
function db_affected_rows() {
  global $st_dbi;
  return mysqli_affected_rows($st_dbi);
}

/* result pointer reset  */
function db_data_seek($result, $row_number) {
  return mysqli_data_seek($result, $row_number);
}

/*  */
function db_free_result($result) {
  return mysqli_free_result($result);
}

/* return id of last db insert */
function db_insert_id() {
  global $st_dbi;
  return mysqli_insert_id($st_dbi);
}

/* return value of last db error */
function db_error() {
  global $st_dbi; 
  return mysqli_error($st_dbi);
}

function db_real_escape_string($str) { 
  global $st_dbi; 
  return mysqli_real_escape_string($st_dbi,$str);
}

/*
        returns url with parameter on the end
        @param url - basic url
        @param appendix - parameter to be added
        @return nove_url - url with parameter
*/
function urlAppend ($url, $appendix)
{
  return $url.((strpos($url, "?") > 0) ? "&" : "?").$appendix;
}

////////////////////////////////////////////////////////////////////////////////
///////////////////////////////HIGH LEVEL FUNCTIONS/////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function getrole ($user)
{
  $query = db_query ("SELECT `role` FROM `users` WHERE `u_user_id`='".$user."';");
  $row = db_fetch_array($query);
  db_free_result($query); 
  return $row['role'];
}


//this function executes one-field query end returns the single result
//it returns the first field of first row
//function will cause die, if != rows match the query unles attribute fatal is set to false
//(use LIMIT 1 to restrict result of SQL query if desired)
function db_one_field_query ($query, $fatal = TRUE)
{
    if (!$res   = db_query($query))
    {
        echo "<h1>SQL Error</h1>\n<p>Error: ".db_error(). "</p>\n<p>When function db_one_field_query() executed query: <i>" .$query. "</i></p>";
        include_once ("footer.php");
        die ();
    }

    if(db_num_rows($res) != 1)
    {
        //decide if this is fatal or not
        if ($fatal)
        {
            echo "<h1>SQL Error</h1>\n<p>Function db_one_field_query() expects, that query:<i>" .$query. "</i> returns 1 row, but " .db_num_rows($res). "were returned!</p>";
            include_once ("footer.php");
            die ();
        }else
        {
            echo "<!-- SQL Error: Function db_one_field_query() expects, that query:" .$query. " returns 1 row, but " .db_num_rows($res). "were returned! -->\n";
            return FALSE;
        }
    }

    $value = db_fetch_row($res);
    db_free_result($res);

    //return only one field
    return $value[0];
}


////////////////////////////////////////////////////////////////////////////////
//this is complex function returning result array for any 1 row db query////////
//if there are more rows in the result, function will fail with error.
//use LIMIT 1 in your SQL statement
//returns false if no row was found
function db_query2array ($query_string, $suppress_warnings = FALSE)
{

    //execute query
    if(!($result = db_query($query_string)))
    {
        echo "<h1>SQL Error</h1>\n<p>Error: ".db_error(). "</p>\n<p>While executing query: <i>" .$query_string. "</i></p>";
        include_once ("footer.php");
        die ();
    }

    //check that only one row was returned
    if(db_num_rows($result) > 1)
    {
        echo "dblib.php : db_query2array () <br />\n";
        echo "<h1>SQL Error</h1>\n<p>Function db_query2array() expects, that query:<i>" .$query_string. "</i> returns 1 row, but " .db_num_rows($result). "were returned!</p>";
        include_once ("footer.php");
        die ();
    } elseif (db_num_rows($result) == 0)
    {
        //in this case, do not die, just return false
        if (!$suppress_warnings)
        {
            echo "\n<!-- dblib.php : db_query2array() : warninng - no rows returned by query: $query_string . -->\n";
        }
        return FALSE;
    }

    $output = db_fetch_array ($result);
    db_free_result($result);

    return $output;
}



////////////////////////////////////////////////////////////////////////////////
//this functions returns two dimensional array corresponding to the result of the query
//obviously, this is slow, so it is restricted to results of size less than 100!
function db_fetch_result_as_table ($query_string)
{
    //execute query
    //die if unsuccessful
    if(!($result = db_query($query_string)))
    {
        echo "<h1>SQL Error</h1>\n<p>Error: ".db_error(). " in dblib.php : db_fetch_result_as_table().</p>\n<p>While executing query: <i>" .$query_string. "</i></p>";
        include_once ("footer.php");
        die ();
    }

    $output_array;

    //get number of rows returned
    $row_count = db_num_rows($result);
    
   
    //prohibit user from using this f for too large queries
    if ($row_count > 4000)
    {
        echo "<h1>Error</h1>\n<p>Error: ".db_error(). " in dblib.php : db_fetch_result_as_table().</p>\n<p>Number of rows returned by: <i>" .$query_string. "</i> exceeds the limit 4000.</p>";
        include_once ("footer.php");
        die ();
    }

    //now fetch all rows
    for ($i = 0; $i < $row_count; $i++)
    {
        //fetch row
        $row = db_fetch_array ($result);

        //append row to the array
        $output_array [] = $row;
    }

    db_free_result($result);

    //done
    return $output_array;
}


?>
