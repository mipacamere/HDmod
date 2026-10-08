<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2025 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    any later version accepted by Marco Maria Francesco De Santis, which
#    shall act as a proxy as defined in Section 14 of version 3 of the
#    license.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##################################################################################



function trova_codice_rel ($rel,&$rel_esist,$rel_sing,$rel_plur,&$cod2,&$cod3,$tablerel,$tablerelutenti,$id_utente) {
$cod = "";
$cod2 = "";
$cod3 = "";
if (strcmp((string) $rel,"")) {
if (empty($rel_esist['e'][$rel_sing][$rel])) {
if (strstr($rel,"&")) {
$rel_decod = trim(html_decod($rel));
if ($rel_decod == $rel) $rel_decod = "";
} # fine if (strstr($rel,"&"))
else $rel_decod = "";
if (!$rel_decod) $cod = esegui_query("select distinct $tablerel.codice_$rel_sing,$tablerel.codice2_$rel_sing,$tablerel.codice3_$rel_sing from $tablerelutenti inner join $tablerel on $tablerelutenti.id$rel_sing = $tablerel.id$rel_plur where $tablerelutenti.idutente = '$id_utente' and $tablerel.nome_$rel_sing = '".aggslashdb($rel)."' ");
else $cod = esegui_query("select distinct $tablerel.codice_$rel_sing,$tablerel.codice2_$rel_sing,$tablerel.codice3_$rel_sing from $tablerelutenti inner join $tablerel on $tablerelutenti.id$rel_sing = $tablerel.id$rel_plur where $tablerelutenti.idutente = '$id_utente' and ($tablerel.nome_$rel_sing = '".aggslashdb($rel)."' or $tablerel.nome_$rel_sing = '".aggslashdb($rel_decod)."') ");
if (numlin_query($cod)) {
$cod2 = risul_query($cod,0,"codice2_$rel_sing",$tablerel);
$cod3 = risul_query($cod,0,"codice3_$rel_sing",$tablerel);
$cod = risul_query($cod,0,"codice_$rel_sing",$tablerel);
} # fine if (numlin_query($cod))
else {
if (!$rel_decod) $cod = esegui_query("select codice_$rel_sing,codice2_$rel_sing,codice3_$rel_sing from $tablerel where nome_$rel_sing = '".aggslashdb($rel)."' ");
else $cod = esegui_query("select codice_$rel_sing,codice2_$rel_sing,codice3_$rel_sing from $tablerel where (nome_$rel_sing = '".aggslashdb($rel)."' or nome_$rel_sing = '".aggslashdb($rel_decod)."') ");
if (numlin_query($cod)) {
$cod2 = risul_query($cod,0,"codice2_$rel_sing");
$cod3 = risul_query($cod,0,"codice3_$rel_sing");
$cod = risul_query($cod,0,"codice_$rel_sing");
} # fine if (numlin_query($cod))
else {
$cod = "";
$cod2 = "";
$cod3 = "";
} # fine else if (numlin_query($cod))
} # fine else if (numlin_query($cod))
$rel_esist['e'][$rel_sing][$rel] = 1;
$rel_esist[$rel_sing][$rel][1] = $cod;
$rel_esist[$rel_sing][$rel][2] = $cod2;
$rel_esist[$rel_sing][$rel][3] = $cod3;
if ($rel_decod and empty($rel_esist['e'][$rel_sing][$rel_decod])) {
$rel_esist['e'][$rel_sing][$rel_decod] = 1;
$rel_esist[$rel_sing][$rel_decod][1] = $cod;
$rel_esist[$rel_sing][$rel_decod][2] = $cod2;
$rel_esist[$rel_sing][$rel_decod][3] = $cod3;
} # fine if ($rel_decod and empty($rel_esist['e'][$rel_sing][$rel_decod]))
} # fine if (empty($rel_esist['e'][$rel_sing][$rel]))
else {
$cod = $rel_esist[$rel_sing][$rel][1];
$cod2 = $rel_esist[$rel_sing][$rel][2];
$cod3 = $rel_esist[$rel_sing][$rel][3];
} # fine else if (empty($rel_esist['e'][$rel_sing][$rel]))
} # fine if (strcmp((string) $rel,""))
return $cod;
} # fine function trova_codice_rel




unset($cache_date_contr);
function formatta_data_contr ($data,$stile_data) {
global $cache_date_contr;
$data = (string) $data;
if (empty($cache_date_contr[$data])) $cache_date_contr[$data] = formatta_data($data,$stile_data);
return $cache_date_contr[$data];
} # fine function formatta_data_contr




function formatta_dir_salva_doc ($dir_salva) {
if ($dir_salva == "~") {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
if (defined('C_CARTELLA_DOC') and C_CARTELLA_DOC != "" and @is_dir(C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC)) $dir_salva = C_CARTELLA_DOC;
else $dir_salva = "";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else {
if (defined('C_DATI_PATH')) $dir_salva = C_DATI_PATH;
else $dir_salva = "";
} # fine else if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
} # fine if ($dir_salva == "~")
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $dir_salva = C_CARTELLA_CREA_MODELLI."/".str_replace("..","",$dir_salva);
if (strcmp((string) $dir_salva,"") and !@is_dir($dir_salva)) $dir_salva = "";
return $dir_salva;
} # fine function formatta_dir_salva_doc




function trova_nomi_contratti (&$max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag) {
$nomi_contratti = array();
$max_contr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
global $lingua_mex;
if (empty($lingua_mex)) for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) $nomi_contratti[$num1] = "documento$num1";
else for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) $nomi_contratti[$num1] = mex("documento",$pag)."$num1";
$nomi_contr = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$id_utente'");
$nomi_contr = (string) risul_query($nomi_contr,0,'valpersonalizza');
$nomi_contr = explode("#@&",$nomi_contr);
$num_nomi_contr = count($nomi_contr);
for ($num1 = 0 ; $num1 < $num_nomi_contr ; $num1++) {
$dati_nome_contr = explode("#?&",$nomi_contr[$num1]);
$nome_contr = str_replace("..","",str_replace("/","_",str_replace("\\","_",fixstr($dati_nome_contr[1]))));
if (strcmp(trim($nome_contr),"")) {
$nomi_contratti[$dati_nome_contr[0]] = $nome_contr;
$nomi_contratti['pers'][$dati_nome_contr[0]] = $nome_contr;
} # fine if (strcmp(trim($nome_contr),""))
} # fine for $num1
for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) {
$nomefile_esistente = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'nomefile' ");
if (numlin_query($nomefile_esistente)) $nomi_contratti['salv'][$num1] = risul_query($nomefile_esistente,0,'testo');
else $nomi_contratti['salv'][$num1] = $nomi_contratti[$num1];
$nomi_contratti['salv'][$num1] = str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nomi_contratti['salv'][$num1])));
$dir_salva = esegui_query("select * from $tablecontratti where numero = '$num1' and tipo = 'dir'");
if (numlin_query($dir_salva)) $nomi_contratti['dir'][$num1] = formatta_dir_salva_doc(risul_query($dir_salva,0,'testo'));
} # fine for $num1
for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) {
if (!empty($nomi_contratti['dir'][$num1])) {
$num_prog = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'num_prog' ");
if (numlin_query($num_prog)) {
$num_prog = risul_query($num_prog,0,'testo');
if ($nomi_contratti['dir'][$num_prog]) {
$nomi_contr_np[$nomi_contratti['dir'][$num1]."/".$nomi_contratti['salv'][$num1]][$nomi_contratti['dir'][$num_prog]."/".$nomi_contratti['salv'][$num_prog]] = 1;
$nomi_contr_np[$nomi_contratti['dir'][$num_prog]."/".$nomi_contratti['salv'][$num_prog]][$nomi_contratti['dir'][$num1]."/".$nomi_contratti['salv'][$num1]] = 1;
} # fine if($nomi_contratti['dir'][$num_prog])
} # fine if (numlin_query($num_prog))
$compress = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'compress' ");
if (numlin_query($compress)) $nomi_contratti['compress'][$num1] = risul_query($compress,0,'testo');
$dati_download = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'datdownl'");
if (numlin_query($dati_download)) {
$nomi_contratti['datdownl'][$num1] = (string) risul_query($dati_download,0,'testo');
$dati_download = explode(">",$nomi_contratti['datdownl'][$num1]);
if (!empty($dati_download[0])) $nomi_contratti['suff'][$num1] = $dati_download[0];
} # fine if (numlin_query($dati_download))
else $nomi_contratti['datdownl'][$num1] = "";
} # fine if (!empty($nomi_contratti['dir'][$num1]))
} # fine for $num1
if (!empty($nomi_contr_np)) {
$num_contr_np = 0;
foreach ($nomi_contr_np as $contr => $arr_contr) {
$key_contr_np[$num_contr_np] = $contr;
$num_contr_np++;
} # fine foreach ($nomi_contr_np as $contr => $arr_contr)
for ($num1 = 0 ; $num1 < $num_contr_np ; $num1++) {
$arr_contr = $nomi_contr_np[$key_contr_np[$num1]];
$arr_contr2 = $arr_contr;
reset($arr_contr);
foreach ($arr_contr as $contr2 => $val_contr2) {
reset($arr_contr2);
foreach ($arr_contr2 as $altro_contr2 => $val_altro) {
if ($contr2 != $altro_contr2) {
$nomi_contr_np[$contr2][$altro_contr2] = 1;
$nomi_contr_np[$altro_contr2][$contr2] = 1;
} # fine if ($contr2 != $altro_contr2)
} # fine foreach ($arr_contr2 as $altro_contr2 => $val_altro)
} # fine foreach ($arr_contr as $contr2 => $val_contr2)
} # fine for $num1
$nomi_contratti['num_prog'] = $nomi_contr_np;
} # fine if (!empty($nomi_contr_np))

return $nomi_contratti;

} # fine function trova_nomi_contratti




function bottone_submit_contr ($val,$id="",$name="",$class="") {
global $origine;
if ($id) $id = " id=\"$id\"";
if ($name) $name = " name=\"$name\"";
if ($class) $class = " class=\"$class\"";
if (substr($origine,0,17) == "punto_vendita.php") $risul = "<button class=\"pos\"$id type=\"submit\"$name value=\"$val\" style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">$val</button>";
else $risul = "<button $class$id type=\"submit\"$name value=\"$val\"><div>$val</div></button>";
return $risul;
} # fine function bottone_submit_contr




function calcola_tasse_contr ($prezzo,$perc_tasse,$arrotond_tasse,&$tasse,&$tasse_p,&$resto_tasse,&$resto_tasse_p,$stile_soldi) {
$prezzo = (float) $prezzo;
if (!$perc_tasse) $tasse = 0;
else {
if ($perc_tasse == -1) $tasse = $prezzo;
else {
$perc_tasse = (float) $perc_tasse;
$arrotond_tasse = (float) $arrotond_tasse;
$tasse = ($prezzo / ($perc_tasse + 100)) * $perc_tasse;
$tasse = $tasse / $arrotond_tasse;
$tasse = round($tasse);
$tasse = $tasse * $arrotond_tasse;
} # fine else if ($perc_tasse == -1)
} # fine else if (!$perc_tasse)
$resto_tasse = $prezzo - $tasse;
$tasse_p = punti_in_num($tasse,$stile_soldi);
$resto_tasse_p = punti_in_num($resto_tasse,$stile_soldi);
} # fine function calcola_tasse_contr




function trasforma_id_in_date ($stringa_date,&$date_id,$tableperiodi) {

if ($stringa_date and $stringa_date != "," and strstr((string) $stringa_date,",")) {
$stringa_date_vett = explode(",",$stringa_date);
$n_date = count($stringa_date_vett);
for ($num1 = 0 ; $num1 < $n_date ; $num1++) {
if ($stringa_date_vett[$num1]) {
if (!isset($date_id[$stringa_date_vett[$num1]]) and controlla_num_pos($stringa_date_vett[$num1]) == "SI") {
$data = esegui_query("select datainizio from $tableperiodi where idperiodi = '".$stringa_date_vett[$num1]."' ");
if (numlin_query($data)) $date_id[$stringa_date_vett[$num1]] = risul_query($data,0,'datainizio');
} # fine if (!isset($date_id[$stringa_date_vett[$num1]]) and controlla_num_pos($stringa_date_vett[$num1]) == "SI")
if (isset($date_id[$stringa_date_vett[$num1]])) $stringa_date_vett[$num1] = $date_id[$stringa_date_vett[$num1]];
} # fine if ($stringa_date_vett[$num1])
} # fine for $num1
$stringa_date = implode(",",$stringa_date_vett);
} # fine if ($stringa_date and $stringa_date != "," and...

return $stringa_date;

} # fine function trasforma_id_in_date




$contratti_prenota = "";
function trova_contratti_prenota (&$nomi_contratti,$anno,$anno_corrente,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag,$attiva_contratti_consentiti,$contratti_consentiti_vett) {

$contratti_prenota = array();
$contratti_visti = array();
if ($anno_corrente == "global") global $anno_corrente;
if (!isset($nomi_contratti) or !is_array($nomi_contratti)) $nomi_contratti = trova_nomi_contratti($max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag);
if (isset($nomi_contratti['dir']) and is_array($nomi_contratti['dir'])) {
reset($nomi_contratti['dir']);
foreach ($nomi_contratti['dir'] as $num_contr => $dir_salva) {
if (strcmp((string) $dir_salva,"") and ($attiva_contratti_consentiti == "n" or (isset($contratti_consentiti_vett[$num_contr]) and $contratti_consentiti_vett[$num_contr] == "SI"))) {
$nome_contr = $nomi_contratti['salv'][$num_contr];
if (empty($contratti_visti[$dir_salva."/".$nome_contr])) {
$contratti_visti[$dir_salva."/".$nome_contr] = 1;
$contr_dir = opendir($dir_salva."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva."/".$contr_corr)) {
if (substr($contr_corr,0,(strlen($nome_contr) + 1)) == $nome_contr."_") {
$contr_corr_orig = $contr_corr;
if (substr($contr_corr,-3) == ".gz") $contr_corr = substr($contr_corr,0,-3);
$suff_file_corr = "";
if (substr($contr_corr,-4) == ".rtf") $suff_file_corr = "rtf";
if (substr($contr_corr,-5) == ".html") $suff_file_corr = "html";
if (substr($contr_corr,-4) == ".txt") $suff_file_corr = "txt";
if ($suff_file_corr) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contr));
if (substr($resto_nome_contr,0,1) != "_" or substr($resto_nome_contr,5,1) != "_") $anno_doc = "";
else $anno_doc = substr($resto_nome_contr,1,4);
if ($anno_doc == $anno or strstr($resto_nome_contr,"$anno+") or ($anno_doc == $anno_corrente and $anno_corrente == ($anno + 1) and !@is_file(C_DATI_PATH."/selectperiodi$anno_corrente.1.php"))) {
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+|\+[0-9]+(\+[0-9]+)?)?)*\.$suff_file_corr/","",$resto_nome_contr) == "") {
$n_contr_corr = explode("_",$resto_nome_contr);
$n_contr_corr = $n_contr_corr[2];
$num_pren_esist = substr($resto_nome_contr,(7 + strlen($n_contr_corr)));
$num_pren_esist = substr($num_pren_esist,0,(-1 * (strlen($suff_file_corr) + 1)));
if (substr($num_pren_esist,0,3) == "1+1") {
$num_pren_esist = substr($num_pren_esist,4);
$file_dat_ext = 1;
} # fine if (substr($num_pren_esist,0,3) == "1+1")
else $file_dat_ext = 0;
$nome_downl = "";
if ($file_dat_ext and is_file($dir_salva."/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".dat")) {
$file_dat = file($dir_salva."/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".dat");
for ($num1 = 0 ; $num1 < count($file_dat) ; $num1++) {
if (!$num_pren_esist and substr($file_dat[$num1],0,3) == "rn:") $num_pren_esist = trim(substr($file_dat[$num1],3));
if (substr($file_dat[$num1],0,3) == "dn:") $nome_downl = "/".trim(substr($file_dat[$num1],3));
} # fine for $num1
if ($nome_downl) {
if (!empty($nomi_contratti['suff'][$num_contr])) $nome_downl .= ".".$nomi_contratti['suff'][$num_contr];
else $nome_downl .= ".$suff_file_corr";
} # fine if ($nome_downl)
} # fine if ($file_dat_ext and is_file($dir_salva."/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".dat"))
elseif (!empty($nomi_contratti['suff'][$num_contr])) $nome_downl = "/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".".$nomi_contratti['suff'][$num_contr];
if ($num_pren_esist) {
$num_pren_esist = explode("_",$num_pren_esist);
for ($num1 = 0 ; $num1 < count($num_pren_esist) ; $num1++) {
$anno_pren = $anno_doc;
if ($anno_doc != $anno and $anno_doc == $anno_corrente and $anno_corrente == ($anno + 1) and !@is_file(C_DATI_PATH."/selectperiodi$anno_corrente.1.php")) $anno_pren = $anno;
if (strstr($num_pren_esist[$num1],"+")) {
$num_pren_esist2 = explode("+",$num_pren_esist[$num1]);
if (($num_pren_esist2[0] + 1) == $anno and !empty($num_pren_esist2[2])) $num_pren_esist[$num1] = $num_pren_esist2[2];
else {
if ($num_pren_esist2[0] == $anno) {
$num_pren_esist[$num1] = $num_pren_esist2[1];
$anno_pren = $anno;
} # fine if ($num_pren_esist2[0] == $anno)
else $num_pren_esist[$num1] = "";
} # fine else if (($num_pren_esist2[0] + 1) == $anno_corrente and $num_pren_esist2[2])
} # fine if (strstr($num_pren_esist[$num1],"+"))
if ($num_pren_esist[$num1]) {
$num_pren_esist2 = explode("-",$num_pren_esist[$num1]);
$fine_for = $num_pren_esist2[(count($num_pren_esist2) - 1)];
$data_creazione = date("Y-m-d",filectime($dir_salva."/".$contr_corr_orig) + (C_DIFF_ORE * 3600));
for ($num2 = $num_pren_esist2[0] ; $num2 <= $fine_for ; $num2++) $contratti_prenota[$anno_pren][$num2][$num_contr."-".$n_contr_corr] = $data_creazione.$contr_corr_orig.$nome_downl;
} # fine if ($num_pren_esist[$num1]) 
} # fine for $num1
} # fine if ($num_pren_esist)
} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+|\+[0-9]+(\+[0-9]+)?)?)*\.$suff_file_corr/","",$resto_nome_contr) == "")
} # fine if ($anno_doc == $anno or strstr($resto_nome_contr,"$anno+") or (($anno_doc == $anno_corrente or...
} # fine if ($suff_file_corr)
} # fine if (substr($contr_corr,0,(strlen($nome_contr) + 1)) == $nome_contr."_")
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($fattura_corr = readdir($fatture_dir))
closedir($contr_dir);
} # fine if (empty($contratti_visti[$dir_salva."/".$nome_contr]))
} # fine if (strcmp((string) $dir_salva,"") and ($attiva_contratti_consentiti == "n" or...
} # fine foreach ($nomi_contratti['dir'] as $num_contr => $dir_salva)
} # fine if (isset($nomi_contratti['dir']) and is_array($nomi_contratti['dir']))

return $contratti_prenota;

} # fine function trova_contratti_prenota




function conv_ascii ($val,$extended="") {

$val = html_decod($val);
if (!$extended) {
$val = str_replace("ñ","n",$val);
$val = str_replace("à","a",$val);
$val = str_replace("è","e",$val);
$val = str_replace("ì","i",$val);
$val = str_replace("ò","o",$val);
$val = str_replace("ù","u",$val);
$val = str_replace("á","a",$val);
$val = str_replace("é","e",$val);
$val = str_replace("í","i",$val);
$val = str_replace("ó","o",$val);
$val = str_replace("ú","u",$val);
$val = str_replace("ä","a",$val);
$val = str_replace("ö","o",$val);
$val = str_replace("ü","u",$val);
$val = str_replace("ß","ss",$val);
$val = str_replace("ç","c",$val);
$val = str_replace("ã","a",$val);
$val = str_replace("õ","o",$val);
$val = str_replace("Ñ","N",$val);
$val = str_replace("À","A",$val);
$val = str_replace("È","E",$val);
$val = str_replace("Ì","I",$val);
$val = str_replace("Ò","O",$val);
$val = str_replace("Ù","U",$val);
$val = str_replace("Á","A",$val);
$val = str_replace("É","E",$val);
$val = str_replace("Í","I",$val);
$val = str_replace("Ó","O",$val);
$val = str_replace("Ú","U",$val);
$val = str_replace("Ä","A",$val);
$val = str_replace("Ö","O",$val);
$val = str_replace("Ü","U",$val);
$val = str_replace("Ç","C",$val);
$val = str_replace("Ã","A",$val);
$val = str_replace("Õ","O",$val);
$val = str_replace("ø","oe",$val);
$val = str_replace("Ø","OE",$val);
$val = str_replace("°","o",$val);
$val = str_replace("ý","y",$val);
$val = str_replace("Ý","Y",$val);
} # fine if (!$extended)
$val = str_replace("€","E",$val);
$val = str_replace("Α","A",$val);
$val = str_replace("α","a",$val);
$val = str_replace("Β","B",$val);
$val = str_replace("β","b",$val);
$val = str_replace("Γ","G",$val);
$val = str_replace("γ","g",$val);
$val = str_replace("Δ","D",$val);
$val = str_replace("δ","d",$val);
$val = str_replace("Ε","E",$val);
$val = str_replace("ε","e",$val);
$val = str_replace("Ζ","Z",$val);
$val = str_replace("ζ","z",$val);
$val = str_replace("Η","H",$val);
$val = str_replace("η","n",$val);
$val = str_replace("Θ","O",$val);
$val = str_replace("θ","O",$val);
$val = str_replace("Ι","I",$val);
$val = str_replace("ι","i",$val);
$val = str_replace("Κ","K",$val);
$val = str_replace("κ","k",$val);
$val = str_replace("Λ","L",$val);
$val = str_replace("λ","l",$val);
$val = str_replace("Μ","M",$val);
$val = str_replace("μ","m",$val);
$val = str_replace("Ν","N",$val);
$val = str_replace("ν","v",$val);
$val = str_replace("Ξ","E",$val);
$val = str_replace("ξ","e",$val);
$val = str_replace("Ο","O",$val);
$val = str_replace("ο","o",$val);
$val = str_replace("Π","N",$val);
$val = str_replace("π","p",$val);
$val = str_replace("Ρ","P",$val);
$val = str_replace("ρ","p",$val);
$val = str_replace("Σ","S",$val);
$val = str_replace("σ","s",$val);
$val = str_replace("ς","c",$val);
$val = str_replace("Τ","T",$val);
$val = str_replace("τ","t",$val);
$val = str_replace("Υ","Y",$val);
$val = str_replace("υ","v",$val);
$val = str_replace("Φ","F",$val);
$val = str_replace("φ","f",$val);
$val = str_replace("Χ","x",$val);
$val = str_replace("χ","X",$val);
$val = str_replace("Ψ","P",$val);
$val = str_replace("ψ","p",$val);
$val = str_replace("Ω","O",$val);
$val = str_replace("ω","o",$val);
$val = str_replace("Ά","A",$val);
$val = str_replace("ά","a",$val);
$val = str_replace("Ό","O",$val);
$val = str_replace("ό","o",$val);
$val = str_replace("Ή","H",$val);
$val = str_replace("ή","n",$val);
$val = str_replace("Ί","I",$val);
$val = str_replace("ί","i",$val);
$val = str_replace("Ύ","Y",$val);
$val = str_replace("ύ","v",$val);
$val = str_replace("Ώ","O",$val);
$val = str_replace("ώ","o",$val);
$val = str_replace("Έ","E",$val);
$val = str_replace("έ","e",$val);
$val = str_replace("ž","z",$val);
$val = str_replace("ř","r",$val);
$val = str_replace("č","c",$val);
$val = str_replace("š","s",$val);
$val = str_replace("ě","e",$val);
$val = str_replace("ů","u",$val);
$val = str_replace("Ž","Z",$val);
$val = str_replace("Ř","R",$val);
$val = str_replace("Č","C",$val);
$val = str_replace("Š","S",$val);
$val = str_replace("Ě","E",$val);
$val = str_replace("Ů","U",$val);
$val = str_replace("æ","ae",$val);
$val = str_replace("Æ","AE",$val);
$val = str_replace("œ","oe",$val);
$val = str_replace("Œ","OE",$val);
$val = str_replace("þ","th",$val);
$val = str_replace("Þ","TH",$val);
$val = str_replace("ł","l",$val);
$val = str_replace("Ł","L",$val);

if (function_exists('mb_convert_encoding')) {
if (function_exists('mb_substitute_character')) mb_substitute_character("none");
$val = mb_convert_encoding($val,"UTF-8","UTF-8");
} # fine if (function_exists('mb_convert_encoding'))
if (!$extended) $val = iconv("UTF-8","ASCII//TRANSLIT",$val);
else $val = iconv("UTF-8","CP437//TRANSLIT",$val);

return $val;

} # fine function conv_ascii




function crea_contratto ($numero_contratto,&$tipo_contratto,$id_utente,$id_sessione,$origine,$origine_vecchia,$trad_var_vett=array(),$set_glob="") {

global $var_predef,$num_var_predef,$num_var_predef_ripeti,$num_ripeti,$tariffa_selezionata,$num_costo_agg_sel,$anno,$pag,$lingua_mex,$LIKE,$ILIKE,$modifica_pers,$vedi_clienti,$dir_salva,$nome_file_contr;
global $utenti_gruppi,$nomi_contratti,$priv_vedi_beni_inv,$priv_vedi_inv_app,$priv_vedi_tab_mesi,$priv_vedi_tab_appartamenti;
global $tablecontratti,$tableclienti,$tablerclientiprenota,$tablepersonalizza,$tableutenti,$tablerelutenti,$tablenazioni,$tableregioni,$tablecitta,$tabledocumentiid,$tableparentele,$tableappartamenti,$tableperiodi,$tablebeniinventario,$tablerelinventario,$tableregole,$tablenometariffe;
global $data_inizio_selezione,$data_fine_selezione,$var_riserv,$var_predef_data,$messaggio_di_errore,$testo_email_richiesta,$num_commenti_pers,$numero_ins_comm_pers,$num_campi_pers_cliente,$numero_inserimento_pers,$numero_ins_cat_pers,$numero_inserimento_alleg,$dati_cat_pers,$dati_allegato;

$contratto = "";
$n_utenti = esegui_query("select idutenti,nome_utente from $tableutenti ");
for ($num1 = 0 ; $num1 < numlin_query($n_utenti) ; $num1++) $n_utente_contr[risul_query($n_utenti,$num1,'idutenti')] = risul_query($n_utenti,$num1,'nome_utente');
if (!defined('C_ID_UTENTE_CONTR')) define('C_ID_UTENTE_CONTR',$id_utente);
unset($id_utente);

$dati_contratto = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo $LIKE 'contr%'");
if (numlin_query($dati_contratto)) {

$tipo_contratto = risul_query($dati_contratto,0,'tipo');
$contratto_orig = (string) risul_query($dati_contratto,0,'testo');
chiudi_query($dati_contratto);

if (!$set_glob and $num_ripeti > 40) $unset_glob = 1;
if (!empty($data_inizio_selezione) and preg_replace("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/","",$data_inizio_selezione) != "") $data_inizio_selezione = "";
if (!empty($data_fine_selezione) and preg_replace("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/","",$data_fine_selezione) != "") $data_fine_selezione = "";
$data_inizio_selezione_orig = $data_inizio_selezione;
$data_fine_selezione_orig = $data_fine_selezione;
$numero_prenotazioni_selezione = $num_ripeti;
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {
for ($num1 = 0 ; $num1 < $num_var_predef_ripeti ; $num1++) {
if (isset($GLOBALS[$var_predef[$num1]."_".$n_r]) and strcmp((string) $GLOBALS[$var_predef[$num1]."_".$n_r],"")) {
if ($tipo_contratto == "contrhtm") ${$var_predef[$num1]."_".$n_r} = htmlspecialchars($GLOBALS[$var_predef[$num1]."_".$n_r],ENT_COMPAT);
else ${$var_predef[$num1]."_".$n_r} = $GLOBALS[$var_predef[$num1]."_".$n_r];
} # fine if (isset($GLOBALS[$var_predef[$num1]."_".$n_r]) and...
if (isset($unset_glob)) unset($GLOBALS[$var_predef[$num1]."_".$n_r]);
} # fine for $num1
if (isset(${"data_inizio_".$n_r}) and (empty($data_primo_arrivo) or ${"data_inizio_".$n_r} < $data_primo_arrivo)) $data_primo_arrivo = ${"data_inizio_".$n_r};
if (isset(${"data_fine_".$n_r}) and (empty($data_ultima_partenza) or ${"data_fine_".$n_r} > $data_ultima_partenza)) $data_ultima_partenza = ${"data_fine_".$n_r};
if (isset(${"utente_inserimento_prenotazione"."_".$n_r}) and isset($n_utente_contr[${"utente_inserimento_prenotazione"."_".$n_r}])) ${"utente_inserimento_prenotazione"."_".$n_r} = $n_utente_contr[${"utente_inserimento_prenotazione"."_".$n_r}];
else ${"utente_inserimento_prenotazione"."_".$n_r} = null;
} # fine for $n_r
if (empty($data_inizio_selezione_orig)) $data_inizio_selezione = fixset($data_primo_arrivo);
if (empty($data_fine_selezione_orig)) $data_fine_selezione = fixset($data_ultima_partenza);
$messaggio_di_errore = "";
$nome_documento_scaricato = "";


$formati_contr = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'formati'");
if (numlin_query($formati_contr)) {
$formati_contr = explode(">",risul_query($formati_contr,0,'testo'));
$stile_soldi = $formati_contr[0];
$stile_data = $formati_contr[1];
} # fine if (numlin_query($formati))
else {
$stile_soldi = "";
$stile_data = "";
} # fine else if (numlin_query($formati))
if (!$stile_soldi) $stile_soldi = stile_soldi(C_ID_UTENTE_CONTR);
if (!$stile_data) $stile_data = stile_data(C_ID_UTENTE_CONTR);

$data_inizio_selezione_orig = $data_inizio_selezione;
$data_fine_selezione_orig = $data_fine_selezione;
if ($data_inizio_selezione) $data_inizio_selezione_f = formatta_data_contr($data_inizio_selezione,$stile_data);
else $data_inizio_selezione_f = null;
if ($data_fine_selezione) $data_fine_selezione_f = formatta_data_contr($data_fine_selezione,$stile_data);
else $data_fine_selezione_f = null;
$ritorno_a_capo = "\r";
$avanzamento_riga = "\n";
$valore_nullo = "";
$nome_valuta = nome_valuta(C_ID_UTENTE_CONTR);
$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$oggi_orig = $oggi;
$oggi_f = formatta_data_contr($oggi,$stile_data);
if ($lingua_mex) $fr_via = mex("via",$pag);
if ($testo_email_richiesta) {
if (@get_magic_quotes_gpc()) $testo_email_richiesta = stripslashes($testo_email_richiesta);
$testo_quotato_email_richiesta_orig = "> ".str_replace("\n","\n> ",$testo_email_richiesta);
if ($lingua_mex) $testo_quotato_email_richiesta_orig = "<email_richiesta> ".mex("ha scritto",$pag).":\n".$testo_quotato_email_richiesta_orig;
} # fine if ($testo_email_richiesta)
else $testo_quotato_email_richiesta_orig = "";
$arrotond_tasse = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_tasse' and idutente = '".C_ID_UTENTE_CONTR."'");
$arrotond_tasse = risul_query($arrotond_tasse,0,'valpersonalizza');
$nome_struttura = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'dati_struttura' and idutente = '".C_ID_UTENTE_CONTR."'");
$nome_struttura = explode("#@&",risul_query($nome_struttura,0,'valpersonalizza'));
$tipo_struttura = $nome_struttura[1];
$email_struttura = $nome_struttura[2];
$ragione_sociale_struttura = $nome_struttura[3];
$sito_web_struttura = $nome_struttura[4];
$nome_contatto_struttura = $nome_struttura[5];
$nazione_struttura = $nome_struttura[6];
$comune_struttura = $nome_struttura[7];
$indirizzo_struttura = $nome_struttura[8];
$CAP_struttura = $nome_struttura[9];
$telefono_struttura = $nome_struttura[10];
$fax_struttura = $nome_struttura[11];
$codice_fiscale_struttura = fixset($nome_struttura[12]);
$partita_iva_struttura = fixset($nome_struttura[13]);
$numero_stelle_struttura = fixset($nome_struttura[14]);
$logo_struttura = fixset($nome_struttura[15]);
$regione_struttura = fixset($nome_struttura[16]);
$nome_struttura = $nome_struttura[0];
$codice_nazione_struttura = trova_codice_rel($nazione_struttura,$rel_esist,"nazione","nazioni",$codice2_nazione_struttura,$codice3_nazione_struttura,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_struttura = trova_codice_rel($regione_struttura,$rel_esist,"regione","regioni",$codice2_regione_struttura,$codice3_regione_struttura,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_comune_struttura = trova_codice_rel($comune_struttura,$rel_esist,"citta","citta",$codice2_citta_struttura,$codice3_citta_struttura,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);

$url_base_pagine_web = "";
if (defined('C_PAGINA_WEB') and C_PAGINA_WEB == '1') {
global $PHP_SELF,$SERVER_NAME,$HTTP_SERVER_VARS,$HTTPS,$SERVER_PORT;
if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or @$HTTP_SERVER_VARS['SERVER_NAME']) {
if (@$PHP_SELF or @$_SERVER['PHP_SELF']) {
if ($_SERVER['SERVER_NAME']) $SERVER_NAME = $_SERVER['SERVER_NAME'];
elseif ($HTTP_SERVER_VARS['SERVER_NAME']) $SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
if ($_SERVER['PHP_SELF']) $PHP_SELF = $_SERVER['PHP_SELF'];
if ((isset($HTTPS) and $HTTPS == "on") or (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == "on") or (isset($SERVER_PORT) and $SERVER_PORT == "443") or (isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == "443")) $url_base_pagine_web = "https://".$SERVER_NAME;
else $url_base_pagine_web = "http://".$SERVER_NAME;
if (substr($PHP_SELF,0,1) != "/") $url_base_pagine_web .= "/".$PHP_SELF;
else $url_base_pagine_web .= $PHP_SELF;
$val_then = explode("/",$url_base_pagine_web);
$url_base_pagine_web = substr($url_base_pagine_web,0,(strlen($val_then[(count($val_then) - 1)]) * -1));
$url_base_pagine_web = str_replace("/./","/",$url_base_pagine_web);
while (str_replace("/../","",$url_base_pagine_web) != $url_base_pagine_web) {
$val_then = explode("/../",$url_base_pagine_web);
$val_if = explode("/",$val_then[0]);
$txt_sost1 = substr($val_then[0],0,(strlen($val_if[(count($val_if) - 1)]) * -1));
$url_base_pagine_web = $txt_sost1.substr($url_base_pagine_web,(strlen($val_then[0]) + 4));
} # fine while (str_replace("/../","",$url_base_pagine_web) != $url_base_pagine_web)
} # fine if (@$PHP_SELF or @$_SERVER['PHP_SELF'])
} # fine if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or...
} # fine if (defined('C_PAGINA_WEB') and C_PAGINA_WEB == '1')
if (!$url_base_pagine_web) {
if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI")) {
$percorso_cartella_modello = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
$percorso_cartella_modello = explode(",",risul_query($percorso_cartella_modello,0,'valpersonalizza'));
$percorso_cartella_modello = $percorso_cartella_modello[0];
$altri_domini = @file(C_FILE_DOMINIO);
if ($altri_domini) {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
if (substr($percorso_cartella_modello,0,strlen(C_CARTELLA_CREA_MODELLI)) != C_CARTELLA_CREA_MODELLI) $percorso_cartella_modello = "./";
else $percorso_cartella_modello = substr($percorso_cartella_modello,strlen(C_CARTELLA_CREA_MODELLI));
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
if (substr($percorso_cartella_modello,0,1) == "/") $percorso_cartella_modello = substr($percorso_cartella_modello,1);
if (strcmp((string) $percorso_cartella_modello,"") and substr($percorso_cartella_modello,-1) != "/") $percorso_cartella_modello .= "/";
if (substr($percorso_cartella_modello,0,2) == "./") $percorso_cartella_modello = substr($percorso_cartella_modello,2);
$url_base_pagine_web = "https://".trim($altri_domini[0])."/$percorso_cartella_modello";
} # fine if ($altri_domini)
unset($altri_domini);
} # fine if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI"))
} # fine if (!$url_base_pagine_web)
if (!$url_base_pagine_web and (!defined('C_PAGINA_WEB') or C_PAGINA_WEB != '1')) {
if (!function_exists('trova_url_pagina')) {
$perc_cart_mod_sel = "";
include("./includes/templates/funzioni_modelli.php");
} # fine if (!function_exists('trova_url_pagina'))
$url_base_pagine_web = trova_url_pagina("",$percorso_cartella_modello,"");
} # fine if (!$url_base_pagine_web and (!defined('C_PAGINA_WEB') or C_PAGINA_WEB != '1'))
unset($percorso_cartella_modello);

$num_contr_vc = $numero_contratto;
$contr_imp_vc = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'impor_vc' ");
if (numlin_query($contr_imp_vc)) $num_contr_vc = risul_query($contr_imp_vc,0,'testo');
$variabili = esegui_query("select * from $tablecontratti where tipo = 'var' or tipo = 'var$num_contr_vc' order by tipo, numero");
$num_variabili = numlin_query($variabili);
$arrays = esegui_query("select * from $tablecontratti where tipo = 'vett' or tipo = 'vett$num_contr_vc' order by tipo, numero");
$num_arrays = numlin_query($arrays);
$condizioni_ini_d = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'ind%' order by tipo, numero");
$num_condizioni_ini_d = numlin_query($condizioni_ini_d);
$condizioni_ini_r = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'inr%' order by tipo, numero");
$num_condizioni_ini_r = numlin_query($condizioni_ini_r);
$condizioni = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rpt%' order by tipo, numero");
$num_condizioni = numlin_query($condizioni);
$condizioni_rip_o = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'ros%' order by tipo, numero");
$num_condizioni_rip_o = numlin_query($condizioni_rip_o);
$condizioni_rip_c = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rca%' order by tipo, numero");
$num_condizioni_rip_c = numlin_query($condizioni_rip_c);
$condizioni_rip_p = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rpa%' order by tipo, numero");
$num_condizioni_rip_p = numlin_query($condizioni_rip_p);
$condizioni_rip_d = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rds%' order by tipo, numero");
$num_condizioni_rip_d = numlin_query($condizioni_rip_d);
$condizioni_rip_u = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'run%' order by tipo, numero");
$num_condizioni_rip_u = numlin_query($condizioni_rip_u);
$condizioni_rip_a = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rar%' order by tipo, numero");
$num_condizioni_rip_a = numlin_query($condizioni_rip_a);
$dir_salva = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'dir'");
if (numlin_query($dir_salva) == 1) {
$dir_salva = formatta_dir_salva_doc(risul_query($dir_salva,0,'testo'));
} # fine if (numlin_query($dir_salva) == 1)
else $dir_salva = "";

$contr_multilingua = 0;
$contratti_orig_mln = array();
$lingue_contr = array();
$num_contr_mln = 1;
$num_unita = "";
if (substr($contratto_orig,0,7) == "#!mln!#") {
$contr_multilingua = 1;
$contratti_orig_mln['predef'] = substr($contratto_orig,7);
$dati_contratti_mln = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo $LIKE 'mln_%'");
$num_contr_mln = 0;
$val_then = numlin_query($dati_contratti_mln);
for ($num1 = 0 ; $num1 < $val_then ; $num1++) {
$lingua_contr = substr(risul_query($dati_contratti_mln,$num1,'tipo'),4);
$val_if = (string) risul_query($dati_contratti_mln,$num1,'testo');
if (strcmp(trim($val_if),"") or $lingua_contr == $contratti_orig_mln['predef'] or (!$num_contr_mln and $num1 == ($val_then - 1))) {
$lingue_contr[$num_contr_mln] = $lingua_contr;
$contratti_orig_mln[$lingua_contr] = $val_if;
if (strstr($val_if,'[r6]')) $num_unita = 1;
$num_contr_mln++;
} # fine if (strcmp(trim($val_if),"" or $lingua_contr == $contratti_orig_mln['predef'] or...
} # fine for $num1
unset($dati_contratti_mln);
unset($lingua_contr);
unset($val_if);
$contratto_orig = fixstr($contratti_orig_mln[$contratti_orig_mln['predef']]);
} # fine if (substr($contratto_orig,0,7) == "#!mln!#")
elseif (strstr($contratto_orig,'[r6]')) $num_unita = 1;

if ($num_unita) {
$priv_vedi_app = $priv_vedi_tab_appartamenti;
if ($priv_vedi_tab_mesi == "s") $priv_vedi_app = "s";
if ($priv_vedi_app != "s" and ($priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g")) $priv_vedi_app = "g";
if ($priv_vedi_app == "n" and ($priv_vedi_tab_mesi == "p" or $priv_vedi_tab_mesi == "q")) $priv_vedi_app = "p";
if ($priv_vedi_app == "n") $num_unita = "";
if ($num_unita) {
$dati_app = esegui_query("select * from $tableappartamenti order by idappartamenti ");
$num_app = numlin_query($dati_app);
if ($priv_vedi_app != "s") {
if (!function_exists('trova_app_consentiti')) include("./includes/funzioni_appartamenti.php");
if ($priv_vedi_app != "g") {
global $attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett;
$appartamenti_consentiti = trova_app_consentiti($dati_app,$num_app,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,C_ID_UTENTE_CONTR,$tableregole,$tablenometariffe);
unset($attiva_regole1_consentite);
unset($regole1_consentite);
unset($priv_mod_assegnazione_app);
unset($priv_mod_prenotazioni);
unset($priv_ins_assegnazione_app);
unset($priv_ins_nuove_prenota);
unset($attiva_tariffe_consentite);
unset($tariffe_consentite_vett);
} # fine if ($priv_vedi_app != "g")
else {
global $attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr;
$appartamenti_consentiti = trova_app_consentiti($dati_app,$num_app,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,C_ID_UTENTE_CONTR,$tableregole,$tablenometariffe);
unset($attiva_regole1_consentite_gr);
unset($regole1_consentite_gr);
unset($priv_mod_assegnazione_app_gr);
unset($priv_mod_prenotazioni_gr);
unset($priv_ins_assegnazione_app_gr);
unset($priv_ins_nuove_prenota_gr);
unset($attiva_tariffe_consentite_gr);
unset($tariffe_consentite_vett_gr);
} # fine else if ($priv_vedi_app != "g")
} # fine if ($priv_vedi_app != "s")
#global $dati_app_contr;
$dati_app_contr = array(0 => 0); # 0: prendi inventario
$num_unita = 0;
for ($num1 = 0 ; $num1 < $num_app ; $num1++) {
$txt_sost1 = risul_query($dati_app,$num1,'idappartamenti');
if ($priv_vedi_app == "s" or fixset($appartamenti_consentiti[$txt_sost1]) == "SI") {
$num_unita++;
$dati_app_contr[$num_unita]['nome'] = $txt_sost1;
$dati_app_contr[$num_unita]['casa'] = risul_query($dati_app,$num1,'numcasa');
$dati_app_contr[$num_unita]['piano'] = risul_query($dati_app,$num1,'numpiano');
$dati_app_contr[$num_unita]['capacita'] = risul_query($dati_app,$num1,'maxoccupanti');
$dati_app_contr[$num_unita]['priorita'] = risul_query($dati_app,$num1,'priorita');
if ($tipo_contratto == "contrhtm") {
$dati_app_contr[$num_unita]['nome'] = htmlspecialchars($dati_app_contr[$num_unita]['nome'],ENT_COMPAT);
$dati_app_contr[$num_unita]['casa'] = htmlspecialchars((string) $dati_app_contr[$num_unita]['casa'],ENT_COMPAT);
$dati_app_contr[$num_unita]['piano'] = htmlspecialchars((string) $dati_app_contr[$num_unita]['piano'],ENT_COMPAT);
} # fine if ($tipo_contratto == "contrhtm")
} # fine if ($priv_vedi_app == "s" or fixset($appartamenti_consentiti[$txt_sost1]) == "SI")
} # fine for $num1
unset($appartamenti_consentiti);
} # fine if ($num_unita)
} # fine if ($num_unita)

$tag_b = "";
$tag_no_b = "";
if ($tipo_contratto == "contrhtm") {
$tag_b = "<b>";
$tag_no_b = "</b>";
$tag_spazio = "&nbsp;";
$tag_acapo = "<br>";
} # fine if ($tipo_contratto == "contrhtm")

if ($tipo_contratto == "contreml") {
$tag_spazio = " ";
$tag_acapo = "\n";
$oggetto_email = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'oggetto'");
$oggetto_email = (string) risul_query($oggetto_email,0,'testo');
$oggetti_email_mln = array();
if ($contr_multilingua) {
$oggetto_email = explode(">",$oggetto_email);
for ($num1 = 0 ; $num1 < count($oggetto_email) ; $num1++) {
$ln_corr = explode(":",$oggetto_email[$num1]);
$oggetti_email_mln[substr($ln_corr[0],4)] = substr(strstr($oggetto_email[$num1],":"),1);
} # fine for $num1
unset($ln_corr);
if (!isset($oggetti_email_mln[$contratti_orig_mln['predef']])) $oggetti_email_mln[$contratti_orig_mln['predef']] = "";
} # fine if ($contr_multilingua)

$nome_mittente_email = $nome_contatto_struttura;
$mittente_email = $email_struttura;
if ($nome_mittente_email) $mittente_email = "$nome_mittente_email &lt;$mittente_email&gt;";
} # fine if ($tipo_contratto == "contreml")

if ($tipo_contratto == "contrrtf") {
$tag_spazio = " ";
$tag_acapo = "}
\\par \\pard\\plain \\ltrpar\\s1\\cf0{\\*\\hyphen2\\hyphlead2\\hyphtrail2\\hyphmax0}\\rtlch\\af3\\afs24\\lang255\\ltrch\\dbch\\af3\\langfe255\\hich\\f0\\fs24\\lang1040\\loch\\f0\\fs24\\lang1040 {\\rtlch \\ltrch\\loch\\f0\\fs24\\lang1040\\i0\\b0 ";
} # fine if ($tipo_contratto == "contrrtf")
if ($tipo_contratto == "contrtxt") {
$tag_spazio = " ";
$tag_acapo = "\n";
} # fine if ($tipo_contratto == "contrtxt")

if (strstr($contratto_orig,'[r7]')) {
global $contratti_prenota,$attiva_contratti_consentiti,$contratti_consentiti_vett;
if (!isset($contratti_prenota) or !is_array($contratti_prenota)) {
$contratti_prenota = trova_contratti_prenota($nomi_contratti,$anno,"global",C_ID_UTENTE_CONTR,$tablecontratti,$tablepersonalizza,$LIKE,'visualizza_contratto.php',$attiva_contratti_consentiti,$contratti_consentiti_vett);
} # fine if (!isset($contratti_prenota) or !is_array($contratti_prenota))
} # fine if (strstr($contratto_orig,'[r7]'))


if (str_replace("[r]","",$contratto_orig) == $contratto_orig or $contr_multilingua) $ripeti_tutto = 1;
else $ripeti_tutto = 0;


$nome_file_contr = array();
$nome_file_downl = array('a' => 0);
$dati_file_dat = array();
$filecontr = array();
$num_prog_contr = array();
$num_prog_contr_max = "";
$incr_np = 0;
$crea_file_dopo = 0;
if ($dir_salva) {
global $num_contr_esist,$nome_file_contr_esist,$cont_salva,$lista_var_form,$num_file_salva,$sovrascrivi,$priv_cancella_contratti;
$nome_contratto = $nomi_contratti['salv'][$numero_contratto];
if (@get_magic_quotes_gpc()) $sovrascrivi = stripslashes($sovrascrivi);
if ($priv_cancella_contratti == "n") $sovrascrivi = "";
if ($ripeti_tutto) $num_file_salva = $num_ripeti;
else $num_file_salva = 1;
$anno_corr = date("Y");
if ($anno_corr != ($anno + 1) or @is_file(C_DATI_PATH."/selectperiodi$anno_corr.1.php")) $anno_corr = $anno;
$val_if = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'incr_np'");
if (numlin_query($val_if)) $incr_np = risul_query($val_if,0,'testo');
if ($tipo_contratto != "contreml") {
if (isset($nomi_contratti['datdownl'][$numero_contratto])) $val_if = $nomi_contratti['datdownl'][$numero_contratto];
else {
$val_if = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'datdownl'");
if (numlin_query($val_if)) $val_if = (string) risul_query($val_if,0,'testo');
else $val_if = "";
} # fine else if (isset($nomi_contratti['datdownl'][$numero_contratto]))
if ($val_if) {
$nome_file_downl['datdownl'] = $val_if;
$val_if = explode(">",$nome_file_downl['datdownl']);
if (isset($val_if[1]) and $val_if[1] == 'var') $nome_file_downl['a'] = 1;
} # fine if ($val_if)
else $nome_file_downl['datdownl'] = ">";
} # fine if ($tipo_contratto != "contreml")
unset($nomi_contratti['datdownl']);
if ($incr_np or $nome_file_downl['a']) $crea_file_dopo = 1;

$lista_var_form = "";
if (@is_array($_POST)) reset($_POST);
for($num1 = 0 ; $num1 < count($_POST); $num1++) {
$lista_var_form .= "<input type=\"hidden\" name=\"".htmlspecialchars(key($_POST))."\" value=\"".htmlspecialchars(strip_magic_slashs($_POST[key($_POST)]))."\">";
next($_POST);
} # fine for $num1
if (@is_array($_GET)) reset($_GET);
for($num1 = 0 ; $num1 < count($_GET); $num1++) {
$lista_var_form .= "<input type=\"hidden\" name=\"".htmlspecialchars(key($_GET))."\" value=\"".htmlspecialchars(strip_magic_slashs($_GET[key($_GET)]))."\">";
next($_GET);
} # fine for $num1

$filelock = fopen(C_DATI_PATH."/crea_contr.lock","w+");
flock($filelock,2);
if ($tipo_contratto == "contrrtf") $suff_file = "rtf";
if ($tipo_contratto == "contrhtm") $suff_file = "html";
if ($tipo_contratto == "contrtxt") $suff_file = "txt";
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) {
global ${"id_anni_prec_".$num1};
$id_pren[$num1] = fixset(${"numero_prenotazione_".$num1});
} # fine for $num1
$num_contr_esist = 0;
$nome_file_contr_esist = array();
$n_prog_contr = 0;
$num_sovrascrivi = 0;
$num_sovrascrivi_max = 0;
$contr_dir = opendir($dir_salva."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva."/".$contr_corr)) {
if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto) {
$contr_corr_orig = $contr_corr;
if (substr($contr_corr,-3) == ".gz") $contr_corr = substr($contr_corr,0,-3);
$suff_file_corr = "";
if (substr($contr_corr,-4) == ".rtf") $suff_file_corr = "rtf";
if (substr($contr_corr,-5) == ".html") $suff_file_corr = "html";
if (substr($contr_corr,-4) == ".txt") $suff_file_corr = "txt";
if ($suff_file_corr) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contratto));
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+|\+[0-9]+(\+[0-9]+)?)?)*\.$suff_file_corr/","",$resto_nome_contr) == "") {
$anno_contr = substr($resto_nome_contr,1,4);
$n_contr_corr = explode("_",substr($resto_nome_contr,0,(-1 * (strlen($suff_file_corr) + 1))));
$n_contr_corr = $n_contr_corr[2];
$resto_nome_contr = substr($resto_nome_contr,(7 + strlen($n_contr_corr)));
if (str_replace("-","",$n_contr_corr) != $n_contr_corr) {
$n_contr_corr = explode("-",$n_contr_corr);
$n_contr_ini_corr = $n_contr_corr[0];
$n_contr_corr = $n_contr_corr[1];
} # fine if (str_replace("-","",$n_contr_corr) != $n_contr_corr)
else $n_contr_ini_corr = 0;
if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr) $n_prog_contr = $n_contr_corr;

if (!$cont_salva and $lista_var_form) {
$num_pren_esist = substr($resto_nome_contr,0,(-1 * (strlen($suff_file_corr) + 1)));
if (substr($num_pren_esist,0,3) == "1+1") {
# 4 caratteri perche considero l'eventuale _ se ci sono anche i numeri delle prenotazioni nel nome
$num_pren_esist = substr($num_pren_esist,4);
$num1 = 1;
} # fine if (substr($num_pren_esist,0,3) == "1+1")
else $num1 = 0;
if ($num1 and !$num_pren_esist and is_file($dir_salva."/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".dat")) {
$num1 = file($dir_salva."/".substr($contr_corr,0,(-1 * (strlen($suff_file_corr) + 1))).".dat");
for ($num2 = 0 ; $num2 < count($num1) ; $num2++) {
if (substr($num1[$num2],0,3) == "rn:") $num_pren_esist = trim(substr($num1[$num2],3));
} # fine for $num2
} # fine if ($num1 and !$num_pren_esist and...
if ($num_pren_esist) {
$num_pren_esist = explode("_",$num_pren_esist);
for ($num1 = 0 ; $num1 < count($num_pren_esist) ; $num1++) {
$anno_pren = $anno_contr;
if (strstr($num_pren_esist[$num1],"+")) {
$num_pren_esist2 = explode("+",$num_pren_esist[$num1]);
if ((($num_pren_esist2[0] + 1) == $anno or ($num_pren_esist2[0] + 1) == $anno_corr) and !empty($num_pren_esist2[2])) $num_pren_esist[$num1] = $num_pren_esist2[2];
else {
if ($num_pren_esist2[0] == $anno or $num_pren_esist2[0] == $anno_corr) {
$num_pren_esist[$num1] = $num_pren_esist2[1];
$anno_pren = $num_pren_esist2[0];
} # fine if ($num_pren_esist2[0] == $anno or $num_pren_esist2[0] == $anno_corr)
else $num_pren_esist[$num1] = "";
} # fine else if ((($num_pren_esist2[0] + 1) == $anno or ($num_pren_esist2[0] + 1) == $anno_corr) and...
} # fine if (strstr($num_pren_esist[$num1],"+"))
if ($num_pren_esist[$num1]) {
$num_pren_esist2 = explode("-",$num_pren_esist[$num1]);
$fine_for = $num_pren_esist2[(count($num_pren_esist2) - 1)];
for ($num2 = $num_pren_esist2[0] ; $num2 <= $fine_for ; $num2++) {
for ($num3 = 1 ; $num3 <= $num_ripeti ; $num3++) {
if (($num2 == $id_pren[$num3] and ($anno_pren == $anno or $anno_pren == $anno_corr)) or (str_replace(";$anno_contr,$num2;","",(string) ${"id_anni_prec_".$num3}) != ${"id_anni_prec_".$num3} and $anno_contr != $anno and $anno_contr != $anno_corr)) {
$num_contr_esist++;
$nome_file_contr_esist[$num_contr_esist] = $contr_corr_orig;
$nome_file_contr_esist['num'] = $num_contr_esist;
if (substr($contr_corr_orig,-3) == ".gz") {
$val_if = explode(".",substr($contr_corr_orig,0,-3));
$val_then = 4;
} # fine if (substr($contr_corr_orig,-3) == ".gz")
else {
$val_if = explode(".",$contr_corr_orig);
$val_then = 1;
} # fine else if (substr($contr_corr_orig,-3) == ".gz")
$val_if = $val_if[(count($val_if) - 1)];
$val_if = substr($contr_corr_orig,0,(-1 * (strlen($val_if) + $val_then)));
if (is_file("$dir_salva/$val_if.dat")) {
$val_then = file("$dir_salva/$val_if.dat");
for ($num1 = 0 ; $num1 < count($val_then) ; $num1++) {
if (substr($val_then[$num1],0,3) == "dn:") {
$nome_file_contr_esist['downl'][$num_contr_esist] = trim(substr($val_then[$num1],3));
$nome_file_contr_esist['downl']['num'] = $num_contr_esist;
} # fine if (substr($val_then[$num1],0,3) == "dn:")
} # fine for $num1
} # fine if (is_file("$dir_salva/$val_if.dat"))
else $val_if = 0;
if ($contr_corr_orig == $sovrascrivi and $num_file_salva == 1) {
$num_sovrascrivi_max = $n_contr_corr;
if ($n_contr_ini_corr) $num_sovrascrivi = $n_contr_ini_corr;
else $num_sovrascrivi = $n_contr_corr;
$anno_corr = $anno_contr;
unlink($dir_salva."/".$contr_corr_orig);
if ($val_if) unlink("$dir_salva/$val_if.dat");
if ($id_pren[$num3] != $num2) ${"numero_prenotazione_".$num3} = $num2;
} # fine if ($contr_corr_orig == $sovrascrivi and...
break 3;
} # fine if (($num2 == $id_prenota[$num3] and...
} # fine for $num3
} # fine for $num2
} # fine if ($num_pren_esist[$num1])
} # fine for $num1
} # fine if ($num_pren_esist)
} # fine if (!$cont_salva and $lista_var_form)

} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,5}\.$suff_file_corr/","",$resto_nome_contr) == "")
} # fine if ($suff_file_corr)
} # fine if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto)
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($contr_corr = readdir($contr_dir))
closedir($contr_dir);

if ($num_sovrascrivi) {
$num_contr_esist = 0;
if ($incr_np and $num_sovrascrivi_max < $n_prog_contr) $num_prog_contr_max = $num_sovrascrivi_max;
$n_prog_contr = ($num_sovrascrivi - 1);
} # fine if ($num_sovrascrivi)

if (!$num_sovrascrivi or ($incr_np and !$num_prog_contr_max)) {
# se il contratto condivide il numero progressivo con altri contratti
if (isset($nomi_contratti['num_prog']) and is_array($nomi_contratti['num_prog'])) {
$altri_contr_np = fixset($nomi_contratti['num_prog'][$dir_salva."/".$nome_contratto]);
if (is_array($altri_contr_np)) {
reset($altri_contr_np);
foreach ($altri_contr_np as $contr_np => $val_contr) {
$nome_contratto_np = explode("/",$contr_np);
$nome_contratto_np = $nome_contratto_np[(count($nome_contratto_np) - 1)];
$dir_salva_np = substr($contr_np,0,(-1 * (strlen($nome_contratto_np) + 1)));
$contr_dir = opendir($dir_salva_np."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva_np."/".$contr_corr)) {
if (substr($contr_corr,0,strlen($nome_contratto_np)) == $nome_contratto_np) {
if (substr($contr_corr,-3) == ".gz") $contr_corr = substr($contr_corr,0,-3);
$suff_file_corr = "";
if (substr($contr_corr,-4) == ".rtf") $suff_file_corr = "rtf";
if (substr($contr_corr,-5) == ".html") $suff_file_corr = "html";
if (substr($contr_corr,-4) == ".txt") $suff_file_corr = "txt";
if ($suff_file_corr) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contratto_np));
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+|\+[0-9]+(\+[0-9]+)?)?)*\.$suff_file_corr/","",$resto_nome_contr) == "") {
$anno_contr = substr($resto_nome_contr,1,4);
$n_contr_corr = explode("_",$resto_nome_contr);
$n_contr_corr = $n_contr_corr[2];
if (str_replace("-","",$n_contr_corr) != $n_contr_corr) {
$n_contr_corr = explode("-",$n_contr_corr);
$n_contr_corr = $n_contr_corr[1];
} # fine if (str_replace("-","",$n_contr_corr) != $n_contr_corr)
if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr) {
if (!$num_sovrascrivi) $n_prog_contr = $n_contr_corr;
else {
$num_prog_contr_max = $num_sovrascrivi_max;
break;
} # fine else if (!$num_sovrascrivi)
} # fine if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr)
} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+|\+[0-9]+(\+[0-9]+)?)?)*\.$suff_file_corr/","",$resto_nome_contr) == "")
} # fine if ($suff_file_corr)
} # fine if (substr($contr_corr,0,strlen($nome_contratto_np)) == $nome_contratto_np)
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($contr_corr = readdir($contr_dir))
closedir($contr_dir);
} # fine foreach ($altri_contr_np as $contr_np => $val_contr)
} # fine if (@is_array($altri_contr_np))
} # fine if (isset($nomi_contratti['num_prog']) and is_array($nomi_contratti['num_prog']))
} # fine if (!$num_sovrascrivi or ($incr_np and !$num_prog_contr_max))

if ($num_contr_esist) return 0;

for ($num1 = 1 ; $num1 <= $num_file_salva ; $num1++) {
$n_prog_contr++;
$num_prog_contr[$num1] = $n_prog_contr;
$nome_file_contr[$num1] = $n_prog_contr;
for ($num2 = strlen($nome_file_contr[$num1]) ; $num2 < 5 ; $num2++) $nome_file_contr[$num1] = "0".$nome_file_contr[$num1];
if ($incr_np) $nome_file_contr[$num1] .= " ";

$num3 = "";
if ($ripeti_tutto) {
if (${"numero_prenotazione_".$num1} and preg_replace("/[0-9]+/","",${"numero_prenotazione_".$num1}) == "") $num3 .= "_".${"numero_prenotazione_".$num1};
} # fine if ($ripeti_tutto)
else {
$lista_num_prenota = array();
for ($num2 = 1 ; $num2 <= $num_ripeti ; $num2++) {
if (!empty(${"numero_prenotazione_".$num1}) and preg_replace("/[0-9]+/","",${"numero_prenotazione_".$num1}) == "") $lista_num_prenota[$num2] = ${"numero_prenotazione_".$num2};
} # fine for $num2
if (!empty($lista_num_prenota)) {
asort($lista_num_prenota);
reset($lista_num_prenota);
$ultimo_num_prenota = -2;
foreach ($lista_num_prenota as $num2 => $num_prenota) {
if ($ultimo_num_prenota != $num_prenota) {
if ($ultimo_num_prenota < 0) {
$num3 .= "_".$num_prenota."_";
$ultimo_num_prenota = ($num_prenota - 1);
} # fine if ($ultimo_num_prenota < 0)
else {
if (($num_prenota - 1) != $ultimo_num_prenota) {
if (substr($num3,-1) != "_") $num3 .= "-".$ultimo_num_prenota."_";
$num3 .= $num_prenota."_";
} # fine (($num_prenota - 1) != $ultimo_num_prenota)
elseif (substr($num3,-1) == "_") $num3 = substr($num3,0,-1);
} # fine else if ($ultimo_num_prenota < 0)
$ultimo_num_prenota = $num_prenota;
} # fine if ($ultimo_num_prenota != $num_prenota)
} # fine foreach ($lista_num_prenota as $num2 => $num_prenota)
if (substr($num3,-1) != "_") $num3 .= "-".$ultimo_num_prenota;
else $num3 = substr($num3,0,-1);
} # fine if (!empty($lista_num_prenota))
} # fine else if ($ripeti_tutto)

if (strlen($nome_contratto."_".$anno_corr."_".$nome_file_contr[$num1].$num3.".$suff_file") < 235) $nome_file_contr[$num1] = $nome_contratto."_".$anno_corr."_".$nome_file_contr[$num1].$num3.".$suff_file";
else {
$dati_file_dat[$num1]['nome'] = $nome_contratto."_".$anno_corr."_".$nome_file_contr[$num1]."_1+1";
$dati_file_dat[$num1]['n_pren'] = substr($num3,1);
$nome_file_contr[$num1] = $dati_file_dat[$num1]['nome'].".$suff_file";
} # fine else if (strlen($nome_contratto."_".$anno_corr."_".$nome_file_contr[$num1].$num3.".$suff_file") < 235)
if (!$crea_file_dopo) {
if (!empty($nomi_contratti['compress'][$numero_contratto])) {
$nome_file_contr[$num1] .= ".gz";
$lock_compress[$num1] = crea_lock_file($dir_salva."/".$nome_file_contr[$num1]);
$filecontr[$num1] = gzopen($dir_salva."/".$nome_file_contr[$num1],"wb9");
} # fine if (!empty($nomi_contratti['compress'][$numero_contratto]))
else {
$filecontr[$num1] = fopen($dir_salva."/".$nome_file_contr[$num1],"w+");
flock($filecontr[$num1],2);
} # fine else if (!empty($nomi_contratti['compress'][$numero_contratto]))
} # fine if (!$crea_file_dopo)
else $filecontr['esist'] = 1;
} # fine for $num1

if (!$crea_file_dopo) {
flock($filelock,3);
fclose($filelock);
if (@is_file(C_DATI_PATH."/crea_contr.lock")) unlink(C_DATI_PATH."/crea_contr.lock");
} # fine if (!$crea_file_dopo)
} # fine if ($dir_salva)


for ($num1 = 0 ; $num1 < $num_variabili ; $num1++) {
$nome_var = risul_query($variabili,$num1,'testo');
$num_var = risul_query($variabili,$num1,'numero');
if (!isset($var_riserv[$nome_var])) $variabile[$num_var] = $nome_var;
} # fine for $num1
$variabile['-1'] = "messaggio_di_errore";
$variabile['-2'] = "errore_ripetizione";
$variabile['-3'] = "nome_documento_scaricato";
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) $variabile[(-100 - $num1)] = "allegato$num1";
$prendi_sempre_ospiti = 0;

for ($num1 = 0 ; $num1 < $num_arrays ; $num1++) {
$nome_arr = explode(";",risul_query($arrays,$num1,'testo'));
$var_array = $nome_arr[1];
#$tipo_arr = $nome_arr[2];
$nome_arr = $nome_arr[0];
if (!isset($var_riserv[$nome_arr])) {
$num_arr = risul_query($arrays,$num1,'numero');
$array[$num_arr] = $nome_arr;
$var_arr[$num_arr] = $var_array;
$var_arr_nome[$nome_arr] = $var_array;
$arr_var_esist[$var_array] = 1;
} # fine if (!isset($var_riserv[$nome_arr]))
} # fine for $num1
unset($var_riserv);

for ($num1 = 0 ; $num1 < $num_condizioni_ini_d ; $num1++) {
$condizione = risul_query($condizioni_ini_d,$num1,'testo');
$condizione_ini_d_vett[$num1] = explode("#@?",$condizione);
$azione_ini_d_vett[$num1] = explode("#%?",$condizione_ini_d_vett[$num1][2]);
if ($condizione_ini_d_vett[$num1][1]) {
$condizione_ini_d_vett[$num1] = explode("#$?",$condizione_ini_d_vett[$num1][1]);
$num_cond_ini_d_vett[$num1] = count($condizione_ini_d_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_ini_d_vett[$num1] ; $num2++) $condizione_ini_d_vett[$num1][$num2] = explode("#%?",$condizione_ini_d_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else {
$num_cond_ini_d_vett[$num1] = 0;
$condizione_ini_d_vett[$num1] = "";
} # fine else if ($condizione_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_ini_r ; $num1++) {
$condizione = risul_query($condizioni_ini_r,$num1,'testo');
$condizione_ini_r_vett[$num1] = explode("#@?",$condizione);
$azione_ini_r_vett[$num1] = explode("#%?",$condizione_ini_r_vett[$num1][2]);
if ($condizione_ini_r_vett[$num1][1]) {
$condizione_ini_r_vett[$num1] = explode("#$?",$condizione_ini_r_vett[$num1][1]);
$num_cond_ini_r_vett[$num1] = count($condizione_ini_r_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_ini_r_vett[$num1] ; $num2++) $condizione_ini_r_vett[$num1][$num2] = explode("#%?",$condizione_ini_r_vett[$num1][$num2]);
} # fine if ($condizione_ini_r_vett[$num1][1])
else {
$num_cond_ini_r_vett[$num1] = 0;
$condizione_ini_r_vett[$num1] = "";
} # fine else if ($condizione_ini_r_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione_vett[$num1] = explode("#@?",$condizione);
$azione_vett[$num1] = explode("#%?",$condizione_vett[$num1][2]);
if ($condizione_vett[$num1][1]) {
$condizione_vett[$num1] = explode("#$?",$condizione_vett[$num1][1]);
$num_cond_vett[$num1] = count($condizione_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_vett[$num1] ; $num2++) $condizione_vett[$num1][$num2] = explode("#%?",$condizione_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else {
$num_cond_vett[$num1] = 0;
$condizione_vett[$num1] = "";
} # fine else if ($condizione_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_o ; $num1++) {
$condizione = risul_query($condizioni_rip_o,$num1,'testo');
$condizione_rip_o_vett[$num1] = explode("#@?",$condizione);
$azione_rip_o_vett[$num1] = explode("#%?",$condizione_rip_o_vett[$num1][2]);
if ($condizione_rip_o_vett[$num1][1]) {
$condizione_rip_o_vett[$num1] = explode("#$?",$condizione_rip_o_vett[$num1][1]);
$num_cond_rip_o_vett[$num1] = count($condizione_rip_o_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_o_vett[$num1] ; $num2++) $condizione_rip_o_vett[$num1][$num2] = explode("#%?",$condizione_rip_o_vett[$num1][$num2]);
} # fine if ($condizione_rip_o_vett[$num1][1])
else {
$num_cond_rip_o_vett[$num1] = 0;
$condizione_rip_o_vett[$num1] = "";
} # fine else if ($condizione_rip_o_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_c ; $num1++) {
$condizione = risul_query($condizioni_rip_c,$num1,'testo');
$condizione_rip_c_vett[$num1] = explode("#@?",$condizione);
$azione_rip_c_vett[$num1] = explode("#%?",$condizione_rip_c_vett[$num1][2]);
if ($condizione_rip_c_vett[$num1][1]) {
$condizione_rip_c_vett[$num1] = explode("#$?",$condizione_rip_c_vett[$num1][1]);
$num_cond_rip_c_vett[$num1] = count($condizione_rip_c_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_c_vett[$num1] ; $num2++) $condizione_rip_c_vett[$num1][$num2] = explode("#%?",$condizione_rip_c_vett[$num1][$num2]);
} # fine if ($condizione_rip_c_vett[$num1][1])
else {
$num_cond_rip_c_vett[$num1] = 0;
$condizione_rip_c_vett[$num1] = "";
} # fine else if ($condizione_rip_c_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_p ; $num1++) {
$condizione = risul_query($condizioni_rip_p,$num1,'testo');
$condizione_rip_p_vett[$num1] = explode("#@?",$condizione);
$azione_rip_p_vett[$num1] = explode("#%?",$condizione_rip_p_vett[$num1][2]);
if ($condizione_rip_p_vett[$num1][1]) {
$condizione_rip_p_vett[$num1] = explode("#$?",$condizione_rip_p_vett[$num1][1]);
$num_cond_rip_p_vett[$num1] = count($condizione_rip_p_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_p_vett[$num1] ; $num2++) $condizione_rip_p_vett[$num1][$num2] = explode("#%?",$condizione_rip_p_vett[$num1][$num2]);
} # fine if ($condizione_rip_p_vett[$num1][1])
else {
$num_cond_rip_p_vett[$num1] = 0;
$condizione_rip_p_vett[$num1] = "";
} # fine else if ($condizione_rip_p_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_u ; $num1++) {
$condizione = risul_query($condizioni_rip_u,$num1,'testo');
$condizione_rip_u_vett[$num1] = explode("#@?",$condizione);
$azione_rip_u_vett[$num1] = explode("#%?",$condizione_rip_u_vett[$num1][2]);
if ($condizione_rip_u_vett[$num1][1]) {
$condizione_rip_u_vett[$num1] = explode("#$?",$condizione_rip_u_vett[$num1][1]);
$num_cond_rip_u_vett[$num1] = count($condizione_rip_u_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_u_vett[$num1] ; $num2++) $condizione_rip_u_vett[$num1][$num2] = explode("#%?",$condizione_rip_u_vett[$num1][$num2]);
} # fine if ($condizione_rip_u_vett[$num1][1])
else {
$num_cond_rip_u_vett[$num1] = 0;
$condizione_rip_u_vett[$num1] = "";
} # fine else if ($condizione_rip_u_vett[$num1][1])
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_d ; $num1++) {
$condizione = risul_query($condizioni_rip_d,$num1,'testo');
$condizione_rip_d_vett[$num1] = explode("#@?",$condizione);
$azione_rip_d_vett[$num1] = explode("#%?",$condizione_rip_d_vett[$num1][2]);
if ($condizione_rip_d_vett[$num1][1]) {
$condizione_rip_d_vett[$num1] = explode("#$?",$condizione_rip_d_vett[$num1][1]);
$num_cond_rip_d_vett[$num1] = count($condizione_rip_d_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_d_vett[$num1] ; $num2++) $condizione_rip_d_vett[$num1][$num2] = explode("#%?",$condizione_rip_d_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else {
$num_cond_rip_d_vett[$num1] = 0;
$condizione_rip_d_vett[$num1] = "";
} # fine else if ($condizione_vett[$num1][1])
} # fine for $num1

$num_condizioni_rip_a_vett = array();
for ($num1 = 0 ; $num1 < $num_condizioni_rip_a ; $num1++) {
$condizione = explode("#@?",risul_query($condizioni_rip_a,$num1,'testo'));
$nome_arr_rip = $array[substr($condizione[0],3)];
if (empty($num_condizioni_rip_a_vett[$nome_arr_rip])) $num_condizioni_rip_a_vett[$nome_arr_rip] = 1;
else $num_condizioni_rip_a_vett[$nome_arr_rip]++;
$num2 = ($num_condizioni_rip_a_vett[$nome_arr_rip] - 1);
$condizione_rip_a_vett[$nome_arr_rip][$num2] = $condizione;
$azione_rip_a_vett[$nome_arr_rip][$num2] = explode("#%?",$condizione_rip_a_vett[$nome_arr_rip][$num2][2]);
if ($condizione_rip_a_vett[$nome_arr_rip][$num2][1]) {
$condizione_rip_a_vett[$nome_arr_rip][$num2] = explode("#$?",$condizione_rip_a_vett[$nome_arr_rip][$num2][1]);
$num_cond_rip_a_vett[$nome_arr_rip][$num2] = count($condizione_rip_a_vett[$nome_arr_rip][$num2]);
for ($num3 = 1 ; $num3 < $num_cond_rip_a_vett[$nome_arr_rip][$num2] ; $num3++) $condizione_rip_a_vett[$nome_arr_rip][$num2][$num3] = explode("#%?",$condizione_rip_a_vett[$nome_arr_rip][$num2][$num3]);
} # fine if ($condizione_rip_a_vett[$nome_arr_rip][$num2][1])
else {
$num_cond_rip_a_vett[$nome_arr_rip][$num2] = 0;
$condizione_rip_a_vett[$nome_arr_rip][$num2] = "";
} # fine else if ($condizione_rip_a_vett[$nome_arr_rip][$num2][1])
} # fine for $num1

if (empty($trad_var_vett)) {
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." && $ini_lingua != $lingua_mex) {
$trad_var = array();
include("./includes/lang/$ini_lingua/visualizza_contratto_var.php");
foreach ($trad_var as $var_trad_ita => $var_trad_ext) $trad_var_vett[$var_trad_ext] = $var_trad_ita;
} # fine if ($file != "." && $file != ".." && $ini_lingua != $lingua)
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
if ($lingua_mex != "ita") {
$trad_var = array();
include("./includes/lang/".$lingua_mex."/visualizza_contratto_var.php");
foreach ($trad_var as $var_trad_ita => $var_trad_ext) $trad_var_vett[$var_trad_ext] = $var_trad_ita;
} # fine if ($lingua_mex != "ita")
unset($trad_var);
} # fine if (empty($trad_var_vett))
foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita) {
if ($var_trad_ita == "commento_personalizzato") {
for ($num1 = 0 ; $num1 < $num_commenti_pers ; $num1++) {
$var_trad_ita = $var_predef[($numero_ins_comm_pers + $num1)];
$trad_var_vett[$var_trad_ext."_".substr($var_trad_ita,24)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "commento_personalizzato")
if ($var_trad_ita == "campo_personalizzato") {
for ($num1 = 0 ; $num1 < $num_campi_pers_cliente ; $num1++) {
$var_trad_ita = $var_predef[($numero_inserimento_pers + $num1)];
$trad_var_vett[$var_trad_ext."_".substr($var_trad_ita,21)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "campo_personalizzato")
if ($var_trad_ita == "num_persone_tipo") {
for ($num1 = 0 ; $num1 < $dati_cat_pers['num'] ; $num1++) {
$var_trad_ita = $var_predef[($numero_ins_cat_pers + $num1)];
$trad_var_vett[$var_trad_ext."_".substr($var_trad_ita,17)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "num_persone_tipo")
if ($var_trad_ita == "allegato") {
for ($num1 = 0 ; $num1 < $dati_allegato['num'] ; $num1++) {
$var_trad_ita = $var_predef[($numero_inserimento_alleg + $num1)];
$trad_var_vett[$var_trad_ext.($num1 + 1)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "allegato")
} # fine foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita)
for ($num_mln = 0 ; $num_mln < $num_contr_mln ; $num_mln++) {
if ($contr_multilingua) $contratto_orig = (string) $contratti_orig_mln[$lingue_contr[$num_mln]];
reset($trad_var_vett);
foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita) {
$contratto_orig = str_replace("[".$var_trad_ext."]","[".$var_trad_ita."]",$contratto_orig);
if (isset($arr_var_esist[$var_trad_ita])) {
for ($num1 = 0 ; $num1 < $num_arrays ; $num1++) {
$num_arr = risul_query($arrays,$num1,'numero');
if ($var_arr[$num_arr] == $var_trad_ita) {
$contratto_orig = str_replace("[".$array[$num_arr]."(".$var_trad_ext.")]","[".$array[$num_arr]."(".$var_trad_ita.")]",$contratto_orig);
} # fine if ($var_arr[$num_arr] == $var_trad_ita)
} # fine for $num1
} # fine if (isset($arr_var_esist[$var_trad_ita]))
} # fine foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita)
# [A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? è una variabile del documento (compresi array)
# \"[^\"]*\" è un valore di testo (alternativo alla variabile nel secondo termine di comparazione)
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_orig)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_orig,2);
$contr_parziale = substr($contratto_orig,strlen($contr_vett[0]));
$contratto_orig = $contr_vett[0];
$condizione = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
#while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
#$contr_parziale = substr($contr_parziale,1);
$txt_sost1 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|&/",preg_replace("/^\\[c +/","",$condizione));
$condizione = "[[c]";
for ($num1 = 0 ; $num1 < count($txt_sost1) ; $num1++) {
if ($num1) $condizione .= " &";
$txt_sost2 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\|/",$txt_sost1[$num1]);
for ($num2 = 0 ; $num2 < count($txt_sost2) ; $num2++) {
if ($num2) $condizione .= " |";
$val_if = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)| *(!?=|!?%|>|<) */",$txt_sost2[$num2]);
$txt_sost2[$num2] = trim(substr($txt_sost2[$num2],strlen($val_if[0])));
$var_if = trim($val_if[0]);
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
#$val_var_if = ${$parti_arr[0]}[${$parti_arr[1]}];
if (!empty($trad_var_vett[$parti_arr[1]])) $var_if = $parti_arr[0]."(".$trad_var_vett[$parti_arr[1]].")";
} # fine if (str_replace("(","",$var_if) != $var_if)
elseif (!empty($trad_var_vett[$var_if])) $var_if = $trad_var_vett[$var_if];
if (substr($txt_sost2[$num2],0,2) == "!=") $condizione .= " $var_if!=";
else {
if (substr($txt_sost2[$num2],0,1) == ">") $condizione .= " $var_if>";
else {
if (substr($txt_sost2[$num2],0,1) == "<") $condizione .= " $var_if<";
else {
if (substr($txt_sost2[$num2],0,2) == "!%") $condizione .= " $var_if!%";
else {
if (substr($txt_sost2[$num2],0,1) == "%") $condizione .= " $var_if%";
else $condizione .= " $var_if=";
} # fine else if (substr($txt_sost2[$num2],0,2) == "!%")
} # fine else if (substr($txt_sost2[$num2],0,1) == "<")
} # fine else if (substr($txt_sost2[$num2],0,1) == ">")
} # fine else if (substr($txt_sost2[$num2],0,2) == "!=")
$val_if = trim($val_if[1]);
if (strstr($val_if,'"')) $val_if = '"'.str_replace("\"","",$val_if).'"';
else {
if (str_replace("(","",$val_if) != $val_if) {
$parti_arr = explode("(",substr($val_if,0,-1));
if (!empty($trad_var_vett[$parti_arr[1]])) $val_if = $parti_arr[0]."(".$trad_var_vett[$parti_arr[1]].")";
} # fine if (str_replace("(","",$val_if) != $val_if)
elseif (!empty($trad_var_vett[$val_if])) $val_if = $trad_var_vett[$val_if];
} # fine else if (strstr($val_if,'"'))
$condizione .= $val_if;
} # fine for $num2
} # fine for $num1
$contratto_orig .= $condizione."]";
$contratto_orig .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_orig))
$contratto_orig = str_replace("[[c] ","[c ",$contratto_orig);
if ($contr_multilingua) $contratti_orig_mln[$lingue_contr[$num_mln]] = $contratto_orig;
if (strstr($contratto_orig,'num_ospiti_tot')) $prendi_sempre_ospiti = 1;
if ($num_unita and strstr($contratto_orig,'_inventario_unita')) $dati_app_contr[0] = 1;
} # fine for $num_mln
unset($trad_var_vett);
if (isset($unset_glob)) for ($num1 = $num_var_predef_ripeti ; $num1 < $num_var_predef ; $num1++) unset($var_predef[$num1]);

if (!$prendi_sempre_ospiti) {
$condizioni_osp = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE '%num_ospiti_tot%' ");
if (numlin_query($condizioni_osp)) $prendi_sempre_ospiti = 1;
chiudi_query($condizioni_osp);
} # fine if (!$prendi_sempre_ospiti)

if ($num_unita) {
if ($priv_vedi_inv_app != "n") {
if (!$dati_app_contr[0]) {
$condizioni_inv = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE '%_inventario_unita%' ");
if (numlin_query($condizioni_inv)) $dati_app_contr[0] = 1;
chiudi_query($condizioni_inv);
} # fine if (!$dati_app_contr[0])
if ($dati_app_contr[0]) {
if ($priv_vedi_beni_inv == "p" or $priv_vedi_beni_inv == "g") {
$condizione_beni_propri = "where ( utente_inserimento = '".C_ID_UTENTE_CONTR."'";
if ($priv_vedi_beni_inv == "g") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != C_ID_UTENTE_CONTR) $condizione_beni_propri .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_vedi_beni_inv == "g")
$condizione_beni_propri .= " )";
} # fine if ($priv_vedi_beni_inv == "p" or $priv_vedi_beni_inv == "g")
else $condizione_beni_propri = "";
$dati_beniinv = esegui_query("select * from $tablebeniinventario $condizione_beni_propri");
$var_if = numlin_query($dati_beniinv);
$txt_sost1 = array();
for ($num1 = 0 ; $num1 < $var_if ; $num1++) {
$val_if = risul_query($dati_beniinv,$num1,'idbeniinventario');
$txt_sost1[$val_if]['nome'] = trim(risul_query($dati_beniinv,$num1,'nome_bene'));
$txt_sost1[$val_if]['codice'] = trim(risul_query($dati_beniinv,$num1,'codice_bene'));
} # fine for $num1
chiudi_query($dati_beniinv);
$var_then_orig = $priv_vedi_inv_app;
if ($var_then_orig == "g" and ($priv_vedi_app == "g" or $priv_vedi_app == "p")) $var_then_orig = "s";
if ($var_then_orig == "p" and $priv_vedi_app == "p") $var_then_orig = "s";
if ($var_then_orig != "s") {
if (!function_exists('trova_app_consentiti')) include("./includes/funzioni_appartamenti.php");
if ($var_then_orig != "g") {
global $attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett;
$appartamenti_consentiti = trova_app_consentiti($dati_app,$num_app,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,C_ID_UTENTE_CONTR,$tableregole,$tablenometariffe);
unset($attiva_regole1_consentite);
unset($regole1_consentite);
unset($priv_mod_assegnazione_app);
unset($priv_mod_prenotazioni);
unset($priv_ins_assegnazione_app);
unset($priv_ins_nuove_prenota);
unset($attiva_tariffe_consentite);
unset($tariffe_consentite_vett);
} # fine if ($var_then_orig != "g")
else {
global $attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr;
$appartamenti_consentiti = trova_app_consentiti($dati_app,$num_app,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,C_ID_UTENTE_CONTR,$tableregole,$tablenometariffe);
unset($attiva_regole1_consentite_gr);
unset($regole1_consentite_gr);
unset($priv_mod_assegnazione_app_gr);
unset($priv_mod_prenotazioni_gr);
unset($priv_ins_assegnazione_app_gr);
unset($priv_ins_nuove_prenota_gr);
unset($attiva_tariffe_consentite_gr);
unset($tariffe_consentite_vett_gr);
} # fine else if ($var_then_orig != "g")
} # fine if ($var_then_orig != "s")
$dati_inv = esegui_query("select * from $tablerelinventario where idappartamento is not NULL ");
$var_if = numlin_query($dati_inv);
for ($num1 = 0 ; $num1 < $var_if ; $num1++) {
$val_if = risul_query($dati_inv,$num1,'idbeneinventario');
if (!empty($txt_sost1[$val_if]['nome'])) {
$txt_sost2 = risul_query($dati_inv,$num1,'idappartamento');
if ($var_then_orig == "s" or fixset($appartamenti_consentiti[$txt_sost2]) == "SI") {
if ($tipo_contratto == "contrhtm") $txt_sost2 = htmlspecialchars($txt_sost2,ENT_COMPAT);
for ($num2 = 1 ; $num2 <= $num_unita ; $num2++) {
if ($dati_app_contr[$num2]['nome'] == $txt_sost2) {
$txt_sost2 = risul_query($dati_inv,$num1,'quantita');
$val_then = risul_query($dati_inv,$num1,'quantita_min_predef');
$var_da_assegnare = risul_query($dati_inv,$num1,'richiesto_checkin');
if ($var_da_assegnare == "s") $var_da_assegnare = "y";
$txt_sost1[$val_if]['nome'] = str_replace("\r","",str_replace("\n"," ",$txt_sost1[$val_if]['nome']));
$txt_sost1[$val_if]['codice'] = str_replace("\r","",str_replace("\n"," ",$txt_sost1[$val_if]['codice']));
if (strstr($txt_sost1[$val_if]['nome'],",") or strstr($txt_sost1[$val_if]['nome'],'&quot;')) $txt_sost1[$val_if]['nome'] = '"'.str_replace('&quot;','""',$txt_sost1[$val_if]['nome']).'"';
if (strstr($txt_sost1[$val_if]['codice'],",") or strstr($txt_sost1[$val_if]['codice'],'&quot;')) $txt_sost1[$val_if]['codice'] = '"'.str_replace('&quot;','""',$txt_sost1[$val_if]['codice']).'"';
if (!isset($dati_app_contr[$num2]['inv']['dettagli'])) $dati_app_contr[$num2]['inv']['dettagli'] = "";
$dati_app_contr[$num2]['inv']['dettagli'] .= $txt_sost1[$val_if]['nome'].",".$txt_sost1[$val_if]['codice'].",$var_da_assegnare,$val_then,$txt_sost2\n";
if ($txt_sost2) {
if (!isset($dati_app_contr[$num2]['inv']['lista'])) $dati_app_contr[$num2]['inv']['lista'] = "";
$dati_app_contr[$num2]['inv']['lista'] .= $txt_sost1[$val_if]['nome'];
if ($txt_sost2 > 1) {
if (substr($txt_sost1[$val_if]['nome'],-1) == '"') $dati_app_contr[$num2]['inv']['lista'] = substr($dati_app_contr[$num2]['inv']['lista'],0,-1)." X_$txt_sost2\"";
else $dati_app_contr[$num2]['inv']['lista'] .= " X_$txt_sost2";
} # fine if ($txt_sost2 > 1)
$dati_app_contr[$num2]['inv']['lista'] .= ", ";
} # fine if ($txt_sost2)
if ($val_then and $txt_sost2 < $val_then) {
if (!isset($dati_app_contr[$num2]['inv']['mancanti'])) $dati_app_contr[$num2]['inv']['mancanti'] = "";
$dati_app_contr[$num2]['inv']['mancanti'] .= $txt_sost1[$val_if]['nome'];
$val_then = ($val_then - $txt_sost2);
if ($val_then > 1) {
if (substr($txt_sost1[$val_if]['nome'],-1) == '"') $dati_app_contr[$num2]['inv']['mancanti'] = substr($dati_app_contr[$num2]['inv']['mancanti'],0,-1)." X_$txt_sost2\"";
else $dati_app_contr[$num2]['inv']['mancanti'] .= " X_$val_then";
} # fine if ($val_then > 1)
$dati_app_contr[$num2]['inv']['mancanti'] .= ", ";
} # fine if ($val_then and $txt_sost2 < $val_then)
break;
} # fine if ($dati_app_contr[$num1]['nome'] == $txt_sost2)
} # fine for $num2
} # fine if ($var_then_orig == "s" or fixset($appartamenti_consentiti[$txt_sost2]) == "SI")
} # fine if (!empty($txt_sost1[$val_if]['nome']))
} # fine for $num1
for ($num1 = 1 ; $num1 <= $num_unita ; $num1++) {
if (isset($dati_app_contr[$num1]['inv']['lista'])) $dati_app_contr[$num1]['inv']['lista'] = substr($dati_app_contr[$num1]['inv']['lista'],0,-2);
if (isset($dati_app_contr[$num1]['inv']['mancanti'])) $dati_app_contr[$num1]['inv']['mancanti'] = substr($dati_app_contr[$num1]['inv']['mancanti'],0,-2);
if (isset($dati_app_contr[$num1]['inv']['dettagli'])) {
$dati_app_contr[$num1]['inv']['dettagli'] = trim($dati_app_contr[$num1]['inv']['dettagli']);
if ($tipo_contratto == "contrhtm") $dati_app_contr[$num1]['inv']['dettagli'] = nl2br($dati_app_contr[$num1]['inv']['dettagli']);
} # fine if (isset($dati_app_contr[$num1]['inv']['dettagli']))
} # fine for $num1
chiudi_query($dati_inv);
unset($txt_sost1);
unset($appartamenti_consentiti);
} # fine if ($dati_app_contr[0])
} # fine if ($priv_vedi_inv_app != "n")
chiudi_query($dati_app);
unset($num_app);
} # fine if ($num_unita)


unset($tablepersonalizza);
unset($tablecontratti);
unset($tablebeniinventario);
unset($tablerelinventario);
unset($tableregole);
unset($tablenometariffe);
unset($priv_vedi_app);
unset($priv_vedi_tab_appartamenti);
unset($priv_vedi_tab_mesi);
unset($priv_vedi_beni_inv);
unset($priv_vedi_inv_app);
$utente_attuale = $n_utente_contr[C_ID_UTENTE_CONTR];


if ($tipo_contratto == "contreml") {
$contratto .= "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div><br>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numero_contratto\" value=\"$numero_contratto\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"origine_vecchia\" value=\"".htmlspecialchars($origine_vecchia)."\">
<input type=\"hidden\" name=\"manda_mail\" value=\"SI\">";
if ($dati_allegato['num']) $contratto .= "<input type=\"hidden\" name=\"num_all_passa\" value=\"".$dati_allegato['num']."\">";
if ($ripeti_tutto) $contratto .= "<input type=\"hidden\" name=\"numero_email\" value=\"$num_ripeti\">";
else {
$email_gia_inviata = 0;
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {
$var_if = fixset(${"email_".$n_r});
if (!$var_if) $var_if = fixset(${"email2_".$n_r});
if ($var_if) {
$cliente = esegui_query("select idclienti,doc_inviati from $tableclienti where (email = '".aggslashdb($var_if)."' or email2 = '".aggslashdb($var_if)."') and cognome = '".aggslashdb(${"cognome_".$n_r})."' and doc_inviati $ILIKE '%".aggslashdb("#@?$oggetto_email#|?")."%' ");
if (numlin_query($cliente) >= 1) {
$email_gia_inviata = 1;
$contratto .= "".mex("<span class=\"colblu\">Attenzione</span>: una email con lo stesso oggetto è già stata inviata al cliente",$pag)." ".${"cognome_".$n_r};
#if (isset(${"cognome2_".$n_r}) and strcmp((string) ${"cognome2_".$n_r},"")) $contratto .= " ".${"cognome2_".$n_r};
$contratto .= " ".mex("in data",$pag)." ".formatta_data($email_gia_inviata,$stile_data)."<br>";
} # fine if (numlin_query($cliente) >= 1)
} # fine if ($var_if)
} # fine for $n_r
if ($email_gia_inviata) $contratto .= "<br>";
$contratto .= "<input type=\"hidden\" name=\"numero_email\" value=\"1\">
<table><tr><td align=\"right\">".mex("Da",$pag).":</td><td>";
if ($modifica_pers != "NO") $contratto .= "<input type=\"text\" name=\"mittente_email1\" size=\"60\" value=\"$mittente_email\">";
else $contratto .= "<b>$mittente_email</b>";
$contratto .= "</td></tr><tr><td align=\"right\">
".mex("A",$pag).":</td><td>
<input type=\"text\" name=\"destinatario_email1\" size=\"60\" value=\"";
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {
if (${"email_".$n_r}) $contratto .= ${"email_".$n_r}.",";
elseif (${"email2_".$n_r}) $contratto .= ${"email2_".$n_r}.",";
} # fine for $n_r
if (substr($contratto,-1) == ",") $contratto = substr($contratto,0,-1);
$contratto .= "\"></td></tr><tr><td align=\"right\">
".mex("Oggetto",$pag).":</td><td>
<input type=\"text\" name=\"oggetto_email1\" size=\"60\" value=\"$oggetto_email\"></td></tr>";
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
if ($dati_allegato['corr'][$num1]) {
$contratto .= "<tr><td></td><td><label><input type=\"checkbox\" value=\"".$dati_allegato['ln_corr'][$num1]."\" name=\"allega$num1"."_1\" checked>
".mex("Allega",$pag)." <b>".$dati_allegato['corr'][$num1]."</b></label></td></tr>";
} # fine if ($dati_allegato['corr'][$num1])
} # fine for $num1
$contratto .= "<tr><td style=\"height: 3px;\"></td></tr></table>
&nbsp;&nbsp;<textarea name=\"testo_email1\" rows=32 cols=90>";
} # fine else if ($ripeti_tutto)
} # fine if ($tipo_contratto == "contreml")
unset($id_sessione);
unset($origine);


# Condizioni applicate all'inizio del documento
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_ini_d ; $num1++) {
$condizione = $condizione_ini_d_vett[$num1];
$num_se = $num_cond_ini_d_vett[$num1];
$azione = $azione_ini_d_vett[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = fixset($$var_if);
else {
$var_if = explode("(",substr($var_if,0,-1));
if (isset(${$var_if[0]}[fixset(${$var_if[1]})])) $var_if = ${$var_if[0]}[fixset(${$var_if[1]})];
else $var_if = null;
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = fixset($$val_if);
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
$var_if = (string) $var_if;
$val_if = (string) $val_if;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "!{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) == strtolower($var_if)) or ($se_cond_corr[1] == "!{A}" and str_replace($val_if,"",$var_if) == $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if (!empty($var_predef_data[$val_then]) and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr(fixstr($$val_then),$stile_data);
else $val_then = fixstr($$val_then);
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = fixstr(${$val_then[0]}[fixset(${$val_then[1]})]);
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9]) {
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
if ($azione[9] == "eht") $val_then = htmlspecialchars($val_then,ENT_QUOTES);
if ($azione[9] == "dht") $val_then = html_decod($val_then);
if ($azione[9] == "e64") $val_then = base64_encode($val_then);
if ($azione[9] == "d64") $val_then = base64_decode($val_then);
if (substr($azione[9],1,1) == "c") {
if (substr($azione[9],0,1) == "0") {
$txt_sost1 = 10;
$val_then = preg_replace("/[^0-9]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "0")
if (substr($azione[9],0,1) == "1") {
$txt_sost1 = 16;
$val_then = preg_replace("/[^0-9a-f]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "1")
if (substr($azione[9],0,1) == "2") {
$txt_sost1 = 2;
$val_then = preg_replace("/[^0-1]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "2")
if (substr($azione[9],0,1) == "3") {
$txt_sost1 = 36;
$val_then = preg_replace("/[^0-9a-z]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "3")
if (substr($azione[9],2,1) == "0") $txt_sost2 = 10;
if (substr($azione[9],2,1) == "1") $txt_sost2 = 16;
if (substr($azione[9],2,1) == "2") $txt_sost2 = 2;
if (substr($azione[9],2,1) == "3") $txt_sost2 = 36;
$val_then = base_convert($val_then,$txt_sost1,$txt_sost2);
} # fine if (substr($azione[9],1,1) == "c")
} # fine if ($azione[9])
} # fine if ($azione[3] == "var")
if (strcmp((string) $azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace((string) $txt_sost1,(string) $txt_sost2,(string) $val_then);
} # fine if (strcmp((string) $azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_then_orig = null;
} # fine else if (substr($azione[1],0,1) != "a")
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_da_assegnare = null;
} # fine else if (substr($azione[1],0,1) != "a")
$val_if = 0;
if ($tipo_contratto == "contrhtm") {
$val_then = (string) $var_da_assegnare;
$var_da_assegnare = html_decod($var_da_assegnare);
if ($val_then != $var_da_assegnare) $val_if = 1;
} # fine if ($tipo_contratto == "contrhtm")
if (strcmp((string) $azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp((string) $azione[3],""))
$var_da_assegnare = tronca_testo($var_da_assegnare,0,$azione[2]);
if ($val_if) $var_da_assegnare = htmlspecialchars($var_da_assegnare,ENT_QUOTES);
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = fixset(${$var_da_oper});
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = fixset(${$var_da_oper[0]}[${$var_da_oper[1]}]);
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = fixset(${$var_da_oper2});
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (float) $var_da_oper + (float) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (float) $var_da_oper - (float) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (float) $var_da_oper * (float) $var_da_oper2;
if ($azione[3] == "/") {
if ((float) $var_da_oper2 != (float) 0) $var_da_assegnare = (float) $var_da_oper / (float) $var_da_oper2;
else $var_da_assegnare = (float) $var_da_oper / 0.01;
} # fine if ($azione[3] == "/")
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (float) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (float) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = fixset(${$var_da_oper2});
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((float) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = fixset(${$array[substr($azione[3],1)]});
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = fixset($array_date_contr[$array[substr($azione[3],1)]]);
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1
unset($condizione_ini_d_vett);
unset($num_cond_ini_d_vett);
unset($azione_ini_d_vett);



if ($ripeti_tutto) {
$contratto_parte0[1] = $contratto_orig;
$ripeti_parte0[1] = "NO";
$tipo_parte0 = "";
$num_parti0_contr = 1;
} # fine if ($ripeti_tutto)
else {
if ($dir_salva) $numero_progressivo_documento = $num_prog_contr[1];
$num_parti0_contr = 0;
$contratto_vett = explode("[",$contratto_orig);
$num_contratto_vett = count($contratto_vett);
$livello = 0;
$contratto_presente = $contratto_vett[0];
for ($num1 = 1 ; $num1 < $num_contratto_vett ; $num1++) {
$parte = $contratto_vett[$num1];
$apertura = "";
$chiusura = "";
if (substr($parte,0,2) == "r]") $apertura = "r";
if (substr($parte,0,10) == "r4 array=\"") {
$parte_vett = explode("\"]",substr($parte,10));
if (strcmp((string) $parte_vett[0],"")) {
if (!strcmp(preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parte_vett[0]),"")) $apertura = "r4 array=\"".$parte_vett[0]."\"";
} # fine if (strcmp((string) $parte_vett[0],""))
} # fine if (substr($parte,0,10) == "r4 array=\"")
if (substr($parte,0,3) == "r6]") $apertura = "r6";
if (substr($parte,0,3) == "/r]") $chiusura = "r";
if (substr($parte,0,4) == "/r4]") $chiusura = "r4";
if (substr($parte,0,4) == "/r6]") $chiusura = "r6";
if ($apertura) {
$livello++;
if ($livello == 1 and (substr($apertura,0,2) == "r4" or $apertura == "r6")) {
if (strcmp((string) $contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "NO";
} # fine if (strcmp((string) $contratto_presente,""))
$contratto_presente = substr($contratto_vett[$num1],(strlen($apertura) + 1));
$var_arr_presente = substr($apertura,10,-1);
} # fine if ($livello == 1 and (substr($apertura,0,2) == "r4" or $apertura == "r6"))
else $apertura = "";
} # fine ($apertura)
if ($chiusura) {
if ($livello == 1 and ($chiusura == "r4" or $chiusura == "r6")) {
if (strcmp((string) $contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "SI";
if ($chiusura == "r4") {
$tipo_parte0[$num_parti0_contr] = "4";
$arr_parte0[$num_parti0_contr] = $var_arr_presente;
} # fine if ($chiusura == "r4")
if ($chiusura == "r6") $tipo_parte0[$num_parti0_contr] = "6";
} # fine if (strcmp((string) $contratto_presente,""))
$contratto_presente = substr($contratto_vett[$num1],(strlen($chiusura) + 2));
} # fine if ($livello == 1 and ($chiusura == "r4" or $chiusura == "r6"))
else $chiusura = "";
$livello--;
} # fine if ($chiusura)
if (!$apertura and !$chiusura) $contratto_presente .= "[".$contratto_vett[$num1];
} # fine for $num1
if (strcmp((string) $contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "NO";
} # fine if (strcmp((string) $contratto_presente,""))
} # fine else if ($ripeti_tutto)


# parti n_p0: parti del contratto se c'è una ripetizione di array o unità esternamente
for ($n_p0 = 1 ; $n_p0 <= $num_parti0_contr ; $n_p0++) {

$contratto_orig0 = $contratto_parte0[$n_p0];
unset($contratto_parte);
if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua) {
$contratto_parte[1] = $contratto_orig0;
if ($ripeti_tutto) $ripeti_parte[1] = "SI";
else $ripeti_parte[1] = "NO";
$num_parti_contr = 1;
} # fine if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua)
else {
$num_parti_contr = 0;
$contratto_restante = $contratto_orig0;
while (str_replace("[r]","",$contratto_restante) != $contratto_restante) {
$contr_vett = explode("[r]",$contratto_restante);
if ($contr_vett[0] != "") {
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contr_vett[0];
$ripeti_parte[$num_parti_contr] = "NO";
} # fine if ($contr_vett[0] != "")
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 3));
$contr_vett = explode("[/r]",$contratto_restante);
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contr_vett[0];
$ripeti_parte[$num_parti_contr] = "SI";
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 4));
} # fine while (str_replace("[r]","",$contratto_restante) != $contratto_restante)
if ($contratto_restante != "") {
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contratto_restante;
$ripeti_parte[$num_parti_contr] = "NO";
} # fine if ($contratto_restante != "")
} # fine else if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua)


if ($ripeti_parte0[$n_p0] == "NO") $num_ripeti0 = 1;
else {
$num_ripeti0 = 0;
if ($tipo_parte0[$n_p0] == "6") $num_ripeti0 = $num_unita;
if ($tipo_parte0[$n_p0] == "4" and @is_array(${$arr_parte0[$n_p0]})) {
$num_ripeti0 = count(${$arr_parte0[$n_p0]});
reset(${$arr_parte0[$n_p0]});
} # fine if ($tipo_parte0[$n_p0] == "4" and @is_array(${$arr_parte0[$n_p0]}))
} # fine else if ($ripeti_parte0[$n_p0] == "NO")

# ripetizione n_r0: ripetere parte del contratto se esternamente c'è una ripetizione di array o unità
for ($n_r0 = 1 ; $n_r0 <= $num_ripeti0 ; $n_r0++) {

$ripeti_prenota_data = "";
$condizioni_alternative0 = 0;
if ($ripeti_parte0[$n_p0] != "NO") {
if ($tipo_parte0[$n_p0] == "6") {
$nome_unita = $dati_app_contr[$n_r0]['nome'];
$casa_unita = $dati_app_contr[$n_r0]['casa'];
$piano_unita = $dati_app_contr[$n_r0]['piano'];
$capacita_unita = $dati_app_contr[$n_r0]['capacita'];
$priorita_unita = $dati_app_contr[$n_r0]['priorita'];
$lista_inventario_unita = fixset($dati_app_contr[$n_r0]['inv']['lista']);
$mancanti_inventario_unita = fixset($dati_app_contr[$n_r0]['inv']['mancanti']);
$dettagli_inventario_unita = fixset($dati_app_contr[$n_r0]['inv']['dettagli']);
# Se questa ripetizione di unità è senza ripetizione di prenotazioni ma ci sono condizioni solo per unità, allora
# uso le condizioni applicate all'inizio delle ripetizioni come condizioni per le unità
if ($num_condizioni_rip_u) $condizioni_alternative0 = "u";
} # fine if ($tipo_parte0[$n_p0] == "6")
else {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
$lista_inventario_unita = "";
$mancanti_inventario_unita = "";
$dettagli_inventario_unita = "";
} # fine else if ($tipo_parte0[$n_p0] == "6")

if ($tipo_parte0[$n_p0] == "4") {
${$var_arr_nome[$arr_parte0[$n_p0]]} = key(${$arr_parte0[$n_p0]});
if (isset($array_date_contr[$arr_parte0[$n_p0]]) and $array_date_contr[$arr_parte0[$n_p0]] == "SI") $ripeti_prenota_data = current(${$arr_parte0[$n_p0]});
next(${$arr_parte0[$n_p0]});
if (!empty($num_condizioni_rip_a_vett[$arr_parte0[$n_p0]])) $condizioni_alternative0 = "a";
} # fine if ($tipo_parte0[$n_p0] == "4")
} # fine if ($ripeti_parte0[$n_p0] != "NO")



# parti n_p: parti del contratto se ci sono ripetizioni prenotazioni
for ($n_p = 1 ; $n_p <= $num_parti_contr ; $n_p++) {
$numero_ripetizione_prenotazioni = 1;
if ($ripeti_parte[$n_p] == "SI" or $condizioni_alternative0) {


if ($ripeti_parte[$n_p] == "SI") {
$costo_tot_somma_ripetizioni = 0;
$caparra_somma_ripetizioni = 0;
$resto_caparra_somma_ripetizioni = 0;
$pagato_somma_ripetizioni = 0;
$resto_da_pagare_somma_ripetizioni = 0;
$num_persone_tot_somma_ripetizioni = 0;
$numero_ripetizione_prenotazioni_orig = 0;

$num_condizioni_corr = $num_condizioni_ini_r;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_ini_r_vett;
$num_cond_vett_corr = $num_cond_ini_r_vett;
$azione_vett_corr = $azione_ini_r_vett;
} # fine if ($num_condizioni_corr)
} # fine if ($ripeti_parte[$n_p] == "SI")

else {
if ($condizioni_alternative0 == "u") {
$num_condizioni_corr = $num_condizioni_rip_u;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_u_vett;
$num_cond_vett_corr = $num_cond_rip_u_vett;
$azione_vett_corr = $azione_rip_u_vett;
} # fine if ($num_condizioni_corr)
} # fine ($condizioni_alternative0 == "u")
else {
$num_condizioni_corr = $num_condizioni_rip_a_vett[$arr_parte0[$n_p0]];
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_a_vett[$arr_parte0[$n_p0]];
$num_cond_vett_corr = $num_cond_rip_a_vett[$arr_parte0[$n_p0]];
$azione_vett_corr = $azione_rip_a_vett[$arr_parte0[$n_p0]];
} # fine if ($num_condizioni_corr)
} # fine ($condizioni_alternative0 == "u")
} # fine else if ($ripeti_parte[$n_p] == "SI")

# Condizioni applicate all'inizio di ogni ripetizione di prenotazioni (o solo per ripetizioni di unità)
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_corr ; $num1++) {
$condizione = $condizione_vett_corr[$num1];
$num_se = $num_cond_vett_corr[$num1];
$azione = $azione_vett_corr[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = fixset($$var_if);
else {
$var_if = explode("(",substr($var_if,0,-1));
if (isset(${$var_if[0]}[fixset(${$var_if[1]})])) $var_if = ${$var_if[0]}[fixset(${$var_if[1]})];
else $var_if = null;
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = fixset($$val_if);
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
$var_if = (string) $var_if;
$val_if = (string) $val_if;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "!{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) == strtolower($var_if)) or ($se_cond_corr[1] == "!{A}" and str_replace($val_if,"",$var_if) == $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if (!empty($var_predef_data[$val_then]) and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr(fixstr($$val_then),$stile_data);
else $val_then = fixstr($$val_then);
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = fixstr(${$val_then[0]}[fixset(${$val_then[1]})]);
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9]) {
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
if ($azione[9] == "eht") $val_then = htmlspecialchars($val_then,ENT_QUOTES);
if ($azione[9] == "dht") $val_then = html_decod($val_then);
if ($azione[9] == "e64") $val_then = base64_encode($val_then);
if ($azione[9] == "d64") $val_then = base64_decode($val_then);
if (substr($azione[9],1,1) == "c") {
if (substr($azione[9],0,1) == "0") {
$txt_sost1 = 10;
$val_then = preg_replace("/[^0-9]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "0")
if (substr($azione[9],0,1) == "1") {
$txt_sost1 = 16;
$val_then = preg_replace("/[^0-9a-f]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "1")
if (substr($azione[9],0,1) == "2") {
$txt_sost1 = 2;
$val_then = preg_replace("/[^0-1]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "2")
if (substr($azione[9],0,1) == "3") {
$txt_sost1 = 36;
$val_then = preg_replace("/[^0-9a-z]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "3")
if (substr($azione[9],2,1) == "0") $txt_sost2 = 10;
if (substr($azione[9],2,1) == "1") $txt_sost2 = 16;
if (substr($azione[9],2,1) == "2") $txt_sost2 = 2;
if (substr($azione[9],2,1) == "3") $txt_sost2 = 36;
$val_then = base_convert($val_then,$txt_sost1,$txt_sost2);
} # fine if (substr($azione[9],1,1) == "c")
} # fine if ($azione[9])
} # fine if ($azione[3] == "var")
if (strcmp((string) $azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace((string) $txt_sost1,(string) $txt_sost2,(string) $val_then);
} # fine if (strcmp((string) $azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_then_orig = null;
} # fine else if (substr($azione[1],0,1) != "a")
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_da_assegnare = null;
} # fine else if (substr($azione[1],0,1) != "a")
$val_if = 0;
if ($tipo_contratto == "contrhtm") {
$val_then = (string) $var_da_assegnare;
$var_da_assegnare = html_decod($var_da_assegnare);
if ($val_then != $var_da_assegnare) $val_if = 1;
} # fine if ($tipo_contratto == "contrhtm")
if (strcmp((string) $azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp((string) $azione[3],""))
$var_da_assegnare = tronca_testo($var_da_assegnare,0,$azione[2]);
if ($val_if) $var_da_assegnare = htmlspecialchars($var_da_assegnare,ENT_QUOTES);
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = fixset(${$var_da_oper});
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = fixset(${$var_da_oper[0]}[${$var_da_oper[1]}]);
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = fixset(${$var_da_oper2});
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (float) $var_da_oper + (float) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (float) $var_da_oper - (float) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (float) $var_da_oper * (float) $var_da_oper2;
if ($azione[3] == "/") {
if ((float) $var_da_oper2 != (float) 0) $var_da_assegnare = (float) $var_da_oper / (float) $var_da_oper2;
else $var_da_assegnare = (float) $var_da_oper / 0.01;
} # fine if ($azione[3] == "/")
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (float) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (float) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr(fixstr($variabile[$azione[1]]),-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = fixset(${$var_da_oper2});
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((float) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = fixset(${$array[substr($azione[3],1)]});
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = fixset($array_date_contr[$array[substr($azione[3],1)]]);
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1

} # fine if ($ripeti_parte[$n_p] == "SI" or $condizioni_alternative0)
if ($ripeti_parte[$n_p] == "SI") {



# ripetizione n_r: se c'è da ripetere la parte del contratto per ogni prenotazione
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {

if (!$ripeti_prenota_data or (fixset(${"data_fine_".$n_r}) >= $ripeti_prenota_data and fixset(${"data_inizio_".$n_r}) <= $ripeti_prenota_data)) {


for ($num1 = 0 ; $num1 < $num_var_predef_ripeti ; $num1++) {
if (isset(${$var_predef[$num1]."_".$n_r})) ${$var_predef[$num1]} = ${$var_predef[$num1]."_".$n_r};
else ${$var_predef[$num1]} = null;
} # fine for $num1

$numero_ripetizione_prenotazioni_orig++;
$numero_ripetizione_prenotazioni = $numero_ripetizione_prenotazioni_orig;

if ($dir_salva and $ripeti_tutto and (empty($numero_progressivo_documento) or $numero_progressivo_documento < $num_prog_contr[$n_r])) $numero_progressivo_documento = $num_prog_contr[$n_r];

if ($tariffa_selezionata) {
$c_tot_selez = "c_tot_selez".$tariffa_selezionata."_".$n_r;
global $$c_tot_selez;
if ($$c_tot_selez) $costo_tot = $$c_tot_selez;
$c_tariffa_selez = "c_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$c_tariffa_selez;
if ($$c_tariffa_selez) $costo_tariffa = $$c_tariffa_selez;
$tarsett_tariffa_selez = "tarsett_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$tarsett_tariffa_selez;
if ($$tarsett_tariffa_selez) {
global ${"tariffesettimanali_".$n_r};
${"tariffesettimanali_".$n_r} = $$tarsett_tariffa_selez;
} # fine if ($$tarsett_tariffa_selez)
$n_tariffa_selez = "n_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$n_tariffa_selez;
if ($$n_tariffa_selez) $nome_tariffa = $$n_tariffa_selez;
$perctas_tariffa_selez = "perctas_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$perctas_tariffa_selez;
if ($$perctas_tariffa_selez) $percentuale_tasse_tariffa = $$perctas_tariffa_selez;
$cap_tariffa_selez = "cap_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$cap_tariffa_selez;
if ($$cap_tariffa_selez) $caparra = $$cap_tariffa_selez;
$comm_tariffa_selez = "comm_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$comm_tariffa_selez;
if ($$comm_tariffa_selez) $commissioni = $$comm_tariffa_selez;
$n_letti_agg_tariffa_selez = "n_letti_agg_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$n_letti_agg_tariffa_selez;
if ($$n_letti_agg_tariffa_selez) $n_letti_agg = $$n_letti_agg_tariffa_selez;
$numpers_tariffa_selez = "numpers_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$numpers_tariffa_selez;
if ($$numpers_tariffa_selez) $num_persone = $$numpers_tariffa_selez;
for ($num1 = 1 ; $num1 <= $dati_cat_pers['num'] ; $num1++) {
$numpers_tipo_tariffa_selez = "numpers_tipo_$num1"."_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$numpers_tipo_tariffa_selez;
if (strcmp((string) $$numpers_tipo_tariffa_selez,"")) ${"num_persone_tipo_$num1"} = $$numpers_tipo_tariffa_selez;
} # fine for $num1
$num_costi_aggiuntivi_tsel = "num_costi_aggiuntivi_tsel".$tariffa_selezionata."_".$n_r;
global $$num_costi_aggiuntivi_tsel;
if ($$num_costi_aggiuntivi_tsel) {
$num_costi_aggiuntivi = $$num_costi_aggiuntivi_tsel;
for ($numca = 0 ; $numca < $num_costi_aggiuntivi ; $numca++) {
$nome_costo_agg_tsel = "nome_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$val_costo_agg_tsel = "val_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$perc_tasse_costo_agg_tsel = "percentuale_tasse_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$molt_max_costo_agg_tsel = "moltiplica_max_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$val_giorn_max_costo_agg_tsel = "valore_giornaliero_max_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$giorni_costo_agg_tsel = "giorni_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$tipo_persona_costo_agg_tsel = "tipo_persona_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
global $$nome_costo_agg_tsel,$$val_costo_agg_tsel,$$perc_tasse_costo_agg_tsel,$$molt_max_costo_agg_tsel,$$val_giorn_max_costo_agg_tsel,$$giorni_costo_agg_tsel,$$tipo_persona_costo_agg_tsel;
${"nome_costo_agg".$numca."_".$n_r} = $$nome_costo_agg_tsel;
${"val_costo_agg".$numca."_".$n_r} = $$val_costo_agg_tsel;
${"percentuale_tasse_costo_agg".$numca."_".$n_r} = $$perc_tasse_costo_agg_tsel;
${"moltiplica_max_costo_agg".$numca."_".$n_r} = $$molt_max_costo_agg_tsel;
${"valore_giornaliero_max_costo_agg".$numca."_".$n_r} = $$val_giorn_max_costo_agg_tsel;
${"giorni_costo_agg".$numca."_".$n_r} = trasforma_id_in_date($$giorni_costo_agg_tsel,$date_id,$tableperiodi);
${"tipo_persona_costo_agg".$numca."_".$n_r} = $$tipo_persona_costo_agg_tsel;
} # fine for $numca
} # fine if ($$num_costi_aggiuntivi_tsel)
} # fine if ($tariffa_selezionata)

if ($ripeti_prenota_data) {
if (!strcmp(fixstr($tariffesettimanali[$n_r][$ripeti_prenota_data]),"")) {
global ${"tariffesettimanali_".$n_r};
$var_if = explode(";",${"tariffesettimanali_".$n_r});
$var_if = explode(",",$var_if[0]);
$num2 = count($var_if);
for ($num1 = 0 ; $num1 <= $num2 ; $num1++) {
if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data) {
if (empty($var_if[$num1])) $tariffesettimanali[$n_r][$ripeti_prenota_data] = 0;
else $tariffesettimanali[$n_r][$ripeti_prenota_data] = $var_if[$num1];
break;
} # fine if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data)
} # fine for $num1
} # fine if (!strcmp(fixstr($tariffesettimanali[$n_r][$ripeti_prenota_data]),""))
$costo_tariffa_giorno_array = $tariffesettimanali[$n_r][$ripeti_prenota_data];
$costo_tariffa_giorno_array_p = punti_in_num($costo_tariffa_giorno_array,$stile_soldi);
} # fine if ($ripeti_prenota_data)

$tutti_i_costi_agg = "";
$tutti_i_costi_agg_p = "";
$valore_tutti_costi_agg = (float) 0;
for ($numca = 0 ; $numca < $num_costi_aggiuntivi ; $numca++) {
$nome_costo_agg = "nome_costo_agg".$numca."_".$n_r;
$val_costo_agg = "val_costo_agg".$numca."_".$n_r;
$percentuale_tasse_costo_agg = "percentuale_tasse_costo_agg".$numca."_".$n_r;
$moltiplica_max_costo_agg = "moltiplica_max_costo_agg".$numca."_".$n_r;
$valore_giornaliero_max_costo_agg = "valore_giornaliero_max_costo_agg".$numca."_".$n_r;
$giorni_costo_agg = "giorni_costo_agg".$numca."_".$n_r;
$tipo_persona_costo_agg = "tipo_persona_costo_agg".$numca."_".$n_r;
$data_inserimento_costo_agg = "data_inserimento_costo_agg".$numca."_".$n_r;
$utente_inserimento_costo_agg = "utente_inserimento_costo_agg".$numca."_".$n_r;
if ((!isset($$nome_costo_agg) or !strcmp((string) $$nome_costo_agg,"")) and (!isset($$val_costo_agg) or !strcmp((string) $$val_costo_agg,""))) {
if ($tipo_contratto == "contrhtm") $$nome_costo_agg = htmlspecialchars($GLOBALS[$nome_costo_agg],ENT_COMPAT);
else $$nome_costo_agg = $GLOBALS[$nome_costo_agg];
$$val_costo_agg = $GLOBALS[$val_costo_agg];
$$percentuale_tasse_costo_agg = $GLOBALS[$percentuale_tasse_costo_agg];
$$moltiplica_max_costo_agg = $GLOBALS[$moltiplica_max_costo_agg];
$$valore_giornaliero_max_costo_agg = fixset($GLOBALS[$valore_giornaliero_max_costo_agg]);
$$giorni_costo_agg = fixset($GLOBALS[$giorni_costo_agg]);
$$tipo_persona_costo_agg = fixset($GLOBALS[$tipo_persona_costo_agg]);
$$data_inserimento_costo_agg = fixset($GLOBALS[$data_inserimento_costo_agg]);
if (isset($n_utente_contr[fixset($GLOBALS[$utente_inserimento_costo_agg])])) $$utente_inserimento_costo_agg = $n_utente_contr[$GLOBALS[$utente_inserimento_costo_agg]];
else $$utente_inserimento_costo_agg = "";
if (isset($unset_glob)) {
unset($GLOBALS[$nome_costo_agg]);
unset($GLOBALS[$val_costo_agg]);
unset($GLOBALS[$percentuale_tasse_costo_agg]);
unset($GLOBALS[$moltiplica_max_costo_agg]);
unset($GLOBALS[$valore_giornaliero_max_costo_agg]);
unset($GLOBALS[$giorni_costo_agg]);
unset($GLOBALS[$tipo_persona_costo_agg]);
unset($GLOBALS[$data_inserimento_costo_agg]);
unset($GLOBALS[$utente_inserimento_costo_agg]);
} # fine if (isset($unset_glob))
} # fine if ((!isset($$nome_costo_agg) or !strcmp((string) $$nome_costo_agg,"")) and (!isset($$val_costo_agg) or...
$nome_costo_agg = $$nome_costo_agg;
$val_costo_agg = $$val_costo_agg;
$percentuale_tasse_costo_agg = $$percentuale_tasse_costo_agg;
$moltiplica_max_costo_agg = $$moltiplica_max_costo_agg;
$valore_giornaliero_max_costo_agg = $$valore_giornaliero_max_costo_agg;
$$giorni_costo_agg = trasforma_id_in_date($$giorni_costo_agg,$date_id,$tableperiodi);
$giorni_costo_agg = $$giorni_costo_agg;
$tipo_persona_costo_agg = $$tipo_persona_costo_agg;
$data_inserimento_costo_agg = fixset($$data_inserimento_costo_agg);
$utente_inserimento_costo_agg = fixset($$utente_inserimento_costo_agg);
$val_costo_agg_p = punti_in_num($val_costo_agg,$stile_soldi);
$tutti_i_costi_agg .= "$nome_costo_agg: $val_costo_agg$tag_acapo";
$tutti_i_costi_agg_p .= "$nome_costo_agg: $val_costo_agg_p$tag_acapo";
$valore_tutti_costi_agg = (float) $valore_tutti_costi_agg + (float) $val_costo_agg;
calcola_tasse_contr($val_costo_agg,$percentuale_tasse_costo_agg,$arrotond_tasse,$tasse_costo_agg,$tasse_costo_agg_p,$val_costo_agg_senza_tasse,$val_costo_agg_senza_tasse_p,$stile_soldi);
if (strstr($moltiplica_max_costo_agg,",")) {
$moltiplica_max_costo_agg = explode(",",$moltiplica_max_costo_agg);
rsort($moltiplica_max_costo_agg);
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[0];
} # fine if (strstr($moltiplica_max_costo_agg,","))
if (strstr((string) $valore_giornaliero_max_costo_agg,",")) {
$valore_giornaliero_max_costo_agg = explode(",",$valore_giornaliero_max_costo_agg);
$valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg[(count($valore_giornaliero_max_costo_agg) - 1)];
} # fine if (strstr((string) $valore_giornaliero_max_costo_agg,","))
$valore_giornaliero_max_costo_agg_p = punti_in_num($valore_giornaliero_max_costo_agg,$stile_soldi);
if ($num_costo_agg_sel == $numca) {
$nome_costo_agg_sel = $nome_costo_agg;
$valore_costo_agg_sel = $val_costo_agg;
$valore_costo_agg_sel_p = $val_costo_agg_p;
$percentuale_tasse_costo_agg_sel = $percentuale_tasse_costo_agg;
$tasse_costo_agg_sel = $tasse_costo_agg;
$tasse_costo_agg_sel_p = $tasse_costo_agg_p;
$moltiplica_max_costo_agg_sel = $moltiplica_max_costo_agg;
$valore_giornaliero_max_costo_agg_sel = $valore_giornaliero_max_costo_agg;
$valore_giornaliero_max_costo_agg_sel_p = $valore_giornaliero_max_costo_agg_p;
} # fine if ($num_costo_agg_sel == $numca)
} # fine for $numca
$valore_tutti_costi_agg_p = punti_in_num($valore_tutti_costi_agg,$stile_soldi);

$tutti_i_pagamenti = "";
$tutti_i_pagamenti_p = "";
for ($num1 = 0 ; $num1 < $num_pagamenti ; $num1++) {
$saldo_paga = "saldo_paga".$num1."_".$n_r;
$data_paga = "data_paga".$num1."_".$n_r;
$data_operazione_paga = "data_operazione_paga".$num1."_".$n_r;
$utente_paga = "utente_paga".$num1."_".$n_r;
$valuta_paga = "valuta_paga".$num1."_".$n_r;
$tasso_cambio_paga = "tasso_cambio_paga".$num1."_".$n_r;
$valore_valuta_paga = "valore_valuta_paga".$num1."_".$n_r;
$metodo_paga = "metodo_paga".$num1."_".$n_r;
$id_paga = "id_paga".$num1."_".$n_r;
$note_paga = "note_paga".$num1."_".$n_r;
if ((!isset($$saldo_paga) or !strcmp((string) $$saldo_paga,"")) and (!isset($$data_paga) or !strcmp((string) $$data_paga,"")) and (!isset($$metodo_paga) or !strcmp((string) $$metodo_paga,""))) {
if (isset($GLOBALS[$saldo_paga])) $$saldo_paga = $GLOBALS[$saldo_paga];
if (isset($GLOBALS[$data_paga])) $$data_paga = $GLOBALS[$data_paga];
if (isset($GLOBALS[$data_operazione_paga])) $$data_operazione_paga = $GLOBALS[$data_operazione_paga];
if (isset($n_utente_contr[$GLOBALS[$utente_paga]])) $$utente_paga = $n_utente_contr[$GLOBALS[$utente_paga]];
if (isset($GLOBALS[$valuta_paga]) and strcmp((string) $GLOBALS[$valuta_paga],"")) {
$$valuta_paga = $GLOBALS[$valuta_paga];
if (isset($GLOBALS[$tasso_cambio_paga])) $$tasso_cambio_paga = $GLOBALS[$tasso_cambio_paga];
if (isset($GLOBALS[$valore_valuta_paga])) $$valore_valuta_paga = $GLOBALS[$valore_valuta_paga];
} # fine if (isset($GLOBALS[$valuta_paga]) and strcmp((string) $GLOBALS[$valuta_paga],""))
if ($tipo_contratto == "contrhtm") {
if (isset($GLOBALS[$metodo_paga]) and strcmp((string) $GLOBALS[$metodo_paga],"")) $$metodo_paga = htmlspecialchars($GLOBALS[$metodo_paga],ENT_COMPAT);
if (isset($GLOBALS[$id_paga]) and strcmp((string) $GLOBALS[$id_paga],"")) $$id_paga = htmlspecialchars($GLOBALS[$id_paga],ENT_COMPAT);
if (isset($GLOBALS[$note_paga]) and strcmp((string) $GLOBALS[$note_paga],"")) $$note_paga = htmlspecialchars($GLOBALS[$note_paga],ENT_COMPAT);
} # fine if ($tipo_contratto == "contrhtm")
else {
if (isset($GLOBALS[$metodo_paga]) and strcmp((string) $GLOBALS[$metodo_paga],"")) $$metodo_paga = $GLOBALS[$metodo_paga];
if (isset($GLOBALS[$id_paga]) and strcmp((string) $GLOBALS[$id_paga],"")) $$id_paga = $GLOBALS[$id_paga];
if (isset($GLOBALS[$note_paga]) and strcmp((string) $GLOBALS[$note_paga],"")) $$note_paga = $GLOBALS[$note_paga];
} # fine else if ($tipo_contratto == "contrhtm")
if (isset($unset_glob)) {
unset($GLOBALS[$saldo_paga]);
unset($GLOBALS[$data_paga]);
unset($GLOBALS[$data_operazione_paga]);
unset($GLOBALS[$utente_paga]);
unset($GLOBALS[$valuta_paga]);
unset($GLOBALS[$tasso_cambio_paga]);
unset($GLOBALS[$valore_valuta_paga]);
unset($GLOBALS[$metodo_paga]);
unset($GLOBALS[$id_paga]);
unset($GLOBALS[$note_paga]);
} # fine if (isset($unset_glob))
} # fine if ((!isset($$saldo_paga) or !strcmp((string) $$saldo_paga,"")) and (!isset($$data_paga) or...
$data_paga_f = formatta_data_contr(fixset($$data_operazione_paga),$stile_data);
$saldo_paga_p = punti_in_num(fixset($$saldo_paga),$stile_soldi);
$tutti_i_pagamenti .= str_replace(" ","$tag_spazio",$data_paga_f."  ".$$saldo_paga." $nome_valuta  ".fixset($$metodo_paga));
$tutti_i_pagamenti_p .= str_replace(" ","$tag_spazio",$data_paga_f."  ".$saldo_paga_p." $nome_valuta  ".fixset($$metodo_paga));
if (($num1 + 1) != $num_pagamenti) {
$tutti_i_pagamenti .= $tag_acapo;
$tutti_i_pagamenti_p .= $tag_acapo;
} # fine if (($num1 + 1) != $num_pagamenti)
} # fine for $num1
$valore_ultimo_pagamento = fixset(${"saldo_paga".($num_pagamenti - 1)."_".$n_r});
$valore_ultimo_pagamento_p = punti_in_num($valore_ultimo_pagamento,$stile_soldi);
$data_ultimo_pagamento = formatta_data_contr(fixset(${"data_paga".($num_pagamenti - 1)."_".$n_r}),$stile_data);
$utente_ultimo_pagamento = fixset(${"utente_paga".($num_pagamenti - 1)."_".$n_r});
$metodo_ultimo_pagamento = fixset(${"metodo_paga".($num_pagamenti - 1)."_".$n_r});


if ($costo_tot) $costo_tot_somma_ripetizioni = $costo_tot_somma_ripetizioni + $costo_tot;
if ($caparra) $caparra_somma_ripetizioni = $caparra_somma_ripetizioni + $caparra;
if ($costo_tot) $resto_caparra_somma_ripetizioni = $resto_caparra_somma_ripetizioni + $costo_tot;
if ($caparra) $resto_caparra_somma_ripetizioni = $resto_caparra_somma_ripetizioni - $caparra;
if ($pagato) $pagato_somma_ripetizioni = $pagato_somma_ripetizioni + $pagato;
if ($costo_tot) $resto_da_pagare_somma_ripetizioni = $resto_da_pagare_somma_ripetizioni + $costo_tot;
if ($pagato) $resto_da_pagare_somma_ripetizioni = $resto_da_pagare_somma_ripetizioni - $pagato;
if ($num_persone != "non specificato" and $num_persone != "") $num_persone_tot_somma_ripetizioni = $num_persone_tot_somma_ripetizioni + $num_persone;
if ($n_letti_agg) $num_persone_tot_somma_ripetizioni = $num_persone_tot_somma_ripetizioni + $n_letti_agg;

if ($costo_tot and $caparra) {
$resto_caparra = $costo_tot - $caparra;
$resto_caparra_p = punti_in_num($resto_caparra,$stile_soldi);
} # fine if ($costo_tot and $caparra)
if ($costo_tot and $commissioni) {
$resto_commissioni = $costo_tot - $commissioni;
$resto_commissioni_p = punti_in_num($resto_commissioni,$stile_soldi);
} # fine if ($costo_tot and $commissioni)
if ($costo_tot) {
$resto_da_pagare = $costo_tot - (float) $pagato;
$resto_da_pagare_p = punti_in_num($resto_da_pagare,$stile_soldi);
} # fine if ($costo_tot)

$nome_orig = $nome;
$soprannome_orig = $soprannome;
$cognome_orig = $cognome;
$cognome2_orig = $cognome2;
$data_nascita_orig = $data_nascita;
$documento_orig = $documento;
$nazione_orig = $nazione;
$regione_orig = $regione;
$citta_orig = $citta;
$via_orig = $via;
$numcivico_orig = $numcivico;
$telefono_orig = $telefono;
$telefono2_orig = $telefono2;
$telefono3_orig = $telefono3;
$fax_orig = $fax;
$email_orig = $email;
$email2_orig = $email2;
$email_certificata_orig = $email_certificata;
$cap_orig = $cap;
$codice_fiscale_orig = $codice_fiscale;
$partita_iva_orig = $partita_iva;
$num_persone_orig = $num_persone;
$caparra_orig = $caparra;
$commissioni_orig = $commissioni;
$data_inizio_orig = $data_inizio;
$data_fine_orig = $data_fine;
$num_periodi_orig = $num_periodi;
$orario_entrata_stimato_orig = $orario_entrata_stimato;
$nome_tariffa_orig = $nome_tariffa;
$costo_tariffa_orig = $costo_tariffa;
$sconto_orig = $sconto;
$percentuale_tasse_tariffa_orig = $percentuale_tasse_tariffa;
$commento_orig = $commento;
$origine_prenotazione_orig = $origine_prenotazione;
$unita_occupata_orig = $unita_occupata;
$unita_assegnabili_orig = $unita_assegnabili;
$pagato_orig = $pagato;
$costo_tot_orig = $costo_tot;
$n_letti_agg_orig = $n_letti_agg;
$numero_prenotazione_orig = $numero_prenotazione;
$data_inserimento_prenotazione_orig = $data_inserimento_prenotazione;

$codice_cittadinanza = trova_codice_rel($cittadinanza,$rel_esist,"nazione","nazioni",$codice2_cittadinanza,$codice3_cittadinanza,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_nascita = trova_codice_rel($nazione_nascita,$rel_esist,"nazione","nazioni",$codice2_nazione_nascita,$codice3_nazione_nascita,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_nascita = trova_codice_rel($regione_nascita,$rel_esist,"regione","regioni",$codice2_regione_nascita,$codice3_regione_nascita,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_nascita = trova_codice_rel($citta_nascita,$rel_esist,"citta","citta",$codice2_citta_nascita,$codice3_citta_nascita,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione = trova_codice_rel($nazione,$rel_esist,"nazione","nazioni",$codice2_nazione,$codice3_nazione,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione = trova_codice_rel($regione,$rel_esist,"regione","regioni",$codice2_regione,$codice3_regione,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta = trova_codice_rel($citta,$rel_esist,"citta","citta",$codice2_citta,$codice3_citta,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_tipo_documento = trova_codice_rel($tipo_documento,$rel_esist,"documentoid","documentiid",$codice2_tipo_documento,$codice3_tipo_documento,$tabledocumentiid,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_documento = trova_codice_rel($citta_documento,$rel_esist,"citta","citta",$codice2_citta_documento,$codice3_citta_documento,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_documento = trova_codice_rel($regione_documento,$rel_esist,"regione","regioni",$codice2_regione_documento,$codice3_regione_documento,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_documento = trova_codice_rel($nazione_documento,$rel_esist,"nazione","nazioni",$codice2_nazione_documento,$codice3_nazione_documento,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);

calcola_tasse_contr($costo_tariffa,$percentuale_tasse_tariffa,$arrotond_tasse,$tasse_tariffa,$tasse_tariffa_p,$costo_tariffa_senza_tasse,$costo_tariffa_senza_tasse_p,$stile_soldi);
calcola_tasse_contr(((float) $costo_tariffa - (float) $sconto),$percentuale_tasse_tariffa,$arrotond_tasse,$tasse_sconto,$tasse_sconto_p,$sconto_senza_tasse,$sconto_senza_tasse_p,$stile_soldi);
$tasse_sconto = $tasse_tariffa - $tasse_sconto;
$tasse_sconto_p = punti_in_num($tasse_sconto,$stile_soldi);
$sconto_senza_tasse = $costo_tariffa_senza_tasse - $sconto_senza_tasse;
$sconto_senza_tasse_p = punti_in_num($sconto_senza_tasse,$stile_soldi);

$apertura_rip_contr = "";
$chiusura_rip_contr = "";
$contratto_ripetizione = "";
$errore_ripetizione = "";
if ($prendi_sempre_ospiti) {
$ospiti = esegui_query("select idclienti,parentela from $tablerclientiprenota where idprenota = '".aggslashdb($numero_prenotazione_orig)."' order by num_ordine ");
$num_ospiti_tot = numlin_query($ospiti);
} # fine if ($prendi_sempre_ospiti)
if ($ripeti_tutto) {
$email_gia_inviata = 0;
$nome_documento_scaricato = "";
} # fine if ($ripeti_tutto)
$cond_non_ripetute_applicate = 0;

if ($tipo_contratto == "contreml") {
if ($ripeti_tutto) {
if ($contr_multilingua) {
if (!$codice_lingua or !strcmp(fixstr($oggetti_email_mln[$codice_lingua]),"")) $oggetto_email = $oggetti_email_mln[$contratti_orig_mln['predef']];
else $oggetto_email = $oggetti_email_mln[$codice_lingua];
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
$dati_allegato['corr'][$num1] = "";
if ($codice_lingua and !empty($dati_allegato["ln$codice_lingua"][$num1])) {
$dati_allegato['corr'][$num1] = $dati_allegato["ln$codice_lingua"][$num1];
$dati_allegato['ln_corr'][$num1] = $codice_lingua;
} # fine if ($codice_lingua and !empty($dati_allegato["ln$codice_lingua"][$num1]))
elseif (!empty($dati_allegato['ln'.$contratti_orig_mln['predef']][$num1])) {
$dati_allegato['corr'][$num1] = $dati_allegato['ln'.$contratti_orig_mln['predef']][$num1];
$dati_allegato['ln_corr'][$num1] = $contratti_orig_mln['predef'];
} # fine elseif (!empty($dati_allegato['ln'.$contratti_orig_mln['predef']][$num1]))
} # fine for $num1
} # fine if ($contr_multilingua)
$var_if = fixset(${"email_".$n_r});
if (!$var_if) $var_if = fixset(${"email2_".$n_r});
if ($var_if) {
$cliente = esegui_query("select idclienti,doc_inviati from $tableclienti where (email = '".aggslashdb($var_if)."' or email2 = '".aggslashdb($var_if)."') and cognome = '".aggslashdb(${"cognome_".$n_r})."' and doc_inviati $ILIKE '%".aggslashdb("#@?$oggetto_email#|?")."%' ");
if (numlin_query($cliente) >= 1) {
$email_gia_inviata = substr(stristr(risul_query($cliente,0,'doc_inviati'),"#@?$oggetto_email#|?"),strlen("#@?$oggetto_email#|?"),10);
$apertura_rip_contr .= "".mex("<span class=\"colblu\">Attenzione</span>: una email con lo stesso oggetto è già stata inviata al cliente",$pag)." ".${"cognome_".$n_r};
#if (isset(${"cognome2_".$n_r}) and strcmp((string) ${"cognome2_".$n_r},"")) $apertura_rip_contr .= " ".${"cognome2_".$n_r};
$apertura_rip_contr .= " ".mex("in data",$pag)." ".formatta_data($email_gia_inviata,$stile_data)."<br><br>";
} # fine if (numlin_query($cliente) >= 1)
} # fine if ($var_if)
$apertura_rip_contr .= "<table><tr><td align=\"right\">".mex("Da",$pag).":</td><td>";
if ($modifica_pers != "NO") $apertura_rip_contr .= "<input type=\"text\" name=\"mittente_email$n_r\" size=\"60\" value=\"$mittente_email\">";
else $apertura_rip_contr .= "<b>$mittente_email</b>";
$apertura_rip_contr .= "</td></tr><tr><td align=\"right\">
".mex("A",$pag).":</td><td>
<input type=\"text\" name=\"destinatario_email$n_r\" size=\"60\" value=\"$var_if\">
</td></tr><tr><td align=\"right\">
".mex("Oggetto",$pag).":</td><td>
<input type=\"text\" name=\"oggetto_email$n_r\" size=\"60\" value=\"$oggetto_email\"></td></tr>";
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
if ($dati_allegato['corr'][$num1]) {
$apertura_rip_contr .= "<tr><td></td><td><label><input type=\"checkbox\" value=\"".$dati_allegato['ln_corr'][$num1]."\" name=\"allega$num1"."_$n_r\" checked>
".mex("Allega",$pag)." <b>".$dati_allegato['corr'][$num1]."</b></label></td></tr>";
} # fine if ($dati_allegato['corr'][$num1])
} # fine for $num1
$apertura_rip_contr .= "<tr><td style=\"height: 3px;\"></td></tr></table>
&nbsp;&nbsp;<textarea name=\"testo_email$n_r\" rows=32 cols=90>";
$chiusura_rip_contr .= "</textarea><br>
<table><tr><td style=\"height: 3px;\"></td></tr></table>
<hr style=\"width: 95%; margin-left: 6px; text-align: left;\">";
} # fine if ($ripeti_tutto)
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) ${"allegato$num1"} = $dati_allegato['corr'][$num1];
} # fine if ($tipo_contratto == "contreml")

if ($contr_multilingua) {
if (!$codice_lingua or !strcmp(fixstr($contratti_orig_mln[$codice_lingua]),"")) $contratto_parte[1] = fixstr($contratti_orig_mln[$contratti_orig_mln['predef']]);
else $contratto_parte[1] = $contratti_orig_mln[$codice_lingua];
} # fine if ($contr_multilingua)


# Ripetizioni per gli ospiti, costi aggiuntivi, array e unità all'interno di ogni prenotazione
unset($contratto_parte2);
if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and str_replace("[r3]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and !preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and str_replace("[r5]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and str_replace("[r6]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and str_replace("[r7]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p]) {
$contratto_parte2[1] = $contratto_parte[$n_p];
$ripeti_parte2[1] = "NO";
$num_parti2_contr = 1;
} # fine if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and...
else {
$num_parti2_contr = 0;
$contratto_restante = $contratto_parte[$n_p];
while (str_replace("[r2]","",$contratto_restante) != $contratto_restante or str_replace("[r3]","",$contratto_restante) != $contratto_restante or preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or str_replace("[r5]","",$contratto_restante) != $contratto_restante or str_replace("[r6]","",$contratto_restante) != $contratto_restante or str_replace("[r7]","",$contratto_restante) != $contratto_restante) {
$contr_vett2 = explode("[r2]",$contratto_restante);
$contr_vett3 = explode("[r3]",$contratto_restante);
$contr_vett4 = preg_split("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante);
$contr_vett5 = explode("[r5]",$contratto_restante);
$contr_vett6 = explode("[r6]",$contratto_restante);
$contr_vett7 = explode("[r7]",$contratto_restante);
$l0_cv2 = strlen($contr_vett2[0]);
$l0_cv3 = strlen($contr_vett3[0]);
$l0_cv4 = strlen($contr_vett4[0]);
$l0_cv5 = strlen($contr_vett5[0]);
$l0_cv6 = strlen($contr_vett6[0]);
$l0_cv7 = strlen($contr_vett7[0]);
if ($l0_cv2 > $l0_cv7 and $l0_cv3 > $l0_cv7 and $l0_cv4 > $l0_cv7 and $l0_cv5 > $l0_cv7 and $l0_cv6 > $l0_cv7) {
$contr_vett = $contr_vett7;
$tipo_contr_vett = 7;
} # fine if ($l0_cv2 > $l0_cv7 and $l0_cv3 > $l0_cv7 and $l0_cv4 > $l0_cv7 and $l0_cv5 > $l0_cv7 and $l0_cv6 > $l0_cv7)
else {
if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6) {
$contr_vett = $contr_vett6;
$tipo_contr_vett = 6;
} # fine if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6)
else {
if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5) {
$contr_vett = $contr_vett5;
$tipo_contr_vett = 5;
} # fine if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5)
else {
if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4) {
$contr_vett = $contr_vett4;
$tipo_contr_vett = 4;
} # fine if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4)
else {
if ($l0_cv2 > $l0_cv3) {
$contr_vett = $contr_vett3;
$tipo_contr_vett = 3;
} # fine if ($l0_cv2 > $l0_cv3)
else {
$contr_vett = $contr_vett2;
$tipo_contr_vett = 2;
} # fine else if ($l0_cv2 > $l0_cv3)
} # fine else if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4)
} # fine else if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5)
} # fine else if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6)
} # fine else if ($l0_cv2 > $l0_cv7 and $l0_cv3 > $l0_cv7 and $l0_cv4 > $l0_cv7 and $l0_cv5 > $l0_cv7 and $l0_cv6 > $l0_cv7)
if (strcmp((string) $contr_vett[0],"")) {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if (strcmp((string) $contr_vett[0],""))
$contratto_restante = substr($contratto_restante,strlen($contr_vett[0]));
if ($tipo_contr_vett == 4) {
$arr_ripeti = explode("\"]",$contratto_restante,2);
$arr_ripeti = str_replace("[r4 array=\"","",$arr_ripeti[0]);
$contratto_restante = substr($contratto_restante,strlen("[r4 array=\"$arr_ripeti\"]"));
} # fine if ($tipo_contr_vett == 4)
else $contratto_restante = substr($contratto_restante,4);
$contr_vett = explode("[/r".$tipo_contr_vett."]",$contratto_restante);
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "SI";
$tipo_parte2[$num_parti2_contr] = $tipo_contr_vett;
if ($tipo_contr_vett == 4) $arr_parte2[$num_parti2_contr] = $arr_ripeti;
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 5));
} # fine while (str_replace("[r2]","",$contratto_restante) != $contratto_restante or...
if (strcmp((string) $contratto_restante,"")) {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contratto_restante;
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if (strcmp((string) $contratto_restante,""))
} # fine else if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and...

# parti n_p2: parti del contratto se ci sono ripetizioni ospiti, costi agg o array interni
for ($n_p2 = 1 ; $n_p2 <= $num_parti2_contr ; $n_p2++) {
if ($ripeti_parte2[$n_p2] != "SI") $num_ripeti2 = 1;
else {
if ($tipo_parte2[$n_p2] == 2) {
if (!$prendi_sempre_ospiti) {
$ospiti = esegui_query("select idclienti,parentela from $tablerclientiprenota where idprenota = '".aggslashdb($numero_prenotazione_orig)."' order by num_ordine ");
$num_ospiti_tot = numlin_query($ospiti);
} # fine if (!$prendi_sempre_ospiti)
$num_ripeti2 = $num_ospiti_tot;
} # fine if ($tipo_parte2[$n_p2] == 2)
if ($tipo_parte2[$n_p2] == 3) $num_ripeti2 = $num_costi_aggiuntivi;
if ($tipo_parte2[$n_p2] == 4) {
if (@is_array(${$arr_parte2[$n_p2]})) {
$num_ripeti2 = count(${$arr_parte2[$n_p2]});
reset(${$arr_parte2[$n_p2]});
} # fine if (@is_array(${$arr_parte2[$n_p2]}))
else $num_ripeti2 = 0;
} # fine if ($tipo_parte2[$n_p2] == 4)
if ($tipo_parte2[$n_p2] == 5) $num_ripeti2 = $num_pagamenti;
if ($tipo_parte2[$n_p2] == 6) $num_ripeti2 = $num_unita;
if ($tipo_parte2[$n_p2] == 7) {
if (@is_array($contratti_prenota[$anno][$numero_prenotazione_orig])) {
$num_ripeti2 = count($contratti_prenota[$anno][$numero_prenotazione_orig]);
if ($num_ripeti2) reset($contratti_prenota[$anno][$numero_prenotazione_orig]);
} # fine if (@is_array($contratti_prenota[$anno][$numero_prenotazione_orig]))
else $num_ripeti2 = 0;
} # fine if ($tipo_parte2[$n_p2] == 7)
} # fine else if ($ripeti_parte2[$n_p2] != "SI")

# ripetizione n_r2: se c'è da ripetere la parte del contratto per ospiti, costi agg, array o unità interni
for ($n_r2 = 1 ; $n_r2 <= $num_ripeti2 ; $n_r2++) {
$mostra_ripetizione = 1;
$condizioni_alternative = 0;

if ($ripeti_parte2[$n_p2] == "SI") {

if ($tipo_parte2[$n_p2] == 2) {
$numero_ospite = $n_r2;
$numero_cliente_ospite = risul_query($ospiti,($n_r2 - 1),'idclienti');
$parentela_ospite = risul_query($ospiti,($n_r2 - 1),'parentela');
$dati_osp = esegui_query("select * from $tableclienti where idclienti = '$numero_cliente_ospite' ");
$utente_ospite = risul_query($dati_osp,0,'utente_inserimento');
$cognome_ospite = "";
$cognome2_ospite = "";
$nome_ospite = "";
$soprannome_ospite = "";
$titolo_ospite = "";
$sesso_ospite = "";
$data_nascita_ospite = "";
$citta_nascita_ospite = "";
$regione_nascita_ospite = "";
$nazione_nascita_ospite = "";
$cittadinanza_ospite = "";
$nazione_ospite = "";
$regione_ospite = "";
$citta_ospite = "";
$via_ospite = "";
$numcivico_ospite = "";
$cap_ospite = "";
$documento_ospite = "";
$tipo_documento_ospite = "";
$citta_documento_ospite = "";
$regione_documento_ospite = "";
$nazione_documento_ospite = "";
$scadenza_documento_ospite = "";
$telefono_ospite = "";
$telefono2_ospite = "";
$telefono3_ospite = "";
$fax_ospite = "";
$email_ospite = "";
$email2_ospite = "";
$email_certificata_ospite = "";
$codice_fiscale_ospite = "";
$partita_iva_ospite = "";
if (numlin_query($dati_osp) == 1 and $vedi_clienti != "NO" and ($vedi_clienti != "PROPRI" or $utente_ospite == C_ID_UTENTE_CONTR) and ($vedi_clienti != "GRUPPI" or $utenti_gruppi[$utente_ospite])) {
$cognome_ospite = risul_query($dati_osp,0,'cognome');
$cognome2_ospite = risul_query($dati_osp,0,'cognome2');
$nome_ospite = risul_query($dati_osp,0,'nome');
$soprannome_ospite = risul_query($dati_osp,0,'soprannome');
$titolo_ospite = risul_query($dati_osp,0,'titolo');
$sesso_ospite = risul_query($dati_osp,0,'sesso');
$data_nascita_ospite = risul_query($dati_osp,0,'datanascita');
$citta_nascita_ospite = risul_query($dati_osp,0,'cittanascita');
$regione_nascita_ospite = risul_query($dati_osp,0,'regionenascita');
$nazione_nascita_ospite = risul_query($dati_osp,0,'nazionenascita');
$cittadinanza_ospite = risul_query($dati_osp,0,'nazionalita');
$nazione_ospite = risul_query($dati_osp,0,'nazione');
$regione_ospite = risul_query($dati_osp,0,'regione');
$citta_ospite = risul_query($dati_osp,0,'citta');
$via_ospite = risul_query($dati_osp,0,'via');
$numcivico_ospite = risul_query($dati_osp,0,'numcivico');
$cap_ospite = risul_query($dati_osp,0,'cap');
$documento_ospite = risul_query($dati_osp,0,'documento');
$tipo_documento_ospite = risul_query($dati_osp,0,'tipodoc');
$citta_documento_ospite = risul_query($dati_osp,0,'cittadoc');
$regione_documento_ospite = risul_query($dati_osp,0,'regionedoc');
$nazione_documento_ospite = risul_query($dati_osp,0,'nazionedoc');
$scadenza_documento_ospite = risul_query($dati_osp,0,'scadenzadoc');
$telefono_ospite = risul_query($dati_osp,0,'telefono');
$telefono2_ospite = risul_query($dati_osp,0,'telefono2');
$telefono3_ospite = risul_query($dati_osp,0,'telefono3');
$fax_ospite = risul_query($dati_osp,0,'fax');
$email_ospite = risul_query($dati_osp,0,'email');
$email2_ospite = risul_query($dati_osp,0,'email2');
$email_certificata_ospite = risul_query($dati_osp,0,'email3');
$codice_fiscale_ospite = risul_query($dati_osp,0,'cod_fiscale');
$partita_iva_ospite = risul_query($dati_osp,0,'partita_iva');
} # fine if (numlin_query($dati_osp) == 1 and...
$codice_cittadinanza_ospite = trova_codice_rel($cittadinanza_ospite,$rel_esist,"nazione","nazioni",$codice2_cittadinanza_ospite,$codice3_cittadinanza_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_parentela_ospite = trova_codice_rel($parentela_ospite,$rel_esist,"parentela","parentele",$codice2_parentela_ospite,$codice3_parentela_ospite,$tableparentele,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_nascita_ospite = trova_codice_rel($nazione_nascita_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_nascita_ospite,$codice3_nazione_nascita_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_nascita_ospite = trova_codice_rel($regione_nascita_ospite,$rel_esist,"regione","regioni",$codice2_regione_nascita_ospite,$codice3_regione_nascita_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_nascita_ospite = trova_codice_rel($citta_nascita_ospite,$rel_esist,"citta","citta",$codice2_citta_nascita_ospite,$codice3_citta_nascita_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_ospite = trova_codice_rel($nazione_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_ospite,$codice3_nazione_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_ospite = trova_codice_rel($regione_ospite,$rel_esist,"regione","regioni",$codice2_regione_ospite,$codice3_regione_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_ospite = trova_codice_rel($citta_ospite,$rel_esist,"citta","citta",$codice2_citta_ospite,$codice3_citta_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_tipo_documento_ospite = trova_codice_rel($tipo_documento_ospite,$rel_esist,"documentoid","documentiid",$codice2_tipo_documento_ospite,$codice3_tipo_documento_ospite,$tabledocumentiid,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_documento_ospite = trova_codice_rel($citta_documento_ospite,$rel_esist,"citta","citta",$codice2_citta_documento_ospite,$codice3_citta_documento_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_documento_ospite = trova_codice_rel($regione_documento_ospite,$rel_esist,"regione","regioni",$codice2_regione_documento_ospite,$codice3_regione_documento_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_documento_ospite = trova_codice_rel($nazione_documento_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_documento_ospite,$codice3_nazione_documento_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
if ($tipo_contratto == "contrhtm" and strcmp((string) $cognome_ospite,"")) {
$cognome_ospite = htmlspecialchars((string) $cognome_ospite,ENT_COMPAT);
$cognome2_ospite = htmlspecialchars((string) $cognome2_ospite,ENT_COMPAT);
$nome_ospite = htmlspecialchars((string) $nome_ospite,ENT_COMPAT);
$soprannome_ospite = htmlspecialchars((string) $soprannome_ospite,ENT_COMPAT);
$titolo_ospite = htmlspecialchars((string) $titolo_ospite,ENT_COMPAT);
$citta_nascita_ospite = htmlspecialchars((string) $citta_nascita_ospite,ENT_COMPAT);
$regione_nascita_ospite = htmlspecialchars((string) $regione_nascita_ospite,ENT_COMPAT);
$nazione_nascita_ospite = htmlspecialchars((string) $nazione_nascita_ospite,ENT_COMPAT);
$cittadinanza_ospite = htmlspecialchars((string) $cittadinanza_ospite,ENT_COMPAT);
$nazione_ospite = htmlspecialchars((string) $nazione_ospite,ENT_COMPAT);
$regione_ospite = htmlspecialchars((string) $regione_ospite,ENT_COMPAT);
$citta_ospite = htmlspecialchars((string) $citta_ospite,ENT_COMPAT);
$via_ospite = htmlspecialchars((string) $via_ospite,ENT_COMPAT);
$numcivico_ospite = htmlspecialchars((string) $numcivico_ospite,ENT_COMPAT);
$cap_ospite = htmlspecialchars((string) $cap_ospite,ENT_COMPAT);
$documento_ospite = htmlspecialchars((string) $documento_ospite,ENT_COMPAT);
$tipo_documento_ospite = htmlspecialchars((string) $tipo_documento_ospite,ENT_COMPAT);
$citta_documento_ospite = htmlspecialchars((string) $citta_documento_ospite,ENT_COMPAT);
$regione_documento_ospite = htmlspecialchars((string) $regione_documento_ospite,ENT_COMPAT);
$nazione_documento_ospite = htmlspecialchars((string) $nazione_documento_ospite,ENT_COMPAT);
$telefono_ospite = htmlspecialchars((string) $telefono_ospite,ENT_COMPAT);
$telefono2_ospite = htmlspecialchars((string) $telefono2_ospite,ENT_COMPAT);
$telefono3_ospite = htmlspecialchars((string) $telefono3_ospite,ENT_COMPAT);
$fax_ospite = htmlspecialchars((string) $fax_ospite,ENT_COMPAT);
$email_ospite = htmlspecialchars((string) $email_ospite,ENT_COMPAT);
$email2_ospite = htmlspecialchars((string) $email2_ospite,ENT_COMPAT);
$email_certificata_ospite = htmlspecialchars((string) $email_certificata_ospite,ENT_COMPAT);
$codice_fiscale_ospite = htmlspecialchars((string) $codice_fiscale_ospite,ENT_COMPAT);
$partita_iva_ospite = htmlspecialchars((string) $partita_iva_ospite,ENT_COMPAT);
} # fine if ($tipo_contratto == "contrhtm" and strcmp((string) $cognome_ospite,""))
if ($num_condizioni_rip_o) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_o;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_o_vett;
$num_cond_vett_corr = $num_cond_rip_o_vett;
$azione_vett_corr = $azione_rip_o_vett;
} # fine if ($num_condizioni_corr)
} # fine if ($num_condizioni_rip_o)
} # fine if ($tipo_parte2[$n_p2] == 2)
else $numero_ospite = 0;

if ($tipo_parte2[$n_p2] == 3) {
$numca = ($n_r2 - 1);
$nome_costo_agg = "nome_costo_agg".$numca."_".$n_r;
$val_costo_agg = "val_costo_agg".$numca."_".$n_r;
$percentuale_tasse_costo_agg = "percentuale_tasse_costo_agg".$numca."_".$n_r;
$moltiplica_max_costo_agg = "moltiplica_max_costo_agg".$numca."_".$n_r;
$valore_giornaliero_max_costo_agg = "valore_giornaliero_max_costo_agg".$numca."_".$n_r;
$giorni_costo_agg = "giorni_costo_agg".$numca."_".$n_r;
$tipo_persona_costo_agg = "tipo_persona_costo_agg".$numca."_".$n_r;
$data_inserimento_costo_agg = "data_inserimento_costo_agg".$numca."_".$n_r;
$utente_inserimento_costo_agg = "utente_inserimento_costo_agg".$numca."_".$n_r;
$nome_costo_agg = $$nome_costo_agg;
$valore_costo_agg = $$val_costo_agg;
$percentuale_tasse_costo_agg = $$percentuale_tasse_costo_agg;
$moltiplica_max_costo_agg = $$moltiplica_max_costo_agg;
$valore_giornaliero_max_costo_agg = $$valore_giornaliero_max_costo_agg;
$giorni_costo_agg = $$giorni_costo_agg;
if ($ripeti_prenota_data and strstr($giorni_costo_agg,",") and !strstr($giorni_costo_agg,",$ripeti_prenota_data,")) $mostra_ripetizione = 0;
$tipo_persona_costo_agg = $$tipo_persona_costo_agg;
$data_inserimento_costo_agg = $$data_inserimento_costo_agg;
$utente_inserimento_costo_agg = $$utente_inserimento_costo_agg;
$valore_costo_agg_p = punti_in_num($valore_costo_agg,$stile_soldi);
calcola_tasse_contr($valore_costo_agg,$percentuale_tasse_costo_agg,$arrotond_tasse,$tasse_costo_agg,$tasse_costo_agg_p,$valore_costo_agg_senza_tasse,$valore_costo_agg_senza_tasse_p,$stile_soldi);
if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg and $mostra_ripetizione) {
$moltiplica_max_costo_agg = explode(",",$moltiplica_max_costo_agg);
$valore_giornaliero_max_costo_agg = explode(",",$valore_giornaliero_max_costo_agg);
if ($ripeti_prenota_data and strstr($giorni_costo_agg,",")) {
$var_if = explode(",",$giorni_costo_agg);
$num2 = count($var_if);
for ($num1 = 1 ; $num1 < $num2 ; $num1++) {
if ($var_if[$num1] == $ripeti_prenota_data) {
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[$num1];
$valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg[($num1 - 1)];
break;
} # fine if ($var_if[$num1] == $ripeti_prenota_data)
} # fine for $num1
} # fine if ($ripeti_prenota_data and strstr($giorni_costo_agg,","))
else {
rsort($moltiplica_max_costo_agg);
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[0];
$valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg[(count($valore_giornaliero_max_costo_agg) - 1)];
} # fine else if ($ripeti_prenota_data and strstr($giorni_costo_agg,","))
} # fine if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg and $mostra_ripetizione)
elseif ($mostra_ripetizione) {
if (strstr((string) $valore_giornaliero_max_costo_agg,",")) {
$valore_giornaliero_max_costo_agg = explode(",",$valore_giornaliero_max_costo_agg);
$var_if = $valore_giornaliero_max_costo_agg[0];
$valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg[(count($valore_giornaliero_max_costo_agg) - 1)];
} # fine if (strstr((string) $valore_giornaliero_max_costo_agg,","))
else $var_if = "";
if ($ripeti_prenota_data) {
if ($ripeti_prenota_data == $data_fine) $valore_giornaliero_max_costo_agg = 0;
else {
if ($var_if) {
if (strstr($var_if,"r")) {
$num2 = explode("r",$var_if);
$var_if = $num2[0];
$num2 = $num2[1];
} # fine if (strstr($var_if,"r"))
else {
$num2 = $var_if;
$var_if = 0;
} # fine else if (strstr($var_if,"r"))
$data_trovata = 0;
for ($num1 = 0 ; $num1 < $num2 ; $num1++) {
if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data) {
$data_trovata = 1;
break;
} # fine if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data)
} # fine for $num1
if (!$var_if and !$data_trovata) $valore_giornaliero_max_costo_agg = 0;
if ($var_if and !$data_trovata) $valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg - $var_if;
} # fine if ($var_if)
$valore_giornaliero_max_costo_agg = $valore_giornaliero_max_costo_agg * $moltiplica_max_costo_agg;
} # fine else if ($ripeti_prenota_data == $data_fine)
} # fine if ($ripeti_prenota_data)
} # fine elseif ($mostra_ripetizione)
$valore_giornaliero_max_costo_agg_p = punti_in_num($valore_giornaliero_max_costo_agg,$stile_soldi);
if ($num_condizioni_rip_c) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_c;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_c_vett;
$num_cond_vett_corr = $num_cond_rip_c_vett;
$azione_vett_corr = $azione_rip_c_vett;
} # fine if ($num_condizioni_corr)
} # fine if ($num_condizioni_rip_c)
} # fine if ($tipo_parte2[$n_p2] == 3)
else {
$nome_costo_agg = "";
$valore_costo_agg = 0;
$valore_costo_agg_p = 0;
$percentuale_tasse_costo_agg = 0;
$tasse_costo_agg = 0;
$resto_tasse_costo_agg = 0;
$moltiplica_max_costo_agg = 0;
$valore_giornaliero_max_costo_agg = 0;
$valore_giornaliero_max_costo_agg_p = 0;
$giorni_costo_agg = "";
$tipo_persona_costo_agg = "";
$data_inserimento_costo_agg = "";
$utente_inserimento_costo_agg = "";
} # fine else if ($tipo_parte2[$n_p2] == 3)

if ($tipo_parte2[$n_p2] == 4) {
$ripeti_prenota_data2 = "";
${$var_arr_nome[$arr_parte2[$n_p2]]} = key(${$arr_parte2[$n_p2]});
if (isset($array_date_contr[$arr_parte2[$n_p2]]) and $array_date_contr[$arr_parte2[$n_p2]] == "SI") $ripeti_prenota_data2 = current(${$arr_parte2[$n_p2]});
next(${$arr_parte2[$n_p2]});
if ($ripeti_prenota_data2) {
if (!strcmp(fixstr($tariffesettimanali[$n_r][$ripeti_prenota_data2]),"")) {
global ${"tariffesettimanali_".$n_r};
$var_if = explode(";",${"tariffesettimanali_".$n_r});
$var_if = explode(",",$var_if[0]);
$num2 = count($var_if);
for ($num1 = 0 ; $num1 <= $num2 ; $num1++) {
if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data2) {
if (empty($var_if[$num1])) $tariffesettimanali[$n_r][$ripeti_prenota_data2] = 0;
else $tariffesettimanali[$n_r][$ripeti_prenota_data2] = $var_if[$num1];
break;
} # fine if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data2)
} # fine for $num1
} # fine if (!strcmp(fixstr($tariffesettimanali[$n_r][$ripeti_prenota_data2]),""))
$costo_tariffa_giorno_array = fixset($tariffesettimanali[$n_r][$ripeti_prenota_data2]);
$costo_tariffa_giorno_array_p = punti_in_num($costo_tariffa_giorno_array,$stile_soldi);
} # fine if ($ripeti_prenota_data2)
if (!empty($num_condizioni_rip_a_vett[$arr_parte2[$n_p2]])) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_a_vett[$arr_parte2[$n_p2]];
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_a_vett[$arr_parte2[$n_p2]];
$num_cond_vett_corr = $num_cond_rip_a_vett[$arr_parte2[$n_p2]];
$azione_vett_corr = $azione_rip_a_vett[$arr_parte2[$n_p2]];
} # fine if ($num_condizioni_corr)
} # fine if (!empty($num_condizioni_rip_a_vett[$arr_parte2[$n_p2]]))
} # fine if ($tipo_parte2[$n_p2] == 4)

if ($tipo_parte2[$n_p2] == 5) {
$valore_pagamento = fixset(${"saldo_paga".($n_r2 - 1)."_".$n_r});
$valore_pagamento_p = punti_in_num($valore_pagamento,$stile_soldi);
$data_pagamento = fixset(${"data_paga".($n_r2 - 1)."_".$n_r});
$data_operazione_pagamento = fixset(${"data_operazione_paga".($n_r2 - 1)."_".$n_r});
$utente_pagamento = fixset(${"utente_paga".($n_r2 - 1)."_".$n_r});
$valuta_pagamento = fixset(${"valuta_paga".($n_r2 - 1)."_".$n_r});
$tasso_cambio_pagamento = fixset(${"tasso_cambio_paga".($n_r2 - 1)."_".$n_r});
$valore_valuta_pagamento = fixset(${"valore_valuta_paga".($n_r2 - 1)."_".$n_r});
$metodo_pagamento = fixset(${"metodo_paga".($n_r2 - 1)."_".$n_r});
$id_pagamento = fixset(${"id_paga".($n_r2 - 1)."_".$n_r});
$note_pagamento = fixset(${"note_paga".($n_r2 - 1)."_".$n_r});
if ($n_r2 == $num_ripeti2) $ultimo_pagamento = "1";
else $ultimo_pagamento = "";
if ($num_condizioni_rip_p) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_p;
$condizione_vett_corr = $condizione_rip_p_vett;
$num_cond_vett_corr = $num_cond_rip_p_vett;
$azione_vett_corr = $azione_rip_p_vett;
} # fine if ($num_condizioni_rip_p)
} # fine if ($tipo_parte2[$n_p2] == 5)
else {
$valore_pagamento = 0;
$valore_pagamento_p = 0;
$data_pagamento = "";
$data_operazione_pagamento = "";
$utente_pagamento = "";
$valuta_pagamento = "";
$tasso_cambio_pagamento = "";
$valore_valuta_pagamento = "";
$metodo_pagamento = "";
$id_pagamento = "";
$note_pagamento = "";
$ultimo_pagamento = "";
} # fine else if ($tipo_parte2[$n_p2] == 5)

if ($tipo_parte2[$n_p2] == 6) {
$nome_unita = $dati_app_contr[$n_r2]['nome'];
$casa_unita = $dati_app_contr[$n_r2]['casa'];
$piano_unita = $dati_app_contr[$n_r2]['piano'];
$capacita_unita = $dati_app_contr[$n_r2]['capacita'];
$priorita_unita = $dati_app_contr[$n_r2]['priorita'];
$lista_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['lista']);
$mancanti_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['mancanti']);
$dettagli_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['dettagli']);
if ($num_condizioni_rip_u) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_u;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_u_vett;
$num_cond_vett_corr = $num_cond_rip_u_vett;
$azione_vett_corr = $azione_rip_u_vett;
} # fine if ($num_condizioni_corr)
} # fine if ($num_condizioni_rip_u)
} # fine if ($tipo_parte2[$n_p2] == 6)
elseif (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
$lista_inventario_unita = "";
$mancanti_inventario_unita = "";
$dettagli_inventario_unita = "";
} # fine elseif (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6")

if ($tipo_parte2[$n_p2] == 7) {
$numero_progressivo_documento_salvato = key($contratti_prenota[$anno][$numero_prenotazione_orig]);
$numero_progressivo_documento_salvato = explode("-",$numero_progressivo_documento_salvato);
$nome_documento_salvato = $nomi_contratti['salv'][$numero_progressivo_documento_salvato[0]];
$numero_progressivo_documento_salvato = $numero_progressivo_documento_salvato[1];
$nome_file_documento_salvato = current($contratti_prenota[$anno][$numero_prenotazione_orig]);
$data_creazione_documento_salvato = substr($nome_file_documento_salvato,0,10);
$nome_file_documento_salvato = substr($nome_file_documento_salvato,10);
if (strstr($nome_file_documento_salvato,"/")) {
$nome_download_documento_salvato = substr(strstr($nome_file_documento_salvato,"/"),1);
$nome_file_documento_salvato = str_replace("/$nome_download_documento_salvato","",$nome_file_documento_salvato);
} # fine if (strstr($nome_file_documento_salvato,"/"))
else {
if (substr($nome_file_documento_salvato,-3) == ".gz") $nome_download_documento_salvato = substr($nome_file_documento_salvato,0,-3);
else $nome_download_documento_salvato = $nome_file_documento_salvato;
} # fine else if (strstr($nome_file_documento_salvato,"/"))
next($contratti_prenota[$anno][$numero_prenotazione_orig]);
if ($num_condizioni_rip_d) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_d;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_rip_d_vett;
$num_cond_vett_corr = $num_cond_rip_d_vett;
$azione_vett_corr = $azione_rip_d_vett;
} # fine if ($num_condizioni_corr)
} # fine if ($num_condizioni_rip_d)
} # fine if ($tipo_parte2[$n_p2] == 7)
else {
$numero_progressivo_documento_salvato = "";
$nome_documento_salvato = "";
$data_creazione_documento_salvato = "";
$nome_file_documento_salvato = "";
$nome_download_documento_salvato = "";
} # fine else if ($tipo_parte2[$n_p2] == 7)

} # fine if ($ripeti_parte2[$n_p2] == "SI")

else {
$nome_costo_agg = "";
$valore_costo_agg = 0;
$valore_costo_agg_p = 0;
$percentuale_tasse_costo_agg = 0;
$tasse_costo_agg = 0;
$resto_tasse_costo_agg = 0;
$moltiplica_max_costo_agg = 0;
$valore_giornaliero_max_costo_agg = 0;
$valore_giornaliero_max_costo_agg_p = 0;
$giorni_costo_agg = "";
$tipo_persona_costo_agg = "";
$data_inserimento_costo_agg = "";
$utente_inserimento_costo_agg = "";
$numero_ospite = 0;
$valore_pagamento = 0;
$valore_pagamento_p = 0;
$data_pagamento = "";
$data_operazione_pagamento = "";
$utente_pagamento = "";
$valuta_pagamento = "";
$tasso_cambio_pagamento = "";
$valore_valuta_pagamento = "";
$metodo_pagamento = "";
$id_pagamento = "";
$note_pagamento = "";
$ultimo_pagamento = "";
$numero_progressivo_documento_salvato = "";
$nome_documento_salvato = "";
$data_creazione_documento_salvato = "";
$nome_file_documento_salvato = "";
$nome_download_documento_salvato = "";
if (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
$lista_inventario_unita = "";
$mancanti_inventario_unita = "";
$dettagli_inventario_unita = "";
} # fine if (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6")
} # fine if else ($ripeti_parte2[$n_p2] == "SI")

if ($mostra_ripetizione) {

#for ($num1 = 0 ; $num1 < $num_variabili ; $num1++) ${$variabile[$num_var]} = "";
if ($num_persone != "non specificato" and $num_persone != "") $num_persone_tot = (int) $num_persone + (int) $n_letti_agg;
else $num_persone_tot = "";
if ($costo_tot) $costo_tot_p = punti_in_num($costo_tot,$stile_soldi);
if ($pagato) $pagato_p = punti_in_num($pagato,$stile_soldi);
if ($sconto) $sconto_p = punti_in_num($sconto,$stile_soldi);
if ($costo_tariffa) $costo_tariffa_p = punti_in_num($costo_tariffa,$stile_soldi);
if ($commissioni) $commissioni_p = punti_in_num($commissioni,$stile_soldi);
if ($caparra) $caparra_p = punti_in_num($caparra,$stile_soldi);


if (!$condizioni_alternative) {
if (!$cond_non_ripetute_applicate or $ripeti_parte2[$n_p2] == "SI") {
$num_condizioni_corr = $num_condizioni;
if ($num_condizioni_corr) {
$condizione_vett_corr = $condizione_vett;
$num_cond_vett_corr = $num_cond_vett;
$azione_vett_corr = $azione_vett;
} # fine if ($num_condizioni_corr)
if ($ripeti_parte2[$n_p2] != "SI") $cond_non_ripetute_applicate = 1;
} # fine if (!$cond_non_ripetute_applicate or $ripeti_parte2[$n_p2] == "SI")
else $num_condizioni_corr = 0;
} # fine if (!$condizioni_alternative)

# Condizioni applicate ad ogni ripetizione di prenotazione
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_corr ; $num1++) {
$condizione = $condizione_vett_corr[$num1];
$num_se = $num_cond_vett_corr[$num1];
$azione = $azione_vett_corr[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = fixset($$var_if);
else {
$var_if = explode("(",substr($var_if,0,-1));
if (isset(${$var_if[0]}[fixset(${$var_if[1]})])) $var_if = ${$var_if[0]}[fixset(${$var_if[1]})];
else $var_if = null;
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = fixset($$val_if);
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
$var_if = (string) $var_if;
$val_if = (string) $val_if;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "!{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) == strtolower($var_if)) or ($se_cond_corr[1] == "!{A}" and str_replace($val_if,"",$var_if) == $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if (!empty($var_predef_data[$val_then]) and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr(fixstr($$val_then),$stile_data);
else $val_then = fixstr($$val_then);
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = fixstr(${$val_then[0]}[fixset(${$val_then[1]})]);
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9]) {
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
if ($azione[9] == "eht") $val_then = htmlspecialchars($val_then,ENT_QUOTES);
if ($azione[9] == "dht") $val_then = html_decod($val_then);
if ($azione[9] == "e64") $val_then = base64_encode($val_then);
if ($azione[9] == "d64") $val_then = base64_decode($val_then);
if (substr($azione[9],1,1) == "c") {
if (substr($azione[9],0,1) == "0") {
$txt_sost1 = 10;
$val_then = preg_replace("/[^0-9]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "0")
if (substr($azione[9],0,1) == "1") {
$txt_sost1 = 16;
$val_then = preg_replace("/[^0-9a-f]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "1")
if (substr($azione[9],0,1) == "2") {
$txt_sost1 = 2;
$val_then = preg_replace("/[^0-1]/","",$val_then);
} # fine if (substr($azione[9],0,1) == "2")
if (substr($azione[9],0,1) == "3") {
$txt_sost1 = 36;
$val_then = preg_replace("/[^0-9a-z]/","",strtolower($val_then));
} # fine if (substr($azione[9],0,1) == "3")
if (substr($azione[9],2,1) == "0") $txt_sost2 = 10;
if (substr($azione[9],2,1) == "1") $txt_sost2 = 16;
if (substr($azione[9],2,1) == "2") $txt_sost2 = 2;
if (substr($azione[9],2,1) == "3") $txt_sost2 = 36;
$val_then = base_convert($val_then,$txt_sost1,$txt_sost2);
} # fine if (substr($azione[9],1,1) == "c")
} # fine if ($azione[9])
} # fine if ($azione[3] == "var")
if (strcmp((string) $azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace((string) $txt_sost1,(string) $txt_sost2,(string) $val_then);
} # fine if (strcmp((string) $azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_then_orig = null;
} # fine else if (substr($azione[1],0,1) != "a")
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = fixset(${$variabile[$azione[1]]});
else {
if (isset(${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}])) $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
else $var_da_assegnare = null;
} # fine else if (substr($azione[1],0,1) != "a")
$val_if = 0;
if ($tipo_contratto == "contrhtm") {
$val_then = (string) $var_da_assegnare;
$var_da_assegnare = html_decod($var_da_assegnare);
if ($val_then != $var_da_assegnare) $val_if = 1;
} # fine if ($tipo_contratto == "contrhtm")
if (strcmp((string) $azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp((string) $azione[3],""))
$var_da_assegnare = tronca_testo($var_da_assegnare,0,$azione[2]);
if ($val_if) $var_da_assegnare = htmlspecialchars($var_da_assegnare,ENT_QUOTES);
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = fixset(${$var_da_oper});
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = fixset(${$var_da_oper[0]}[${$var_da_oper[1]}]);
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = fixset(${$var_da_oper2});
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (float) $var_da_oper + (float) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (float) $var_da_oper - (float) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (float) $var_da_oper * (float) $var_da_oper2;
if ($azione[3] == "/") {
if ((float) $var_da_oper2 != (float) 0) $var_da_assegnare = (float) $var_da_oper / (float) $var_da_oper2;
else $var_da_assegnare = (float) $var_da_oper / 0.01;
} # fine if ($azione[3] == "/")
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (float) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (float) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = (string) $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") {
if (isset($variabile[$azione[1]])) ${$variabile[$azione[1]]} = $var_da_assegnare;
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = fixstr(${$var_da_oper});
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = fixstr(${$var_da_oper[0]}[fixset(${$var_da_oper[1]})]);
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = fixset(${$var_da_oper2});
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = fixset(${$var_da_oper2[0]}[${$var_da_oper2[1]}]);
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((float) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(fixstr(${$var_arr[substr($azione[1],1)]}),""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = fixset(${$array[substr($azione[3],1)]});
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = fixset($array_date_contr[$array[substr($azione[3],1)]]);
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1


$contratto_corr = $contratto_parte2[$n_p2];
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr,2);
$contr_parziale = substr($contratto_corr,strlen($contr_vett[0]));
$contratto_corr = $contr_vett[0];
$condizione = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$cond_verificata = 0;
$cont_oper = "";
$txt_sost1 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|&/",preg_replace("/^\\[c +/","",$condizione));
for ($num1 = 0 ; $num1 < count($txt_sost1) ; $num1++) {
if ($num1) $cont_oper = "&";
$txt_sost2 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\|/",$txt_sost1[$num1]);
for ($num2 = 0 ; $num2 < count($txt_sost2) ; $num2++) {
if ($num2) $cont_oper = "|";
if ($cont_oper == "&" and !$cond_verificata) break 2;
if ($cont_oper == "|" and $cond_verificata) break 2;
$val_if = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)| *(!?=|!?%|>|<) */",$txt_sost2[$num2]);
$txt_sost2[$num2] = trim(substr($txt_sost2[$num2],strlen($val_if[0])));
$var_if = trim($val_if[0]);
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = fixset(${$parti_arr[1]});
$val_var_if = fixset(${$parti_arr[0]}[$val_var_if]);
} # fine if (str_replace("(","",$var_if) != $var_if)
else $val_var_if = fixset($$var_if);
if (substr($txt_sost2[$num2],0,2) == "!=") $cond = "!=";
else {
if (substr($txt_sost2[$num2],0,1) == ">") $cond = ">";
else {
if (substr($txt_sost2[$num2],0,1) == "<") $cond = "<";
else {
if (substr($txt_sost2[$num2],0,2) == "!%") $cond = "!%";
else {
if (substr($txt_sost2[$num2],0,1) == "%") $cond = "%";
else $cond = "=";
} # fine else if (substr($txt_sost2[$num2],0,2) == "!%")
} # fine else if (substr($txt_sost2[$num2],0,1) == "<")
} # fine else if (substr($txt_sost2[$num2],0,1) == ">")
} # fine else if (substr($txt_sost2[$num2],0,2) == "!=")
$val_if = trim($val_if[1]);
if (strstr($val_if,'"')) $val_if = str_replace("\"","",$val_if);
else {
if (str_replace("(","",$val_if) != $val_if) {
$parti_arr = explode("(",substr($val_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_if = substr($parti_arr[1],1,-1);
else $val_if = fixset(${$parti_arr[1]});
$val_if = fixset(${$parti_arr[0]}[$val_if]);
} # fine if (str_replace("(","",$val_if) != $val_if)
else $val_if = fixset($$val_if);
} # fine else if (strstr($val_if,'"'))
$cond_verificata = 0;
if ($cond == "=" and $val_var_if == $val_if) $cond_verificata = 1;
if ($cond == "!=" and $val_var_if != $val_if) $cond_verificata = 1;
if ($cond == ">" and $val_var_if > $val_if) $cond_verificata = 1;
if ($cond == "<" and $val_var_if < $val_if) $cond_verificata = 1;
if ($cond == "%" and strstr((string) $val_var_if,$val_if)) $cond_verificata = 1;
if ($cond == "!%" and !strstr((string) $val_var_if,$val_if)) $cond_verificata = 1;
} # fine for $num2
} # fine for $num1
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
#while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
#$contr_parziale = substr($contr_parziale,1);
$contr_vett = explode("[/c]",$contr_parziale,2);
$contr_parziale = substr($contr_parziale,(strlen($contr_vett[0]) + 4));
if ($cond_verificata) $contratto_corr .= $contr_vett[0];
$contratto_corr .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr))


if ($data_inizio) $data_inizio = formatta_data_contr($data_inizio,$stile_data);
if ($data_fine) $data_fine = formatta_data_contr($data_fine,$stile_data);
if ($data_nascita) $data_nascita = formatta_data_contr($data_nascita,$stile_data);
if ($data_nascita_ospite) $data_nascita_ospite = formatta_data_contr($data_nascita_ospite,$stile_data);
if ($data_inserimento_costo_agg) $data_inserimento_costo_agg = formatta_data_contr($data_inserimento_costo_agg,$stile_data);
if ($data_pagamento) $data_pagamento = formatta_data_contr($data_pagamento,$stile_data);
if ($data_operazione_pagamento) $data_operazione_pagamento = formatta_data_contr($data_operazione_pagamento,$stile_data);
if ($data_inserimento_prenotazione) $data_inserimento_prenotazione = formatta_data_contr($data_inserimento_prenotazione,$stile_data);
if ($data_creazione_documento_salvato) $data_creazione_documento_salvato = formatta_data_contr($data_creazione_documento_salvato,$stile_data);
$data_inizio_selezione = $data_inizio_selezione_f;
$data_fine_selezione = $data_fine_selezione_f;
$oggi = $oggi_f;
$testo_quotato_email_richiesta = str_replace("<email_richiesta> ","<$email> ",$testo_quotato_email_richiesta_orig);

if (!$nome) $nome = "$tag_b"."____________________$tag_no_b";
if (!$soprannome) $soprannome = "$tag_b"."____________________$tag_no_b";
if (!$cognome) $cognome = "$tag_b"."_________________$tag_no_b";
if (!$cognome2) $cognome2 = "$tag_b"."_________________$tag_no_b";
if (!$data_nascita) $data_nascita = "$tag_b"."______________$tag_no_b";
if (!$documento) $documento = " $tag_b"."________________________________$tag_no_b";
if (!$nazione) $nazione = "$tag_b"."_____________$tag_no_b";
if (!$regione) $regione = "$tag_b"."_____________$tag_no_b";
if (!$citta) $citta = "$tag_b"."___________________$tag_no_b";
if (!$via and $lingua_mex) $via2 = "$fr_via $tag_b"."________________________________$tag_no_b";
else $via2 = $via;
if (!$via) $via = "$tag_b"."________________________________$tag_no_b";
if (!$numcivico) $numcivico = "$tag_b"."_____$tag_no_b";
if (!$telefono) $telefono = "$tag_b"."__________________$tag_no_b";
if (!$telefono2) $telefono2 = "$tag_b"."__________________$tag_no_b";
if (!$telefono3) $telefono3 = "$tag_b"."__________________$tag_no_b";
if (!$fax) $fax = "$tag_b"."__________________$tag_no_b";
if (!$email) $email = "$tag_b"."__________________$tag_no_b";
if (!$email2) $email2 = "$tag_b"."__________________$tag_no_b";
if (!$email_certificata) $email_certificata = "$tag_b"."__________________$tag_no_b";
if (!$cap) $cap = "$tag_b"."________$tag_no_b";
if (!$codice_fiscale) $codice_fiscale = "$tag_b"."_____________$tag_no_b";
if (!$partita_iva) $partita_iva = "$tag_b"."_____________$tag_no_b";
if ($num_persone == "non specificato" or $num_persone == "") {
$num_persone = "$tag_b"."_____$tag_no_b";
$num_persone_tot = "$tag_b"."_____$tag_no_b";
} # fine if ($num_persone == "non specificato" or $num_persone == "")
if (!$costo_tot or !$caparra) {
$resto_caparra = "$tag_b"."_____________$tag_no_b";
$resto_caparra_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot or !$caparra)
if (!$caparra) {
$caparra = "$tag_b"."___________$tag_no_b";
$caparra_p = "$tag_b"."___________$tag_no_b";
} # fine if (!$caparra)
if (!$costo_tot or !$commissioni) {
$resto_commissioni = "$tag_b"."_____________$tag_no_b";
$resto_commissioni_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot or !$commissioni)
if (!$commissioni) {
$commissioni = "$tag_b"."___________$tag_no_b";
$commissioni_p = "$tag_b"."___________$tag_no_b";
} # fine if (!$commissioni)
if (!$costo_tot) {
$resto_da_pagare = "$tag_b"."_____________$tag_no_b";
$resto_da_pagare_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot)
if (!$data_inizio) $data_inizio = "$tag_b"."______________$tag_no_b";
if (!$data_fine) $data_fine = "$tag_b"."______________$tag_no_b";
if (!$num_periodi or $num_periodi == "?") $num_periodi = "$tag_b"."____$tag_no_b";
if ($orario_entrata_stimato) $orario_entrata_stimato = substr(str_replace("$data_inizio ","",formatta_data_contr($orario_entrata_stimato,$stile_data)),0,-3);
if (!$nome_tariffa) $nome_tariffa = "$tag_b"."____________________$tag_no_b";
if (!$costo_tariffa) {
$costo_tariffa = "$tag_b"."____________________$tag_no_b";
$costo_tariffa_p = "$tag_b"."____________________$tag_no_b";
} # fine if (!$costo_tariffa)
if (!$sconto) {
$sconto = "$tag_b"."____________$tag_no_b";
$sconto_p = "$tag_b"."____________$tag_no_b";
} # fine if (!$sconto)
if (!$commento) $commento = "$tag_b"."______________________$tag_no_b";
if (!$origine_prenotazione) $origine_prenotazione = "$tag_b"."_________________$tag_no_b";
if (!$unita_occupata) $unita_occupata = "$tag_b"."____$tag_no_b";
$appartamento = $unita_occupata;
$apartment = $unita_occupata;
$apartamento = $unita_occupata;
if (!$unita_assegnabili) $unita_assegnabili = "$tag_b"."_________________$tag_no_b";
$app_assegnabili = $unita_assegnabili;
$apartment_list = $unita_assegnabili;
$lista_apartamentos = $unita_assegnabili;
if (!$pagato) {
$pagato = "$tag_b"."_____________$tag_no_b";
$pagato_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$pagato)
if (!$costo_tot) {
$costo_tot = "$tag_b"."_____________$tag_no_b";
$costo_tot_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot)
if (!$n_letti_agg) $n_letti_agg = "$tag_b"."____$tag_no_b";
if (!$numero_prenotazione) $numero_prenotazione = "$tag_b"."____$tag_no_b";
if (!$nome_ospite) $nome_ospite = "$tag_b"."____________________$tag_no_b";
if (!$soprannome_ospite) $soprannome_ospite = "$tag_b"."____________________$tag_no_b";
if (!$cognome_ospite) $cognome_ospite = "$tag_b"."_________________$tag_no_b";
if (!$cognome2_ospite) $cognome2_ospite = "$tag_b"."_________________$tag_no_b";
if (!$data_nascita_ospite) $data_nascita_ospite = "$tag_b"."______________$tag_no_b";
if (!$documento_ospite) $documento_ospite = " $tag_b"."________________________________$tag_no_b";
if (!$nazione_ospite) $nazione_ospite = "$tag_b"."_____________$tag_no_b";
if (!$regione_ospite) $regione_ospite = "$tag_b"."_____________$tag_no_b";
if (!$citta_ospite) $citta_ospite = "$tag_b"."___________________$tag_no_b";
if (!$via_ospite and $lingua_mex) $via2_ospite = "$fr_via $tag_b"."________________________________$tag_no_b";
else $via2_ospite = $via_ospite;
if (!$via_ospite) $via_ospite = "$tag_b"."________________________________$tag_no_b";
if (!$numcivico_ospite) $numcivico_ospite = "$tag_b"."_____$tag_no_b";
if (!$telefono_ospite) $telefono_ospite = "$tag_b"."__________________$tag_no_b";
if (!$telefono2_ospite) $telefono2_ospite = "$tag_b"."__________________$tag_no_b";
if (!$telefono3_ospite) $telefono3_ospite = "$tag_b"."__________________$tag_no_b";
if (!$fax_ospite) $fax_ospite = "$tag_b"."__________________$tag_no_b";
if (!$email_ospite) $email_ospite = "$tag_b"."__________________$tag_no_b";
if (!$email2_ospite) $email2_ospite = "$tag_b"."__________________$tag_no_b";
if (!$email_certificata_ospite) $email_certificata_ospite = "$tag_b"."__________________$tag_no_b";
if (!$cap_ospite) $cap_ospite = "$tag_b"."________$tag_no_b";
if (!$codice_fiscale_ospite) $codice_fiscale_ospite = "$tag_b"."_____________$tag_no_b";
if (!$partita_iva_ospite) $partita_iva_ospite = "$tag_b"."_____________$tag_no_b";


$contratto_r = $contratto_corr;
$contr_vett = explode("[",$contratto_r);
$num_contr_vett = count($contr_vett);
$contr_parziale = "";
for ($num1 = 0 ; $num1 < $num_contr_vett ; $num1++) {
$contr_parziale .= $contr_vett[$num1]."[";
$resto_contr = substr($contratto_corr,strlen($contr_parziale));
$lettera = (string) substr($resto_contr,0,1);
if ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "") {
$num2 = 1;
$lettere_var = $lettera;
$cond = 0;
while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and ($cond != 1 or ($lettera != "]" and $lettera != "("))) {
if ($lettera == "'") {
if (!$cond) $cond = 1;
else $cond = 2;
} # fine if ($lettera == "'")
$lettera = (string) substr($resto_contr,$num2,1);
if ($lettera == "]") {
if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var) {
if ($incr_np and $lettere_var == "numero_progressivo_documento") {
if ($num_prog_contr_max and $numero_progressivo_documento > $num_prog_contr_max) $numero_progressivo_documento--;
$val_if = strstr($contratto_r,"[numero_progressivo_documento]");
$contratto_r = substr($contratto_r,0,(strlen($val_if) * -1)).$numero_progressivo_documento.substr($val_if,30);
$numero_progressivo_documento++;
} # fine if ($incr_np and $lettere_var == "numero_progressivo_documento")
else {
if (isset(${$lettere_var}) and is_array(${$lettere_var})) $contratto_r = str_replace("[".$lettere_var."]","Array",$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",fixstr(${$lettere_var}),$contratto_r);
} # fine else if ($incr_np and $lettere_var == "numero_progressivo_documento")
} # fine if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
else {
$parti_arr = explode("(",substr($lettere_var,0,-1));
if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and preg_replace("/(('[^']*')|([A-Za-z]+[A-Za-z0-9_]*))/","",$parti_arr[1]) == "") {
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = fixset(${$parti_arr[1]});
if (isset($array_date_contr[$parti_arr[0]]) and $array_date_contr[$parti_arr[0]] == "SI") $contratto_r = str_replace("[".$lettere_var."]",formatta_data_contr(${$parti_arr[0]}[$val_var_if],$stile_data),$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",fixstr(${$parti_arr[0]}[$val_var_if]),$contratto_r);
} # fine if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and...
} # fine else if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
} # fine if ($lettera == "]")
$lettere_var .= $lettera;
$num2++;
} # fine while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and...
} # fine ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "")
} # fine for $num1
$contratto_ripetizione .= $contratto_r;


$nome = $nome_orig;
$soprannome = $soprannome_orig;
$cognome = $cognome_orig;
$cognome2 = $cognome2_orig;
$data_nascita = $data_nascita_orig;
$documento = $documento_orig;
$nazione = $nazione_orig;
$regione = $regione_orig;
$citta = $citta_orig;
$via = $via_orig;
$numcivico = $numcivico_orig;
$telefono = $telefono_orig;
$telefono2 = $telefono2_orig;
$telefono3 = $telefono3_orig;
$fax = $fax_orig;
$email = $email_orig;
$email2 = $email2_orig;
$email_certificata = $email_certificata_orig;
$cap = $cap_orig;
$codice_fiscale = $codice_fiscale_orig;
$partita_iva = $partita_iva_orig;
$num_persone = $num_persone_orig;
$caparra = $caparra_orig;
$commissioni = $commissioni_orig;
$data_inizio = $data_inizio_orig;
$data_fine = $data_fine_orig;
$num_periodi = $num_periodi_orig;
$orario_entrata_stimato = $orario_entrata_stimato_orig;
$nome_tariffa = $nome_tariffa_orig;
$costo_tariffa = $costo_tariffa_orig;
$sconto = $sconto_orig;
$percentuale_tasse_tariffa = $percentuale_tasse_tariffa_orig;
$commento = $commento_orig;
$origine_prenotazione = $origine_prenotazione_orig;
$unita_occupata = $unita_occupata_orig;
$unita_assegnabili = $unita_assegnabili_orig;
$pagato = $pagato_orig;
$costo_tot = $costo_tot_orig;
$n_letti_agg = $n_letti_agg_orig;
$numero_prenotazione = $numero_prenotazione_orig;
$data_inizio_selezione = $data_inizio_selezione_orig;
$data_fine_selezione = $data_fine_selezione_orig;
$data_inserimento_prenotazione = $data_inserimento_prenotazione_orig;
$oggi = $oggi_orig;

$numero_cliente_ospite = "";
$cognome_ospite = "";

} # fine if ($mostra_ripetizione)
} # fine for $n_r2
} # fine for $n_p2


if ($prendi_sempre_ospiti) chiudi_query($ospiti);

if (empty($errore_ripetizione)) {
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
if (empty(${"allegato$num1"})) {
if ($ripeti_tutto) $apertura_rip_contr = str_replace("<b>".$dati_allegato['corr'][$num1]."</b>",$dati_allegato['corr'][$num1],str_replace(" name=\"allega$num1"."_$n_r\" checked>"," name=\"allega$num1"."_$n_r\">",$apertura_rip_contr));
else $contratto = str_replace("<b>".$dati_allegato['corr'][$num1]."</b>",$dati_allegato['corr'][$num1],str_replace(" name=\"allega$num1"."_1\" checked>"," name=\"allega$num1"."_1\">",$contratto));
} # fine if (empty(${"allegato$num1"}))
} # fine for $num1
$contratto .= $apertura_rip_contr.$contratto_ripetizione.$chiusura_rip_contr;
} # fine if (empty($errore_ripetizione))

if ($ripeti_tutto) {
if ($nome_file_downl['a'] and strcmp(trim($nome_documento_scaricato),"")) {
$nome_file_downl[$n_r] = trim(str_replace("\r","_",str_replace("\n","_",str_replace("\"","_",str_replace("'","_",str_replace("..","_",str_replace("\\","_",str_replace("/","_",$nome_documento_scaricato))))))));
$nome_file_downl['num'] = $n_r;
$dati_file_dat[$n_r]['n_downl'] = $nome_file_downl[$n_r];
if (!isset($dati_file_dat[$n_r]['nome'])) $dati_file_dat[$n_r]['ins_nome'] = 1;
} # fine if ($nome_file_downl['a'] and strcmp(trim($nome_documento_scaricato),""))
if (!empty($filecontr)) {
if ($tipo_contratto == "contrtxt") $contratto = html_decod($contratto);
if ($tipo_contratto == "contrrtf") {
$contratto = str_replace("&quot;","\"",$contratto);
$contratto = str_replace("&#039;","'",$contratto);
$contratto = str_replace("&apos;","'",$contratto);
$contratto = str_replace("&lt;","<",$contratto);
$contratto = str_replace("&gt;",">",$contratto);
$contratto = str_replace("&amp;","&",$contratto);
$contratto = str_replace("ñ","\u241\'f1",$contratto);
$contratto = str_replace("à","\u224\'e0",$contratto);
$contratto = str_replace("è","\u232\'e8",$contratto);
$contratto = str_replace("ì","\u236\'ec",$contratto);
$contratto = str_replace("ò","\u242\'f2",$contratto);
$contratto = str_replace("ù","\u249\'f9",$contratto);
$contratto = str_replace("á","\u225\'e1",$contratto);
$contratto = str_replace("é","\u233\'e9",$contratto);
$contratto = str_replace("í","\u237\'ed",$contratto);
$contratto = str_replace("ó","\u243\'f3",$contratto);
$contratto = str_replace("ú","\u250\'fa",$contratto);
$contratto = str_replace("ä","\u228\'e4",$contratto);
$contratto = str_replace("ö","\u246\'f6",$contratto);
$contratto = str_replace("ü","\u252\'fc",$contratto);
$contratto = str_replace("ß","\u223\'df",$contratto);
$contratto = str_replace("ç","\u231\'e7",$contratto);
$contratto = str_replace("ã","\u227\'e3",$contratto);
$contratto = str_replace("õ","\u245\'f5",$contratto);
$contratto = str_replace("ø","\u248\'f8",$contratto);
$contratto = str_replace("€","\u8364\'80",$contratto);
$contratto = str_replace("°","\u176\'b0",$contratto);
$contratto = str_replace("’","\u8217\'92",$contratto);
$contratto = str_replace("Ñ","\u209\'d1",$contratto);
$contratto = str_replace("À","\u192\'c0",$contratto);
$contratto = str_replace("È","\u200\'c8",$contratto);
$contratto = str_replace("Ì","\u204\'cc",$contratto);
$contratto = str_replace("Ò","\u210\'d2",$contratto);
$contratto = str_replace("Ù","\u217\'d9",$contratto);
$contratto = str_replace("Á","\u193\'c1",$contratto);
$contratto = str_replace("É","\u201\'c9",$contratto);
$contratto = str_replace("Í","\u205\'cd",$contratto);
$contratto = str_replace("Ó","\u211\'d3",$contratto);
$contratto = str_replace("Ú","\u218\'da",$contratto);
$contratto = str_replace("Ä","\u196\'c4",$contratto);
$contratto = str_replace("Ö","\u214\'d6",$contratto);
$contratto = str_replace("Ü","\u220\'dc",$contratto);
$contratto = str_replace("Ç","\u199\'c7",$contratto);
$contratto = str_replace("Ã","\u195\'c3",$contratto);
$contratto = str_replace("Õ","\u213\'d5",$contratto);
$contratto = str_replace("Ø","\u216\'d8",$contratto);
$contratto = str_replace("Α","\u913\'91",$contratto);
$contratto = str_replace("α","\u945\'b1",$contratto);
$contratto = str_replace("Β","\u914\'92",$contratto);
$contratto = str_replace("β","\u946\'b2",$contratto);
$contratto = str_replace("Γ","\u915\'93",$contratto);
$contratto = str_replace("γ","\u947\'b3",$contratto);
$contratto = str_replace("Δ","\u916\'94",$contratto);
$contratto = str_replace("δ","\u948\'b4",$contratto);
$contratto = str_replace("Ε","\u917\'95",$contratto);
$contratto = str_replace("ε","\u949\'b5",$contratto);
$contratto = str_replace("Ζ","\u918\'96",$contratto);
$contratto = str_replace("ζ","\u950\'b6",$contratto);
$contratto = str_replace("Η","\u919\'97",$contratto);
$contratto = str_replace("η","\u951\'b7",$contratto);
$contratto = str_replace("Θ","\u920\'98",$contratto);
$contratto = str_replace("θ","\u952\'b8",$contratto);
$contratto = str_replace("Ι","\u921\'99",$contratto);
$contratto = str_replace("ι","\u953\'b9",$contratto);
$contratto = str_replace("Κ","\u922\'9a",$contratto);
$contratto = str_replace("κ","\u954\'ba",$contratto);
$contratto = str_replace("Λ","\u923\'9b",$contratto);
$contratto = str_replace("λ","\u955\'bb",$contratto);
$contratto = str_replace("Μ","\u924\'9c",$contratto);
$contratto = str_replace("μ","\u956\'bc",$contratto);
$contratto = str_replace("Ν","\u925\'9d",$contratto);
$contratto = str_replace("ν","\u957\'bd",$contratto);
$contratto = str_replace("Ξ","\u926\'9e",$contratto);
$contratto = str_replace("ξ","\u958\'be",$contratto);
$contratto = str_replace("Ο","\u927\'9f",$contratto);
$contratto = str_replace("ο","\u959\'bf",$contratto);
$contratto = str_replace("Π","\u928\'a0",$contratto);
$contratto = str_replace("π","\u960\'c0",$contratto);
$contratto = str_replace("Ρ","\u929\'a1",$contratto);
$contratto = str_replace("ρ","\u961\'c1",$contratto);
$contratto = str_replace("Σ","\u931\'a3",$contratto);
$contratto = str_replace("σ","\u963\'c3",$contratto);
$contratto = str_replace("ς","\u962\'c2",$contratto);
$contratto = str_replace("Τ","\u932\'a4",$contratto);
$contratto = str_replace("τ","\u964\'c4",$contratto);
$contratto = str_replace("Υ","\u933\'a5",$contratto);
$contratto = str_replace("υ","\u965\'c5",$contratto);
$contratto = str_replace("Φ","\u934\'a6",$contratto);
$contratto = str_replace("φ","\u966\'c6",$contratto);
$contratto = str_replace("Χ","\u935\'a7",$contratto);
$contratto = str_replace("χ","\u967\'c7",$contratto);
$contratto = str_replace("Ψ","\u936\'a8",$contratto);
$contratto = str_replace("ψ","\u968\'c8",$contratto);
$contratto = str_replace("Ω","\u937\'a9",$contratto);
$contratto = str_replace("ω","\u969\'c9",$contratto);
$contratto = str_replace("Ά","\u902\'86",$contratto);
$contratto = str_replace("ά","\u940\'ce",$contratto);
$contratto = str_replace("Ό","\u908\'8c",$contratto);
$contratto = str_replace("ό","\u972\'cf",$contratto);
$contratto = str_replace("Ή","\u905\'89",$contratto);
$contratto = str_replace("ή","\u942\'ce",$contratto);
$contratto = str_replace("Ί","\u906\'8a",$contratto);
$contratto = str_replace("ί","\u943\'ce",$contratto);
$contratto = str_replace("Ύ","\u910\'8e",$contratto);
$contratto = str_replace("ύ","\u973\'cf",$contratto);
$contratto = str_replace("Ώ","\u911\'8f",$contratto);
$contratto = str_replace("ώ","\u974\'cf",$contratto);
$contratto = str_replace("Έ","\u904\'88",$contratto);
$contratto = str_replace("έ","\u941\'ce",$contratto);
$contratto = str_replace("ý","\u253\'fd",$contratto);
$contratto = str_replace("ž","\u382\'9e",$contratto);
$contratto = str_replace("ř","\u345\'3f",$contratto);
$contratto = str_replace("č","\u269\'3f",$contratto);
$contratto = str_replace("š","\u353\'9a",$contratto);
$contratto = str_replace("ě","\u283\'3f",$contratto);
$contratto = str_replace("ů","\u367\'3f",$contratto);
$contratto = str_replace("Ý","\u221\'dd",$contratto);
$contratto = str_replace("Ž","\u381\'8e",$contratto);
$contratto = str_replace("Ř","\u344\'3f",$contratto);
$contratto = str_replace("Č","\u268\'3f",$contratto);
$contratto = str_replace("Š","\u352\'8a",$contratto);
$contratto = str_replace("Ě","\u282\'3f",$contratto);
$contratto = str_replace("Ů","\u366\'3f",$contratto);
} # fine if ($tipo_contratto == "contrrtf")

if (!isset($dati_file_dat[$n_r]['nome']) and isset($dati_file_dat[$n_r]['ins_nome'])) {
$val_if = explode("_",substr($nome_file_contr[$n_r],strlen($nome_contratto)));
$val_if[0] = $nome_contratto;
$val_if[3] = "1+1_".$val_if[3];
$nome_file_contr[$n_r] = implode("_",$val_if);
$val_if = explode(".",$nome_file_contr[$n_r]);
$val_if = $val_if[(count($val_if) - 1)];
$dati_file_dat[$n_r]['nome'] = substr($nome_file_contr[$n_r],0,(-1 * (strlen($val_if) + 1)));
} # fine if (!isset($dati_file_dat[$n_r]['nome']) and isset($dati_file_dat[$n_r]['ins_nome']))

if ($incr_np and !$messaggio_di_errore) {
if ($numero_progressivo_documento > ($num_prog_contr[$n_r] + 1)) {
$val_if = $numero_progressivo_documento - 1;
for ($num1 = strlen($val_if) ; $num1 < 5 ; $num1++) $val_if = "0".$val_if;
$nome_file_contr[$n_r] = str_replace(" ","-$val_if",$nome_file_contr[$n_r]);
if (isset($dati_file_dat[$n_r]['nome'])) $dati_file_dat[$n_r]['nome'] = str_replace(" ","-$val_if",$dati_file_dat[$n_r]['nome']);
for ($num1 = ($n_r + 1) ; $num1 <= $num_ripeti ; $num1++) {
$val_if = $num_prog_contr[$num1] + $numero_progressivo_documento - 1 - $num_prog_contr[$n_r];
if (strlen($val_if) > strlen($num_prog_contr[$num1]) and strlen($val_if) <= 5) $num_prog_contr[$num1] = substr("00000",0,(strlen($val_if) - strlen($num_prog_contr[$num1]))).$num_prog_contr[$num1];
$nome_file_contr[$num1] = str_replace($num_prog_contr[$num1]." ",$val_if." ",$nome_file_contr[$num1]);
if (isset($dati_file_dat[$num1]['nome'])) $dati_file_dat[$num1]['nome'] = str_replace($num_prog_contr[$num1]." ",$val_if." ",$dati_file_dat[$num1]['nome']);
$num_prog_contr[$num1] = $val_if;
} # fine for $num1
} # fine if ($numero_progressivo_documento > ($num_prog_contr[$n_r] + 1))
else {
$nome_file_contr[$n_r] = str_replace(" ","",$nome_file_contr[$n_r]);
if (isset($dati_file_dat[$n_r]['nome'])) $dati_file_dat[$n_r]['nome'] = str_replace(" ","",$dati_file_dat[$n_r]['nome']);
} # fine else if ($numero_progressivo_documento > ($num_prog_contr[$n_r] + 1))
} # fine if ($incr_np and !$messaggio_di_errore)

if ($crea_file_dopo and !$messaggio_di_errore) {
if (!empty($nomi_contratti['compress'][$numero_contratto])) {
$nome_file_contr[$n_r] .= ".gz";
$lock_compress[$n_r] = crea_lock_file($dir_salva."/".$nome_file_contr[$n_r]);
$filecontr[$n_r] = gzopen($dir_salva."/".$nome_file_contr[$n_r],"wb9");
} # fine if 
else {
$filecontr[$n_r] = fopen($dir_salva."/".$nome_file_contr[$n_r],"w+");
flock($filecontr[$n_r],2);
} # fine else if (!empty($nomi_contratti['compress'][$numero_contratto]))
} # fine if ($crea_file_dopo and !$messaggio_di_errore)

if (!$messaggio_di_errore or !$crea_file_dopo) {
if (!empty($nomi_contratti['compress'][$numero_contratto])) {
gzwrite($filecontr[$n_r],$contratto);
gzclose($filecontr[$n_r]);
distruggi_lock_file($lock_compress[$n_r],$dir_salva."/".$nome_file_contr[$n_r]);
} # fine if (!empty($nomi_contratti['compress'][$numero_contratto]))
else {
fwrite($filecontr[$n_r],$contratto);
flock($filecontr[$n_r],3);
fclose($filecontr[$n_r]);
} # fine else if (!empty($nomi_contratti['compress'][$numero_contratto]))
if ($messaggio_di_errore) unlink($dir_salva."/".$nome_file_contr[$n_r]);
elseif (isset($dati_file_dat[$n_r]['nome'])) {
$num1 = fopen($dir_salva."/".$dati_file_dat[$n_r]['nome'].".dat","w+");
flock($num1,2);
if (isset($dati_file_dat[$n_r]['n_pren'])) fwrite($num1,"rn:".$dati_file_dat[$n_r]['n_pren']."\n");
if (isset($dati_file_dat[$n_r]['n_downl'])) fwrite($num1,"dn:".$dati_file_dat[$n_r]['n_downl']."\n");
flock($num1,3);
fclose($num1);
} # fine elseif (isset($dati_file_dat[$n_r]['nome']))
} # fine if (!$messaggio_di_errore or !$crea_file_dopo)
$contratto = "";
} # fine if (!empty($filecontr))
} # fine if ($ripeti_tutto)

} # fine if (!$ripeti_prenota_data or...
} # fine for $n_r

} # fine if ($ripeti_parte[$n_p] == "SI")


# Parti del documento non ripetute con le prenotazioni
else {

$costo_tot_somma_ripetizioni_p = punti_in_num(fixset($costo_tot_somma_ripetizioni),$stile_soldi);
$caparra_somma_ripetizioni_p = punti_in_num(fixset($caparra_somma_ripetizioni),$stile_soldi);
$resto_caparra_somma_ripetizioni_p = punti_in_num(fixset($resto_caparra_somma_ripetizioni),$stile_soldi);
$pagato_somma_ripetizioni_p = punti_in_num(fixset($pagato_somma_ripetizioni),$stile_soldi);
$resto_da_pagare_somma_ripetizioni_p = punti_in_num(fixset($resto_da_pagare_somma_ripetizioni),$stile_soldi);

if ($dir_salva and $ripeti_tutto and (!$numero_progressivo_documento or $numero_progressivo_documento < $num_prog_contr[$n_r])) $numero_progressivo_documento = $num_prog_contr[$n_r];


# Ripetizioni degli array all'interno delle parti non ripetute con le prenotazioni
unset($contratto_parte2);
if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and str_replace("[r6]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p]) {
$contratto_parte2[1] = $contratto_parte[$n_p];
$ripeti_parte2[1] = "NO";
$num_parti2_contr = 1;
} # fine if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and...
else {
$num_parti2_contr = 0;
$contratto_restante = $contratto_parte[$n_p];
while (preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or str_replace("[r6]","",$contratto_restante) != $contratto_restante) {
$contr_vett4 = preg_split("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante);
$contr_vett6 = explode("[r6]",$contratto_restante);
$l0_cv4 = strlen($contr_vett4[0]);
$l0_cv6 = strlen($contr_vett6[0]);
if ($l0_cv4 > $l0_cv6) {
$contr_vett = $contr_vett6;
$tipo_contr_vett = 6;
} # fine if ($l0_cv4 > $l0_cv6)
else {
$contr_vett = $contr_vett4;
$tipo_contr_vett = 4;
} # fine else if ($l0_cv4 > $l0_cv6)
if ($contr_vett[0] != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contr_vett[0] != "")
$contratto_restante = substr($contratto_restante,strlen($contr_vett[0]));
if ($tipo_contr_vett == 4) {
$arr_ripeti = explode("\"]",$contratto_restante,2);
$arr_ripeti = str_replace("[r4 array=\"","",$arr_ripeti[0]);
$contratto_restante = substr($contratto_restante,strlen("[r4 array=\"$arr_ripeti\"]"));
} # fine if ($tipo_contr_vett == 4)
else $contratto_restante = substr($contratto_restante,4);
$contr_vett = explode("[/r".$tipo_contr_vett."]",$contratto_restante);
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "SI";
$tipo_parte2[$num_parti2_contr] = $tipo_contr_vett;
if ($tipo_contr_vett == 4) $arr_parte2[$num_parti2_contr] = $arr_ripeti;
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 5));
} # fine while (preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or...
if ($contratto_restante != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contratto_restante;
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contratto_restante != "")
} # fine else if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and...

for ($n_p2 = 1 ; $n_p2 <= $num_parti2_contr ; $n_p2++) {
if ($ripeti_parte2[$n_p2] != "SI") $num_ripeti2 = 1;
else {
if ($tipo_parte2[$n_p2] == 4) {
if (@is_array(${$arr_parte2[$n_p2]})) {
$num_ripeti2 = count(${$arr_parte2[$n_p2]});
reset(${$arr_parte2[$n_p2]});
} # fine if (@is_array(${$arr_parte2[$n_p2]}))
else $num_ripeti2 = 0;
} # fine if ($tipo_parte2[$n_p2] == 4)
if ($tipo_parte2[$n_p2] == 6) $num_ripeti2 = $num_unita;
} # fine else if ($ripeti_parte2[$n_p2] != "SI")

for ($n_r2 = 1 ; $n_r2 <= $num_ripeti2 ; $n_r2++) {

if ($ripeti_parte2[$n_p2] == "SI") {

if ($tipo_parte2[$n_p2] == 4) {
${$var_arr_nome[$arr_parte2[$n_p2]]} = key(${$arr_parte2[$n_p2]});
next(${$arr_parte2[$n_p2]});
} # fine if ($tipo_parte2[$n_p2] == 4)

if ($tipo_parte2[$n_p2] == 6) {
$nome_unita = $dati_app_contr[$n_r2]['nome'];
$casa_unita = $dati_app_contr[$n_r2]['casa'];
$piano_unita = $dati_app_contr[$n_r2]['piano'];
$capacita_unita = $dati_app_contr[$n_r2]['capacita'];
$priorita_unita = $dati_app_contr[$n_r2]['priorita'];
$lista_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['lista']);
$mancanti_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['mancanti']);
$dettagli_inventario_unita = fixset($dati_app_contr[$n_r2]['inv']['dettagli']);
} # fine if ($tipo_parte2[$n_p2] == 6)
elseif (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
$lista_inventario_unita = "";
$mancanti_inventario_unita = "";
$dettagli_inventario_unita = "";
} # fine else if ($tipo_parte2[$n_p2] == 6)

} # fine if ($ripeti_parte2[$n_p2] == "SI")

elseif (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
$lista_inventario_unita = "";
$mancanti_inventario_unita = "";
$dettagli_inventario_unita = "";
} # fine elseif (!isset($tipo_parte0[$n_p0]) or $tipo_parte0[$n_p0] != "6")


$contratto_corr = $contratto_parte2[$n_p2];
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr,2);
$contr_parziale = substr($contratto_corr,strlen($contr_vett[0]));
$contratto_corr = $contr_vett[0];
$condizione = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$cond_verificata = 0;
$cont_oper = "";
$txt_sost1 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|&/",preg_replace("/^\\[c +/","",$condizione));
for ($num1 = 0 ; $num1 < count($txt_sost1) ; $num1++) {
if ($num1) $cont_oper = "&";
$txt_sost2 = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)|\\|/",$txt_sost1[$num1]);
for ($num2 = 0 ; $num2 < count($txt_sost2) ; $num2++) {
if ($num2) $cont_oper = "|";
if ($cont_oper == "&" and !$cond_verificata) break 2;
if ($cont_oper == "|" and $cond_verificata) break 2;
$val_if = preg_split("/(?:\"[^\"]*\")(*SKIP)(*F)| *(!?=|!?%|>|<) *\"/",$txt_sost2[$num2]);
$txt_sost2[$num2] = trim(substr($txt_sost2[$num2],strlen($val_if[0])));
$var_if = trim($val_if[0]);
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = fixset(${$parti_arr[1]});
$val_var_if = fixset(${$parti_arr[0]}[$val_var_if]);
} # fine if (str_replace("(","",$var_if) != $var_if)
else $val_var_if = fixset($$var_if);
if (substr($txt_sost2[$num2],0,2) == "!=") $cond = "!=";
else {
if (substr($txt_sost2[$num2],0,1) == ">") $cond = ">";
else {
if (substr($txt_sost2[$num2],0,1) == "<") $cond = "<";
else {
if (substr($txt_sost2[$num2],0,2) == "!%") $cond = "!%";
else {
if (substr($txt_sost2[$num2],0,1) == "%") $cond = "%";
else $cond = "=";
} # fine else if (substr($txt_sost2[$num2],0,2) == "!%")
} # fine else if (substr($txt_sost2[$num2],0,1) == "<")
} # fine else if (substr($txt_sost2[$num2],0,1) == ">")
} # fine else if (substr($txt_sost2[$num2],0,2) == "!=")
$val_if = trim($val_if[1]);
if (strstr($val_if,'"')) $val_if = str_replace("\"","",$val_if);
else {
if (str_replace("(","",$val_if) != $val_if) {
$parti_arr = explode("(",substr($val_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_if = substr($parti_arr[1],1,-1);
else $val_if = fixset(${$parti_arr[1]});
$val_if = fixset(${$parti_arr[0]}[$val_if]);
} # fine if (str_replace("(","",$val_if) != $val_if)
else $val_if = fixset($$val_if);
} # fine else if (strstr($val_if,'"'))
$cond_verificata = 0;
if ($cond == "=" and $val_var_if == $val_if) $cond_verificata = 1;
if ($cond == "!=" and $val_var_if != $val_if) $cond_verificata = 1;
if ($cond == ">" and $val_var_if > $val_if) $cond_verificata = 1;
if ($cond == "<" and $val_var_if < $val_if) $cond_verificata = 1;
if ($cond == "%" and strstr((string) $val_var_if,$val_if)) $cond_verificata = 1;
if ($cond == "!%" and !strstr((string) $val_var_if,$val_if)) $cond_verificata = 1;
} # fine for $num2
} # fine for $num1
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
#while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
#$contr_parziale = substr($contr_parziale,1);
$contr_vett = explode("[/c]",$contr_parziale,2);
$contr_parziale = substr($contr_parziale,(strlen($contr_vett[0]) + 4));
if ($cond_verificata) $contratto_corr .= $contr_vett[0];
$contratto_corr .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *(\"[^\"]*\"|[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))?) *)*\\]/",$contratto_corr))


$data_inizio_selezione = $data_inizio_selezione_f;
$data_fine_selezione = $data_fine_selezione_f;
$oggi = $oggi_f;
$testo_quotato_email_richiesta = str_replace("<email_richiesta> ","<".fixset($email)."> ",$testo_quotato_email_richiesta_orig);


$contratto_r = $contratto_corr;
$contr_vett = explode("[",$contratto_r);
$num_contr_vett = count($contr_vett);
$contr_parziale = "";
for ($num1 = 0 ; $num1 < $num_contr_vett ; $num1++) {
$contr_parziale .= $contr_vett[$num1]."[";
$resto_contr = substr($contratto_corr,strlen($contr_parziale));
$lettera = (string) substr($resto_contr,0,1);
if ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "") {
$num2 = 1;
$lettere_var = $lettera;
$cond = 0;
while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and ($cond != 1 or ($lettera != "]" and $lettera != "("))) {
if ($lettera == "'") {
if (!$cond) $cond = 1;
else $cond = 2;
} # fine if ($lettera == "'")
$lettera = (string) substr($resto_contr,$num2,1);
if ($lettera == "]") {
if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var) {
if ($incr_np and $lettere_var == "numero_progressivo_documento") {
if ($num_prog_contr_max and $numero_progressivo_documento > $num_prog_contr_max) $numero_progressivo_documento--;
$val_if = strstr($contratto_r,"[numero_progressivo_documento]");
$contratto_r = substr($contratto_r,0,(strlen($val_if) * -1)).$numero_progressivo_documento.substr($val_if,30);
$numero_progressivo_documento++;
} # fine if ($incr_np and $lettere_var == "numero_progressivo_documento")
else {
if (isset(${$lettere_var}) and is_array(${$lettere_var})) $contratto_r = str_replace("[".$lettere_var."]","Array",$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",fixstr(${$lettere_var}),$contratto_r);
} # fine else if ($incr_np and $lettere_var == "numero_progressivo_documento")
} # fine if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
else {
$parti_arr = explode("(",substr($lettere_var,0,-1));
if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and preg_replace("/(('[^']*')|([A-Za-z]+[A-Za-z0-9_]*))/","",$parti_arr[1]) == "") {
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = fixset(${$parti_arr[1]});
if (isset($array_date_contr[$parti_arr[0]]) and $array_date_contr[$parti_arr[0]] == "SI") $contratto_r = str_replace("[".$lettere_var."]",formatta_data_contr(${$parti_arr[0]}[$val_var_if],$stile_data),$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",fixstr(${$parti_arr[0]}[$val_var_if]),$contratto_r);
} # fine if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and...
} # fine else if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
} # fine if ($lettera == "]")
$lettere_var .= $lettera;
$num2++;
} # fine while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and...
} # fine ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "")
} # fine for $num1
$contratto .= $contratto_r;


$data_inizio_selezione = $data_inizio_selezione_orig;
$data_fine_selezione = $data_fine_selezione_orig;
$oggi = $oggi_orig;

} # fine for $n_r2
} # fine for $n_p2

} # fine else if ($ripeti_parte[$n_p] == "SI")

} # fine for $n_p


} # fine for $n_r0
} # fine for $n_p0


if ($tipo_contratto == "contreml") {
if (!$ripeti_tutto) {
$contratto .= "</textarea><br>
</table><table><tr><td style=\"height: 3px;\"></td></tr></table>
<hr style=\"width: 95%; margin-left: 6px; text-align: left;\">";
} # fine if (!$ripeti_tutto)
$contratto .= "&nbsp;&nbsp;".bottone_submit_contr(mex("Spedisci",$pag),"inse","","snml")."
</div></form>";
} # fine if ($tipo_contratto == "contreml")

if (!$ripeti_tutto and $nome_file_downl['a'] and strcmp(trim($nome_documento_scaricato),"")) {
$nome_file_downl[1] = trim(str_replace("\r","_",str_replace("\n","_",str_replace("\"","_",str_replace("'","_",str_replace("..","_",str_replace("\\","_",str_replace("/","_",$nome_documento_scaricato))))))));
$nome_file_downl['num'] = 1;
$dati_file_dat[1]['n_downl'] = $nome_file_downl[1];
if (!isset($dati_file_dat[1]['nome'])) $dati_file_dat[1]['ins_nome'] = 1;
} # fine if (!$ripeti_tutto and $nome_file_downl['a'] and...

if ($tipo_contratto == "contrtxt") $contratto = html_decod($contratto);
if ($tipo_contratto == "contrrtf") {
$contratto = str_replace("&quot;","\"",$contratto);
$contratto = str_replace("&#039;","'",$contratto);
$contratto = str_replace("&apos;","'",$contratto);
$contratto = str_replace("&lt;","<",$contratto);
$contratto = str_replace("&gt;",">",$contratto);
$contratto = str_replace("&amp;","&",$contratto);
$contratto = str_replace("ñ","\u241\'f1",$contratto);
$contratto = str_replace("à","\u224\'e0",$contratto);
$contratto = str_replace("è","\u232\'e8",$contratto);
$contratto = str_replace("ì","\u236\'ec",$contratto);
$contratto = str_replace("ò","\u242\'f2",$contratto);
$contratto = str_replace("ù","\u249\'f9",$contratto);
$contratto = str_replace("á","\u225\'e1",$contratto);
$contratto = str_replace("é","\u233\'e9",$contratto);
$contratto = str_replace("í","\u237\'ed",$contratto);
$contratto = str_replace("ó","\u243\'f3",$contratto);
$contratto = str_replace("ú","\u250\'fa",$contratto);
$contratto = str_replace("ä","\u228\'e4",$contratto);
$contratto = str_replace("ö","\u246\'f6",$contratto);
$contratto = str_replace("ü","\u252\'fc",$contratto);
$contratto = str_replace("ß","\u223\'df",$contratto);
$contratto = str_replace("ç","\u231\'e7",$contratto);
$contratto = str_replace("ã","\u227\'e3",$contratto);
$contratto = str_replace("õ","\u245\'f5",$contratto);
$contratto = str_replace("ø","\u248\'f8",$contratto);
$contratto = str_replace("€","\u8364\'80",$contratto);
$contratto = str_replace("°","\u176\'b0",$contratto);
$contratto = str_replace("’","\u8217\'92",$contratto);
$contratto = str_replace("Ñ","\u209\'d1",$contratto);
$contratto = str_replace("À","\u192\'c0",$contratto);
$contratto = str_replace("È","\u200\'c8",$contratto);
$contratto = str_replace("Ì","\u204\'cc",$contratto);
$contratto = str_replace("Ò","\u210\'d2",$contratto);
$contratto = str_replace("Ù","\u217\'d9",$contratto);
$contratto = str_replace("Á","\u193\'c1",$contratto);
$contratto = str_replace("É","\u201\'c9",$contratto);
$contratto = str_replace("Í","\u205\'cd",$contratto);
$contratto = str_replace("Ó","\u211\'d3",$contratto);
$contratto = str_replace("Ú","\u218\'da",$contratto);
$contratto = str_replace("Ä","\u196\'c4",$contratto);
$contratto = str_replace("Ö","\u214\'d6",$contratto);
$contratto = str_replace("Ü","\u220\'dc",$contratto);
$contratto = str_replace("Ç","\u199\'c7",$contratto);
$contratto = str_replace("Ã","\u195\'c3",$contratto);
$contratto = str_replace("Õ","\u213\'d5",$contratto);
$contratto = str_replace("Ø","\u216\'d8",$contratto);
$contratto = str_replace("Α","\u913\'91",$contratto);
$contratto = str_replace("α","\u945\'b1",$contratto);
$contratto = str_replace("Β","\u914\'92",$contratto);
$contratto = str_replace("β","\u946\'b2",$contratto);
$contratto = str_replace("Γ","\u915\'93",$contratto);
$contratto = str_replace("γ","\u947\'b3",$contratto);
$contratto = str_replace("Δ","\u916\'94",$contratto);
$contratto = str_replace("δ","\u948\'b4",$contratto);
$contratto = str_replace("Ε","\u917\'95",$contratto);
$contratto = str_replace("ε","\u949\'b5",$contratto);
$contratto = str_replace("Ζ","\u918\'96",$contratto);
$contratto = str_replace("ζ","\u950\'b6",$contratto);
$contratto = str_replace("Η","\u919\'97",$contratto);
$contratto = str_replace("η","\u951\'b7",$contratto);
$contratto = str_replace("Θ","\u920\'98",$contratto);
$contratto = str_replace("θ","\u952\'b8",$contratto);
$contratto = str_replace("Ι","\u921\'99",$contratto);
$contratto = str_replace("ι","\u953\'b9",$contratto);
$contratto = str_replace("Κ","\u922\'9a",$contratto);
$contratto = str_replace("κ","\u954\'ba",$contratto);
$contratto = str_replace("Λ","\u923\'9b",$contratto);
$contratto = str_replace("λ","\u955\'bb",$contratto);
$contratto = str_replace("Μ","\u924\'9c",$contratto);
$contratto = str_replace("μ","\u956\'bc",$contratto);
$contratto = str_replace("Ν","\u925\'9d",$contratto);
$contratto = str_replace("ν","\u957\'bd",$contratto);
$contratto = str_replace("Ξ","\u926\'9e",$contratto);
$contratto = str_replace("ξ","\u958\'be",$contratto);
$contratto = str_replace("Ο","\u927\'9f",$contratto);
$contratto = str_replace("ο","\u959\'bf",$contratto);
$contratto = str_replace("Π","\u928\'a0",$contratto);
$contratto = str_replace("π","\u960\'c0",$contratto);
$contratto = str_replace("Ρ","\u929\'a1",$contratto);
$contratto = str_replace("ρ","\u961\'c1",$contratto);
$contratto = str_replace("Σ","\u931\'a3",$contratto);
$contratto = str_replace("σ","\u963\'c3",$contratto);
$contratto = str_replace("ς","\u962\'c2",$contratto);
$contratto = str_replace("Τ","\u932\'a4",$contratto);
$contratto = str_replace("τ","\u964\'c4",$contratto);
$contratto = str_replace("Υ","\u933\'a5",$contratto);
$contratto = str_replace("υ","\u965\'c5",$contratto);
$contratto = str_replace("Φ","\u934\'a6",$contratto);
$contratto = str_replace("φ","\u966\'c6",$contratto);
$contratto = str_replace("Χ","\u935\'a7",$contratto);
$contratto = str_replace("χ","\u967\'c7",$contratto);
$contratto = str_replace("Ψ","\u936\'a8",$contratto);
$contratto = str_replace("ψ","\u968\'c8",$contratto);
$contratto = str_replace("Ω","\u937\'a9",$contratto);
$contratto = str_replace("ω","\u969\'c9",$contratto);
$contratto = str_replace("Ά","\u902\'86",$contratto);
$contratto = str_replace("ά","\u940\'ce",$contratto);
$contratto = str_replace("Ό","\u908\'8c",$contratto);
$contratto = str_replace("ό","\u972\'cf",$contratto);
$contratto = str_replace("Ή","\u905\'89",$contratto);
$contratto = str_replace("ή","\u942\'ce",$contratto);
$contratto = str_replace("Ί","\u906\'8a",$contratto);
$contratto = str_replace("ί","\u943\'ce",$contratto);
$contratto = str_replace("Ύ","\u910\'8e",$contratto);
$contratto = str_replace("ύ","\u973\'cf",$contratto);
$contratto = str_replace("Ώ","\u911\'8f",$contratto);
$contratto = str_replace("ώ","\u974\'cf",$contratto);
$contratto = str_replace("Έ","\u904\'88",$contratto);
$contratto = str_replace("έ","\u941\'ce",$contratto);
$contratto = str_replace("ý","\u253\'fd",$contratto);
$contratto = str_replace("ž","\u382\'9e",$contratto);
$contratto = str_replace("ř","\u345\'3f",$contratto);
$contratto = str_replace("č","\u269\'3f",$contratto);
$contratto = str_replace("š","\u353\'9a",$contratto);
$contratto = str_replace("ě","\u283\'3f",$contratto);
$contratto = str_replace("ů","\u367\'3f",$contratto);
$contratto = str_replace("Ý","\u221\'dd",$contratto);
$contratto = str_replace("Ž","\u381\'8e",$contratto);
$contratto = str_replace("Ř","\u344\'3f",$contratto);
$contratto = str_replace("Č","\u268\'3f",$contratto);
$contratto = str_replace("Š","\u352\'8a",$contratto);
$contratto = str_replace("Ě","\u282\'3f",$contratto);
$contratto = str_replace("Ů","\u366\'3f",$contratto);
} # fine if ($tipo_contratto == "contrrtf")


if (!empty($filecontr) and !$ripeti_tutto) {
if (!isset($dati_file_dat[1]['nome']) and isset($dati_file_dat[1]['ins_nome'])) {
$val_if = explode("_",substr($nome_file_contr[1],strlen($nome_contratto)));
$val_if[0] = $nome_contratto;
$val_if[3] = "1+1_".$val_if[3];
$nome_file_contr[1] = implode("_",$val_if);
$val_if = explode(".",$nome_file_contr[1]);
$val_if = $val_if[(count($val_if) - 1)];
$dati_file_dat[1]['nome'] = substr($nome_file_contr[1],0,(-1 * (strlen($val_if) + 1)));
} # fine if (!isset($dati_file_dat[1]['nome']) and isset($dati_file_dat[1]['ins_nome']))

if ($incr_np and !$messaggio_di_errore) {
if ($numero_progressivo_documento > ($num_prog_contr[1] + 1)) {
$val_if = $numero_progressivo_documento - 1;
for ($num1 = strlen($val_if) ; $num1 < 5 ; $num1++) $val_if = "0".$val_if;
$nome_file_contr[1] = str_replace(" ","-$val_if",$nome_file_contr[1]);
if (isset($dati_file_dat[1]['nome'])) $dati_file_dat[1]['nome'] = str_replace(" ","-$val_if",$dati_file_dat[1]['nome']);
} # fine if ($numero_progressivo_documento > ($num_prog_contr[1] + 1))
else {
$nome_file_contr[1] = str_replace(" ","",$nome_file_contr[1]);
if (isset($dati_file_dat[1]['nome'])) $dati_file_dat[1]['nome'] = str_replace(" ","",$dati_file_dat[1]['nome']);
} # fine else if ($numero_progressivo_documento > ($num_prog_contr[1] + 1))
} # fine if ($incr_np and !$messaggio_di_errore)

if ($crea_file_dopo and !$messaggio_di_errore) {
if (!empty($nomi_contratti['compress'][$numero_contratto])) {
$nome_file_contr[1] .= ".gz";
$lock_compress[1] = crea_lock_file($dir_salva."/".$nome_file_contr[1]);
$filecontr[1] = gzopen($dir_salva."/".$nome_file_contr[1],"wb9");
} # fine if (!empty($nomi_contratti['compress'][$numero_contratto]))
else {
$filecontr[1] = fopen($dir_salva."/".$nome_file_contr[1],"w+");
flock($filecontr[1],2);
} # fine else if (!empty($nomi_contratti['compress'][$numero_contratto]))
} # fine if ($crea_file_dopo and !$messaggio_di_errore)

if (!$messaggio_di_errore or !$crea_file_dopo) {
if (!empty($nomi_contratti['compress'][$numero_contratto])) {
gzwrite($filecontr[1],$contratto);
gzclose($filecontr[1]);
distruggi_lock_file($lock_compress[1],$dir_salva."/".$nome_file_contr[1]);
} # fine if (!empty($nomi_contratti['compress'][$numero_contratto]))
else {
fwrite($filecontr[1],$contratto);
flock($filecontr[1],3);
fclose($filecontr[1]);
} # fine else if (!empty($nomi_contratti['compress'][$numero_contratto]))
if ($messaggio_di_errore) unlink($dir_salva."/".$nome_file_contr[1]);
elseif (isset($dati_file_dat[1]['nome'])) {
$num1 = fopen($dir_salva."/".$dati_file_dat[1]['nome'].".dat","w+");
flock($num1,2);
if (isset($dati_file_dat[1]['n_pren'])) fwrite($num1,"rn:".$dati_file_dat[1]['n_pren']."\n");
if (isset($dati_file_dat[1]['n_downl'])) fwrite($num1,"dn:".$dati_file_dat[1]['n_downl']."\n");
flock($num1,3);
fclose($num1);
} # fine elseif (isset($dati_file_dat[1]['nome']))
} # fine if (!$messaggio_di_errore or !$crea_file_dopo)
} # fine if (!empty($filecontr) and !$ripeti_tutto)

if ($crea_file_dopo) {
flock($filelock,3);
fclose($filelock);
if (@is_file(C_DATI_PATH."/crea_contr.lock")) unlink(C_DATI_PATH."/crea_contr.lock");
} # fine if ($crea_file_dopo)

$nome_file_contr['num'] = count($nome_file_contr);
$nome_file_contr['downl'] = $nome_file_downl;

if ($messaggio_di_errore and $tipo_contratto != "contrhtm") {
$messaggio_di_errore = htmlspecialchars($messaggio_di_errore);
$messaggio_di_errore = preg_replace("/&lt;(b|em|span) +style=&quot;([a-zA-Z0-9:;#%+-_ ]*)&quot; *&gt;/i",'&lt;$1 style="$2"&gt;',$messaggio_di_errore);
$messaggio_di_errore = preg_replace("/&lt;(b|em|span)( style=\"[a-zA-Z0-9:;#%+-_ ]*\")?&gt;/i",'<$1$2>',$messaggio_di_errore);
$messaggio_di_errore = preg_replace("#&lt;/(b|em|span) *&gt;#i",'</$1>',$messaggio_di_errore);
$messaggio_di_errore = preg_replace("#&lt;br/? *&gt;#i","<br>",$messaggio_di_errore);
} # fine if ($messaggio_di_errore and $tipo_contratto != "contrhtm")


} # fine if (numlin_query($dati_contratto))

return $contratto;

} # fine function crea_contratto





function crea_messaggio_contr_salva ($nome_file_contr,$num_file_salva,$num_contr_esist,$nome_file_contr_esist,$numero_contratto,$nomi_contratti,$dir_salva,$tipo_contratto,$num_ripeti,$origine,$origine_vecchia,$lista_var_form,$mostra_headers,$anno,$id_sessione,$id_utente,$tema,$tableversioni,$tabletransazioni,$pag) {
global $PHPR_TAB_PRE,$priv_cancella_contratti;

$nome_file_contr_orig = $nome_file_contr;
$num_file_salva_orig = $num_file_salva;
if ($num_contr_esist) {
$nome_file_contr = $nome_file_contr_esist;
$num_file_salva = $nome_file_contr['num'];
} # fine if ($num_contr_esist)
$tabelle_lock = array($tableversioni,$tabletransazioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$id_transazione = crea_id_sessione("",$tableversioni,8);
esegui_query("insert into $tabletransazioni (idtransazioni,idsessione,tipo_transazione,anno,dati_transazione1,dati_transazione2,dati_transazione3,dati_transazione4,dati_transazione5,ultimo_accesso) values ('$id_transazione','$id_sessione','con_s','$anno','".aggslashdb($dir_salva)."','".aggslashdb(serialize($nome_file_contr))."','$tipo_contratto','".aggslashdb($origine)."','".aggslashdb($origine_vecchia)."','$ultimo_accesso')");
unlock_tabelle($tabelle_lock);
if ($tipo_contratto == "contrrtf") $sec_aspetta = 2;
else $sec_aspetta = 6;
$url_reload = "./$pag?id_sessione=$id_sessione&amp;anno=$anno&amp;id_transazione=$id_transazione&amp;numero_contratto=$numero_contratto";
if ($num_file_salva == 1) $target = "";
else $target = " target=\"_blank\"";
if ($mostra_headers == "NO") {
if ($num_file_salva == 1) $extra_head = "<meta http-equiv=\"refresh\" content=\"$sec_aspetta; url=$url_reload&n_file=1\">
";
global $lingua_mex,$titolo,$anno_corrente,$numconnessione,$LIKE,$show_bar;
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
} # fine if ($mostra_headers == "NO")
if ($num_contr_esist and $num_file_salva_orig == 1) echo "<div style=\"line-height: 180%\"><br>";
else echo "<div style=\"line-height: 130%\"><br>";
if ($num_contr_esist) {
if ($num_ripeti > 1) echo mex("Documenti già esistenti riguardanti queste prenotazioni",$pag).":<br>";
else echo mex("Documenti già esistenti riguardanti questa prenotazione",$pag).":<br>";
} # fine if ($num_contr_esist)
for ($num1 = 1 ; $num1 <= $num_file_salva ; $num1++) {
if ($num_contr_esist and $num_file_salva_orig == 1) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
$lista_var_form
<input type=\"hidden\" name=\"sovrascrivi\" value=\"".htmlspecialchars($nome_file_contr[$num1])."\">";
} # fine if ($num_contr_esist and $num_file_salva_orig == 1)
if (!$num_contr_esist) {
echo ucfirst(mex("documento",$pag))." ";
if ($nomi_contratti['salv'][$numero_contratto] != $nomi_contratti[$numero_contratto]) echo "\"".$nomi_contratti[$numero_contratto]."\" ";
echo mex("<span class=\"colblu\">salvato</span> come",$pag);
} # fine if (!$num_contr_esist)
echo " <b><a style=\"color: #000000;\" href=\"$url_reload&n_file=$num1\"$target>".$nome_file_contr[$num1]."</a></b>";
if ($num_contr_esist and $num_file_salva_orig == 1 and $priv_cancella_contratti != "n") {
echo " ".bottone_submit_contr(mex("Sovrascrivi",$pag),"","","xdoc")."
.</div></form>";
} # fine if ($num_contr_esist and $num_file_salva_orig == 1 and...
else echo ".<br>";
} # fine for $num1
echo "<br></div>";
if (!$num_contr_esist) {
if ($priv_cancella_contratti != "n") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numero_contratto\" value=\"$numero_contratto\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<input type=\"hidden\" name=\"cancella\" value=\"SI\">";
if ($num_file_salva == 1) echo "&nbsp;".bottone_submit_contr(mex("Cancella il documento",$pag),"","","cdoc");
else echo bottone_submit_contr(mex("Cancella i documenti",$pag),"","","cdoc");
echo "</div></form><br>";
} # fine if ($priv_cancella_contratti != "n")
} # fine if (!$num_contr_esist)
else {
echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
$lista_var_form
<input type=\"hidden\" name=\"cont_salva\" value=\"SI\">";
if ($num_file_salva_orig == 1) echo "&nbsp;".bottone_submit_contr(mex("Salva un nuovo documento",$pag),"","","adoc");
else echo bottone_submit_contr(mex("Salva dei nuovi documenti",$pag),"","","adoc");
echo "</div></form><br>";
} # fine else if (!$num_contr_esist)

} # fine function crea_messaggio_contr_salva





?>
