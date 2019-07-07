<?PHP


function quote_smart($value)
{
  global $st_dbi;
   // Ueberfluessige Maskierungen entfernen
   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }
   $value = mysqli_real_escape_string($st_dbi, $value);
   return $value;
}



?>
