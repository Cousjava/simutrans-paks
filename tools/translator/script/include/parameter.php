<?php

error_reporting(E_ALL);
//error_reporting(0);
//phpinfo();
umask(002);

date_default_timezone_set('Europe/Berlin');


//$servermail = 'translator@simutrans-germany.com';
//$servermail = 'makie@makie.de';

// kontakt form user adress
$contact_adr = array();
$contact_adr[0] = "makie@makie.de";
//$contact_adr[1] = "name@example.com";

// template cache time in sekunden
$tpl_cache_time = 120;

$FORMAT_DATUM = "d. M Y H:i";   

// Userlevel für admin_user.php
$minimal_user_level=array('guest','tr1','tr2','admin','gu','painter','pakadmin');  
// Userstates
$userstates = array('active', 'suspended', 'removed');

// directorys
$datapfad   = '../data/';
$tempfilepfad = '../data/temp/';
$htmlpfad   = '../data/html/';
$htmlexpfad = '../data/htmlex/';
$imagepfad  = '../data/img/';
$tabpfad    = '../data/tab/';
$savpfad    = '../data/sav/';
$sugpfad    = '../data/sug/';
$exportpfad = '../data/ex/';
$zippfad    = '../data/tab/';  
$webpfad    = '../data/web/';  


define('TMP_PATH', $tempfilepfad);
define ('TMP_DIRECTORY', $tempfilepfad."obj_import/");


// defaults Language  
$default_lang = array();
$default_lang['name'] = 'Language name';
$default_lang['font1'] = 'Prop-Latin1.bdf';
$default_lang['font2'] = '';
$default_lang['codepage'] = 'cp852';

// codepage
$lang_codepage = array();
$lang_codepage[0] = "UCS-4";
$lang_codepage[2] = "UCS-4BE";
$lang_codepage[3] = "UCS-4LE";
$lang_codepage[4] = "UCS-2";
$lang_codepage[5] = "UCS-2BE";
$lang_codepage[6] = "UCS-2LE";
$lang_codepage[7] = "UTF-32";
$lang_codepage[8] = "UTF-32BE";
$lang_codepage[9] = "UTF-32LE";
$lang_codepage[10] = "UCS-2LE";
$lang_codepage[11] = "UTF-16";
$lang_codepage[12] = "UTF-16BE";
$lang_codepage[13] = "UTF-16LE";
$lang_codepage[14] = "UTF-8";
$lang_codepage[15] = "UTF-7";
$lang_codepage[16] = "ASCII";
$lang_codepage[17] = "EUC-JP";
$lang_codepage[18] = "SJIS";
$lang_codepage[19] = "eucJP-win";
$lang_codepage[20] = "SJIS-win";
$lang_codepage[21] = "ISO-2022-JP";
$lang_codepage[22] = "JIS";
$lang_codepage[23] = "ISO-8859-1";
$lang_codepage[24] = "ISO-8859-2";
$lang_codepage[25] = "ISO-8859-3";
$lang_codepage[26] = "ISO-8859-4";
$lang_codepage[27] = "ISO-8859-5";
$lang_codepage[28] = "ISO-8859-6";
$lang_codepage[29] = "ISO-8859-7";
$lang_codepage[30] = "ISO-8859-8";
$lang_codepage[31] = "ISO-8859-9";
$lang_codepage[32] = "ISO-8859-10";
$lang_codepage[33] = "ISO-8859-13";
$lang_codepage[34] = "ISO-8859-14";
$lang_codepage[35] = "ISO-8859-15";
$lang_codepage[36] = "byte2be";
$lang_codepage[37] = "byte2le";
$lang_codepage[38] = "byte4be";
$lang_codepage[39] = "byte4le";
$lang_codepage[40] = "BASE64";
$lang_codepage[41] = "7bit";
$lang_codepage[42] = "8bit";
$lang_codepage[43] = "UTF7-IMAP";
$lang_codepage[44] = "EUC-CN";
$lang_codepage[45] = "CP936";
$lang_codepage[46] = "HZ";
$lang_codepage[47] = "EUC-TW";
$lang_codepage[48] = "CP950";
$lang_codepage[49] = "BIG-5";
$lang_codepage[50] = "EUC-KR";
$lang_codepage[51] = "UHC";
$lang_codepage[52] = "(CP949)";
$lang_codepage[53] = "ISO-2022-KR";
$lang_codepage[54] = "Windows-1251";
$lang_codepage[55] = "CP1251";
$lang_codepage[56] = "Windows-1252";
$lang_codepage[57] = "(CP1252)";
$lang_codepage[58] = "CP866";
$lang_codepage[59] = "KOI8-R";

$lang_fonts = array();
$lang_fonts['cn'] = 'wenquanyi_9pt.bdf';
$lang_fonts['cn_byte'] = '3 MB';
$lang_fonts['cyr'] = 'cyr.bdf';
$lang_fonts['cyr_byte'] = '414 kB';
$lang_fontsfiles = array();
$lang_fontsfiles['wenquanyi'] = array('cn');
$lang_fontsfiles['cyr'] = array('tr', 'gr', 'ko', 'lt', 'hu');


// codepages sortieren
usort($lang_codepage, "strnatcmp");

// grafics size
$ar_setsize = array();
$ar_setsize[0] = 0;
$ar_setsize[1] = 32;
$ar_setsize[2] = 48;
$ar_setsize[3] = 64;
$ar_setsize[4] = 96;
$ar_setsize[5] = 128;
$ar_setsize[6] = 160;
$ar_setsize[7] = 192;
$ar_setsize[8] = 224;
$ar_setsize[9] = 254;
$ar_setsize[10] = 256;
$ar_setsize[11] = 512;

// sets 
$id_tilecutter = 100; // TileCutter       
$id_onlinedat = 202; // Online-Dat       
$id_simutrans_com = 204; // www.simutrans.com webpage       

// sets
define("VEH_NAME_LEN",          60);
define("BASE_TEXTS_SET_ID",     0);   // Base texts
define("HELP_BASE__SET_ID",    10);   // Help files for Base Simutrans
define("EXTE_TEXTS_SET_ID",   101);   // Simutrans Experimental 
define("HELP_EXTEN_SET_ID",   102);   // Simutrans Experimental Help files
define("TRANSLATOR_SET_ID",   200);   // Simutrans Experimental Help files

define("INTERNAL_ENCODING",     "UTF-8");

define("COND_NO_INFO",          1);
define("COND_NO_BREAKS",        2);

// *_text array + show note
$object_text = array();
$object_text[0] = 'message_text';
$object_text[1] = 'button_text';
$object_text[2] = 'climates_text';
$object_text[3] = 'error_text';
$object_text[4] = 'help_text';
$object_text[5] = 'menu_text';
$object_text[6] = 'program_text';
$object_text[7] = 'record_text';
$object_text[8] = 'ki_text';
$object_text[9] = 'help_file';
$object_text[10] = 'dummy_info';
$object_text[11] = 'tilecutter';
$object_text[12] = 'translator';
$object_text[13] = 'win_installer';
$object_text[14] = 'onlinedat';
$object_text[15] = 'unnecessary_text';
$object_text[16] = 'serverlist';
$object_text[17] = 'webpage';
$object_text[18] = 'scenario_text';
$object_text[19] = 'scenario_textfile';
$object_text[20] = 'squirrel_text';
$object_text[21] = 'web_site';

//only display for certain object types
//some uninteresting can be skipped
$object_nodisp = array();
#$object_nodisp[] = 'crossing';
$object_nodisp[] = 'smoke';
$object_nodisp[] = 'cursor';
#$object_nodisp[] = 'ground';
#$object_nodisp[] = 'misc';
$object_nodisp[] = 'symbol';
#$object_nodisp[] = 'field';
#$object_nodisp[] = 'menu';

$building_city = array();
$building_city[0] = 'res';
$building_city[1] = 'com';
$building_city[2] = 'ind';
$building_city[3] = 'tow';


$building_cur = array();
$building_cur[0] = 'cur';
$building_cur[1] = 'mon';


$building_player = array();
$building_player[0] = 'stop';
$building_player[1] = 'habour';
$building_player[2] = 'depot';
$building_player[3] = 'extension';
$building_player[4] = 'hq';
$building_player[5] = 'dock';


$sub_waytypes = array();
$sub_waytypes[0] = 'vehicle';
$sub_waytypes[1] = 'way';
$sub_waytypes[2] = 'way-object';
$sub_waytypes[3] = 'tunnel';
$sub_waytypes[4] = 'roadsign';
$sub_waytypes[5] = 'bridge';

$way_type = array();
$way_type[0] = 'track';
$way_type[1] = 'tram_track';
$way_type[2] = 'monorail_track';
$way_type[3] = 'maglev_track';
$way_type[4] = 'road';
$way_type[5] = 'water';
$way_type[6] = 'air';
$way_type[7] = 'narrowgauge_track';
$way_type[8] = 'power';
$way_type[9] = 'electrified_track';
$way_type[10] = 'schiene_tram'; // old parameter

sort($way_type);

$no_sub_obj = array();
$no_sub_obj[0] = 'ground_obj';
$no_sub_obj[1] = ''; //bridge
$no_sub_obj[2] = 'citycar';
$no_sub_obj[3] = 'pedestrian';
$no_sub_obj[4] = 'tree';
$no_sub_obj[5] = 'button_text';
$no_sub_obj[6] = 'climates_text';
$no_sub_obj[7] = 'error_text';
$no_sub_obj[8] = 'help_text';
$no_sub_obj[9] = 'menu_text';
$no_sub_obj[10] = 'program_text';
$no_sub_obj[11] = 'record_text';
$no_sub_obj[12] = 'dummy_info';
$no_sub_obj[13] = 'ki_text';
$no_sub_obj[14] = 'message_text'; 
$no_sub_obj[15] = ''; //way 
$no_sub_obj[16] = ''; //way-object
$no_sub_obj[17] = ''; //roadsign
$no_sub_obj[18] = ''; //tunnel
$no_sub_obj[19] = 'factory'; 
$no_sub_obj[20] = 'help_file'; 
$no_sub_obj[21] = 'tilecutter'; 
$no_sub_obj[22] = 'translator'; 
$no_sub_obj[23] = 'win_installer';
$no_sub_obj[24] = 'onlinedat'; 
$no_sub_obj[25] = 'unnecessary_text';
$no_sub_obj[26] = 'serverlist';
$no_sub_obj[27] = 'webpage';
$no_sub_obj[28] = 'scenario_text';
$no_sub_obj[29] = 'scenario_textfile';
$no_sub_obj[30] = 'squirrel_text';
$no_sub_obj[31] = 'factory_info';


/* Steuertabelle für Wurzelgnome
$edit_conf = array(); // htm-file-name,html,rows,cols 
$edit_conf['undefind']          = array('edit'      ,'text',35,70);
$edit_conf['help_file']         = array('edit'      ,'html',35,70);
$edit_conf['dummy_info']        = array('edit'      ,'html',35,70);
$edit_conf['webpage']           = array('edit'      ,'html',35,70);
$edit_conf['scenario_textfile'] = array('edit'      ,'html',35,70);
$edit_conf['good']              = array('edit_block','text', 1,40);
$edit_conf['button_text']       = array('edit_block','text', 1,40);
$edit_conf['climates_text']     = array('edit_block','text', 1,40);
$edit_conf['menu_text']         = array('edit_block','text', 1,40);
$edit_conf['tunnel']            = array('edit_block','text', 4,35);
$edit_conf['bridge']            = array('edit_block','text', 4,35);
$edit_conf['com']               = array('edit_block','text', 8,45);
$edit_conf['ind']               = array('edit_block','text', 8,45);
$edit_conf['res']               = array('edit_block','text', 8,45);
$edit_conf['tree']              = array('edit_block','text', 8,45);
*/

// Steuertabelle ohne links und History
$edit_conf = array(); // htm-file-name,html,rows,cols, (col_typ,html,rows,cols)*x //first is always 't'
$edit_conf['undefind']          = array('edit'      ,'text',15,80);
$edit_conf['help_file']         = array('edit'      ,'html',35,100);
$edit_conf['dummy_info']        = array('edit'      ,'html',35,70);
$edit_conf['webpage']           = array('edit'      ,'html',35,70);
$edit_conf['scenario_textfile'] = array('edit'      ,'html',35,70);
$edit_conf['factory']           = array('edit'      ,'text', 1,70,'d','html',10,100);
$edit_conf['vehicle']           = array('edit'      ,'text', 1,70);
$edit_conf['good']              = array('edit'      ,'text', 1,40);
$edit_conf['button_text']       = array('edit'      ,'text', 1,80);
$edit_conf['climates_text']     = array('edit'      ,'text', 1,80);
$edit_conf['menu_text']         = array('edit'      ,'text', 1,80);
$edit_conf['error_text']        = array('edit'      ,'text', 1,80);
$edit_conf['message_text']      = array('edit'      ,'text', 3,80);
$edit_conf['program_text']      = array('edit'      ,'text', 1,80);
$edit_conf['tunnel']            = array('edit'      ,'text', 4,35);
$edit_conf['bridge']            = array('edit'      ,'text', 4,35);
$edit_conf['com']               = array('edit'      ,'text', 3,70);
$edit_conf['ind']               = array('edit'      ,'text', 3,70);
$edit_conf['res']               = array('edit'      ,'text', 3,70);
$edit_conf['cur']               = array('edit'      ,'text', 3,70);
$edit_conf['tree']              = array('edit'      ,'text', 8,45);
$edit_conf['web_site']          = array('edit'      ,'web' , 35,200);

// Steuertabelle mit Details und History
$edit_conf_20 = array(); // htm-file-name,html,rows,cols, (col_typ,html,rows,cols)*x //first is always 't'
$edit_conf_20['undefind']          = array('edit'      ,'text',35,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['help_file']         = array('edit'      ,'html',35,100);
$edit_conf_20['scenario_textfile'] = array('edit'      ,'html',35,70);
$edit_conf_20['factory']           = array('edit'      ,'text', 1,70,'d','html',10,100,'l','link',3,100,'h','web',20,100);
$edit_conf_20['vehicle']           = array('edit'      ,'text', 1,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['good']              = array('edit'      ,'text', 1,40);
$edit_conf_20['button_text']       = array('edit_block','text', 1,40);
$edit_conf_20['climates_text']     = array('edit'      ,'text', 1,40);
$edit_conf_20['menu_text']         = array('edit_block','text', 1,40);
$edit_conf_20['tunnel']            = array('edit'      ,'text', 4,35,'l','link',1,100,'h','web',20,100);
$edit_conf_20['bridge']            = array('edit'      ,'text', 4,35,'l','link',1,100,'h','web',20,100);
$edit_conf_20['com']               = array('edit'      ,'text', 3,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['ind']               = array('edit'      ,'text', 3,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['res']               = array('edit'      ,'text', 3,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['cur']               = array('edit'      ,'text', 3,70,'l','link',1,100,'h','web',20,100);
$edit_conf_20['tree']              = array('edit'      ,'text', 8,45);
$edit_conf_20['web_site']          = array('edit'      ,'web' , 35,200);


$edit_conf_tab = array();
$edit_conf_tab[20] = $edit_conf_20;


$no_rss = array();
$no_rss[0] = "220"; // set test


$climat = array();
$climat[0] = 'desert';
$climat[1] = 'tropic';
$climat[2] = 'mediterran';
$climat[3] = 'temperate';
$climat[4] = 'tundra';
$climat[5] = 'rocky';
$climat[6] = 'arctic';


?>
