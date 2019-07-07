<?php
/*
    SimuTranslator
    General Page Footer
    Tomas Kubes 2006

    Footer must be printed on all pages.
*/


  require_once("./include/pcltemplate/pcltemplate.class.php");

  unset($v_att);     

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/footer.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['about'] = "About SimuTranslator";
  $v_att['zipsupport'] = "used zip support from";
  $v_att['tplsupport'] = "used template support from"; 
 
  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;

?>
