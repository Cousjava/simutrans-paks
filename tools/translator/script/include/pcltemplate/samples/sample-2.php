<?php
// -----------------------------------------------------------------------------
// PhpConcept Script Engine - sample-2.php
// -----------------------------------------------------------------------------
// Copyright - Vincent Blavet - November 2006
// http://www.phpconcept.net
// -----------------------------------------------------------------------------
// Overview :
//   See http://www.phpconcept.net for more information
// -----------------------------------------------------------------------------
// CVS : $Id$
// -----------------------------------------------------------------------------

  //require_once('../pcltrace.lib.php');
//  require_once('../pcltemplate-trace.class.php');
  require_once('../pcltemplate.class.php');
  
  //PclTraceOn(5);

  // ----- Create the template object
  $v_template = new PclTemplate();
  
  // ----- Prepare a template string
  $v_str = '
    <document name="--[doc_name]--" size="--[doc_size]--">
      <object_list>
        --[list:object_list]--
        --[item]--
        <line ref="--[ref]--">--[value]--</line>
        --[enditem]--
        --[endlist]--
      </object_list>
      --[if:condition_1]--
      <lieu type="ville"></lieu>
      --[endif]--
    </document>
  ';
  
  // ----- Change the delimiters
  $v_template->changeDelimiters('--[',']--');
  
  // ----- Parse the template file
  $v_template->parseString($v_str);
 
  // ----- Prepare data
  $v_att = array();
  $v_att['doc_name'] = 'document.txt';
  $v_att['doc_size'] = '12';
  $v_att['object_list'] = array();
  $i=0;
  $v_att['object_list'][$i]['ref'] = $i;
  $v_att['object_list'][$i]['value'] = 'hello '.$i;
  $i++;
  $v_att['object_list'][$i]['ref'] = $i;
  $v_att['object_list'][$i]['value'] = 'hello '.$i;
  $i++;
  $v_att['object_list'][$i]['ref'] = $i;
  $v_att['object_list'][$i]['value'] = 'hello '.$i;
   
  // ----- Generate result
  $v_result = $v_template->generate($v_att, 'string');
  
  // ----- Display result
  echo '<pre>';
  echo htmlentities($v_result);
  echo '</pre>';

  //PclTraceDisplayNew();
?>
