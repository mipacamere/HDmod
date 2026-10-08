<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2023 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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





$num_allegati_max = 30;
$trad_var = array();

function mexv ($messaggio) {
global $trad_var,$lingua_mex;
if (empty($trad_var)) {
if ($lingua_mex != "ita") include("./includes/lang/$lingua_mex/visualizza_contratto_var.php");
if (empty($trad_var['commento_personalizzato'])) $trad_var['commento_personalizzato'] = 'commento_personalizzato';
if (empty($trad_var['campo_personalizzato'])) $trad_var['campo_personalizzato'] = 'campo_personalizzato';
if (empty($trad_var['num_persone_tipo'])) $trad_var['num_persone_tipo'] = 'num_persone_tipo';
if (empty($trad_var['allegato'])) $trad_var['allegato'] = 'allegato';
} # fine if (empty($trad_var))
if (!empty($trad_var[$messaggio])) $messaggio = $trad_var[$messaggio];
else {
if (substr($messaggio,-1) == ")") {
$mess_vett = explode("(",substr($messaggio,0,-1));
if (!empty($trad_var[$mess_vett[1]])) $messaggio = $mess_vett[0]."(".$trad_var[$mess_vett[1]].")";
else $messaggio = $mess_vett[0]."(".mexv($mess_vett[1]).")";
} # fine if (substr($messaggio,-1) == ")")
else {
if (substr($messaggio,0,8) == "allegato" and preg_replace("/[0-9]/","",substr($messaggio,8)) == "") $messaggio = $trad_var['allegato'].substr($messaggio,8);
else {
if (substr($messaggio,0,24) == "commento_personalizzato_") $messaggio = $trad_var['commento_personalizzato'].substr($messaggio,23);
else {
if (substr($messaggio,0,21) == "campo_personalizzato_") $messaggio = $trad_var['campo_personalizzato'].substr($messaggio,20);
elseif (substr($messaggio,0,17) == "num_persone_tipo_") $messaggio = $trad_var['num_persone_tipo'].substr($messaggio,16);
} # fine else if (substr($messaggio,0,24) == "commento_personalizzato_")
} # fine else if (substr($messaggio,0,8) == "allegato" amd...
} # fine else if (substr($messaggio,-1) == ")")
} # fine else if ($trad_var[$messaggio])
return $messaggio;
} # fine function mexv




function elimina_array_pers ($arr_pers_elimina,$num_contr,$tablecontratti) {
global $LIKE;
$arr_pers_elimina = aggslashdb($arr_pers_elimina);
$continua = "SI";
if (substr($arr_pers_elimina,0,1) != "a") $continua = "NO";
$arr_pers_elimina = substr($arr_pers_elimina,1);
if (controlla_num_pos($arr_pers_elimina) != "SI") $continua = "NO";
if ($continua == "SI") {
if ($num_contr) $n_arr_pers_el = esegui_query("select * from $tablecontratti where numero = '$arr_pers_elimina' and tipo = 'vett".(int) $num_contr."'");
else $n_arr_pers_el = esegui_query("select * from $tablecontratti where numero = '$arr_pers_elimina' and tipo $LIKE 'vett%'");
if (numlin_query($n_arr_pers_el) != 1) $continua = "NO";
else {
$n_arr_pers_el = explode(";",risul_query($n_arr_pers_el,0,"testo"));
$n_arr_pers_el = $n_arr_pers_el[0]."(".$n_arr_pers_el[1].")";
$arr_pers_el = "a".$arr_pers_elimina;
if ($num_contr) $condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond".(int) $num_contr."' order by numero");
else $condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
$num_cond_canc = 0;
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione = explode("#@?",$condizione);
$elimina_cond_corr = "NO";
if ($condizione[0] == "rar$arr_pers_elimina") $elimina_cond_corr = "SI";
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($se_cond_corr[0] == $n_arr_pers_el) $elimina_cond_corr = "SI";
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $n_arr_pers_el) $elimina_cond_corr = "SI";
} # fine for $num2
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and ($azione[1] == $arr_pers_el or ($azione[3] == "var" and $azione[4] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "set" and (($azione[5] == "var" and $azione[6] == $n_arr_pers_el) or ($azione[7] == "var" and $azione[8] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "trunc" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($azione[0] == "oper" and ($azione[1] == $arr_pers_el or $azione[2] == $n_arr_pers_el or ($azione[4] == "var" and $azione[5] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "unset" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($azione[0] == "array" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($elimina_cond_corr == "SI") {
$num_cond = risul_query($condizioni,$num1,'numero');
$tipo_cond = risul_query($condizioni,$num1,'tipo');
esegui_query("delete from $tablecontratti where numero = '$num_cond' and tipo = '$tipo_cond' ");
$num_cond_canc++;
} # fine ($elimina_cond_corr == "SI")
} # fine for $num1
esegui_query("delete from $tablecontratti where numero = '$arr_pers_elimina' and tipo $LIKE 'vett%'");
if ($num_cond_canc) echo mex("Condizioni eliminate",'modifica_var_contr.php').": <em>$num_cond_canc</em>.<br><br>";
echo mex("Array personalizzato eliminato",'modifica_var_contr.php').".<br><br>";
} # fine else if (numlin_query($n_var_pers_el) != 1)
} # fine if ($continua == "SI")
return $continua;
} # fine function elimina_array_pers




function elimina_cond_variabile_pers ($nome_var_pers,$numero_var_pers,$num_contr,$tablecontratti) {
global $LIKE;
if ($num_contr) $condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond".(int) $num_contr."' order by numero");
else $condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
$num_cond_canc = 0;
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione = explode("#@?",$condizione);
$elimina_cond_corr = "NO";
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($se_cond_corr[0] == $nome_var_pers) $elimina_cond_corr = "SI";
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $nome_var_pers) $elimina_cond_corr = "SI";
} # fine for $num2
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and ($azione[1] == $numero_var_pers or ($azione[3] == "var" and $azione[4] == $nome_var_pers))) $elimina_cond_corr = "SI";
if ($azione[0] == "set" and (($azione[5] == "var" and $azione[6] == $nome_var_pers) or ($azione[7] == "var" and $azione[8] == $nome_var_pers))) $elimina_cond_corr = "SI";
if ($azione[0] == "trunc" and $azione[1] == $numero_var_pers) $elimina_cond_corr = "SI";
if ($azione[0] == "oper" and ($azione[1] == $numero_var_pers or $azione[2] == $nome_var_pers or ($azione[4] == "var" and $azione[5] == $nome_var_pers))) $elimina_cond_corr = "SI";
if ($azione[0] == "date" and ($azione[1] == $numero_var_pers or $azione[2] == $nome_var_pers)) $elimina_cond_corr = "SI";
if ($azione[0] == "opdat" and ($azione[1] == $numero_var_pers or $azione[3] == $nome_var_pers or $azione[4] == $nome_var_pers)) $elimina_cond_corr = "SI";
if ($elimina_cond_corr == "SI") {
$num_cond = risul_query($condizioni,$num1,'numero');
$tipo_cond = risul_query($condizioni,$num1,'tipo');
esegui_query("delete from $tablecontratti where numero = '$num_cond' and tipo = '$tipo_cond' ");
$num_cond_canc++;
} # fine ($elimina_cond_corr == "SI")
} # fine for $num1
if ($num_contr) $array = esegui_query("select * from $tablecontratti where tipo = 'vett".(int) $num_contr."' and testo $LIKE '%;$nome_var_pers%' ");
else $array = esegui_query("select * from $tablecontratti where tipo $LIKE 'vett%' and testo $LIKE '%;$nome_var_pers%' ");
$num_array = numlin_query($array);
for ($num1 = 0 ; $num1 < $num_array ; $num1++) {
$var_arr = explode(";",risul_query($array,$num1,'testo'));
$var_arr = $var_arr[1];
if ($var_arr == $nome_var_pers) {
$num_arr = risul_query($array,$num1,'numero');
elimina_array_pers("a".$num_arr,$num_contr,$tablecontratti);
} # fine if ($var_arr == $nome_var_pers)
} # fine for $num1
if ($num_cond_canc) echo mex("Condizioni eliminate",'modifica_var_contr.php').": <em>$num_cond_canc</em>.<br><br>";
return $num_cond_canc;
} # fine function elimina_cond_variabile_pers




function rinomina_testo_variabile_pers ($v_nome,$n_nome,$testo,$rinomina_var_arr=0) {
$v_nome = trim(fixstr($v_nome));
$n_nome = trim(fixstr($n_nome));
if ($v_nome and $v_nome != $n_nome) {
$num_v_nomi = 1;
$v_nomi = array(0 => $v_nome);
$n_nomi = array(0 => $n_nome);

if (substr($v_nome,-1) == ")") {
$v_nome_arr = explode("(",$v_nome);
$v_nome_var_arr = substr($v_nome_arr[1],0,-1);
$v_nome_arr = $v_nome_arr[0];
$n_nome_arr = explode("(",$n_nome);
$n_nome_var_arr = substr($n_nome_arr[1],0,-1);
$n_nome_arr = $n_nome_arr[0];
if ($v_nome_arr and $v_nome_arr != $n_nome_arr) {
$testo = str_replace("[r4 array=\"$v_nome_arr\"]","[r4 array=\"$n_nome_arr\"]",$testo);
$testo = preg_replace("/\\[$v_nome_arr\\('([^'\\]\\(]*)'\\)\\]/","[$n_nome_arr('$1')]",$testo);
while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome_arr\\(('[^'\\]\\(]*')\\) *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo)) {
$testo = preg_replace("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome_arr\\(('[^'\\]\\(]*')\\) *(!?=|!?%|>|<) *(\"[^\"]*\")( *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/","[c $1$n_nome_arr($9)$10$11$12]",$testo);
# Numeri parentesi:           12                      3   45               6                               7                          8                         9                   0              1           2  34                              5   67               8                               9
} # fine while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome_arr\\(('[^'\\]\\(]*')\\) *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo))
/*$testo = preg_replace("/\\[c +$v_nome_arr\\(\\'/","[c $n_nome_arr('",$testo);
$testo = preg_replace("/\" *& *$v_nome_arr\\(\\'/","\" & $n_nome_arr('",$testo);
$testo = preg_replace("/\" *\\| *$v_nome_arr\\('/","\" | $n_nome_arr('",$testo);*/
} # fine if ($v_nome_arr and $v_nome_arr != $n_nome_arr)
if ($v_nome_var_arr == $n_nome_var_arr) {
global $lingua_mex;
if (is_dir("./includes/lang/en")) {
$trad_var = array();
include("./includes/lang/en/visualizza_contratto_var.php");
if (!empty($trad_var[$v_nome_var_arr])) {
$v_nomi[$num_v_nomi] = $v_nome_arr."(".$trad_var[$v_nome_var_arr].")";
$n_nomi[$num_v_nomi] = $n_nome_arr."(".$trad_var[$n_nome_var_arr].")";
$num_v_nomi++;
} # fine if (!empty($trad_var[$v_nome_var_arr]))
} # fine if (is_dir("./includes/lang/en"))
if (is_dir("./includes/lang/es")) {
$trad_var = array();
include("./includes/lang/es/visualizza_contratto_var.php");
if (!empty($trad_var[$v_nome_var_arr])) {
$v_nomi[$num_v_nomi] = $v_nome_arr."(".$trad_var[$v_nome_var_arr].")";
$n_nomi[$num_v_nomi] = $n_nome_arr."(".$trad_var[$n_nome_var_arr].")";
$num_v_nomi++;
} # fine if (!empty($trad_var[$v_nome_var_arr]))
} # fine if (is_dir("./includes/lang/es"))
if (!empty($lingua_mex) and $lingua_mex != 'ita' and $lingua_mex != 'en' and $lingua_mex != 'es' and is_dir("./includes/lang/$lingua_mex") and is_file("./includes/lang/$lingua_mex/visualizza_contratto_var.php")) {
$trad_var = array();
include("./includes/lang/$lingua_mex/visualizza_contratto_var.php");
if (!empty($trad_var[$v_nome_var_arr])) {
$v_nomi[$num_v_nomi] = $v_nome_arr."(".$trad_var[$v_nome_var_arr].")";
$n_nomi[$num_v_nomi] = $n_nome_arr."(".$trad_var[$n_nome_var_arr].")";
$num_v_nomi++;
} # fine if (!empty($trad_var[$v_nome_var_arr]))
} # fine if (!empty($lingua_mex) and $lingua_mex != 'ita' and...
} # fine if ($v_nome_var_arr == $n_nome_var_arr)
} # fine if (substr($v_nome,-1) == ")")
elseif ($rinomina_var_arr) {
$testo = preg_replace("/\\[([A-Za-z]+[A-Za-z0-9_]*)\\($v_nome\\)\\]/","[$1($n_nome)]",$testo);
while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)([A-Za-z]+[A-Za-z0-9_]*)\\($v_nome\\) *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo)) {
$testo = preg_replace("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)([A-Za-z]+[A-Za-z0-9_]*)\\($v_nome\\) *(!?=|!?%|>|<) *(\"[^\"]*\")( *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/","[c $1$9($n_nome)$10$11$12]",$testo);
# Numeri parentesi:           12                      3   45               6                               7                          8           9                                      0              1           2  34                              5   67               8                               9
} # fine while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome_arr\\(('[^'\\]\\(]*')\\) *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo))
} # fine elseif ($rinomina_var_arr)

for ($num1 = 0 ; $num1 < $num_v_nomi ; $num1++) {
$v_nome = $v_nomi[$num1];
$n_nome = $n_nomi[$num1];
$testo = str_replace("[$v_nome]","[$n_nome]",$testo);
$v_nome = str_replace(")","\\)",str_replace("(","\\(",$v_nome));
while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo)) {
$testo = preg_replace("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome *(!?=|!?%|>|<) *(\"[^\"]*\")( *((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/","[c $1$n_nome$9$10$11]",$testo);
# Numeri parentesi:           12                      3   45               6                               7                          8                    9              0           1  23                              4   56               7                               8
} # fine while (preg_match("/\\[c +(([A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *(\\||&) *)*)$v_nome *(!?=|!?%|>|<) *(\"[^\"]*\") *(((\\||&) *[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *(!?=|!?%|>|<) *\"[^\"]*\" *)*)\\]/",$testo))
/*$testo = preg_replace("/\\[c +$v_nome *= *\"/","[c $n_nome=\"",$testo);
$testo = preg_replace("/\\[c +$v_nome *!= *\"/","[c $n_nome!=\"",$testo);
$testo = preg_replace("/\\[c +$v_nome *% *\"/","[c $n_nome%\"",$testo);
$testo = preg_replace("/\\[c +$v_nome *!% *\"/","[c $n_nome!%\"",$testo);
$testo = preg_replace("/\\[c +$v_nome *> *\"/","[c $n_nome>\"",$testo);
$testo = preg_replace("/\\[c +$v_nome *< *\"/","[c $n_nome<\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *= *\"/","\" & $n_nome=\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *!= *\"/","\" & $n_nome!=\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *% *\"/","\" & $n_nome%\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *!% *\"/","\" & $n_nome!%\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *> *\"/","\" & $n_nome>\"",$testo);
$testo = preg_replace("/\" *& *$v_nome *< *\"/","\" & $n_nome<\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *= *\"/","\" | $n_nome=\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *!= *\"/","\" | $n_nome!=\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *% *\"/","\" | $n_nome%\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *!% *\"/","\" | $n_nome!%\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *> *\"/","\" | $n_nome>\"",$testo);
$testo = preg_replace("/\" *\\| *$v_nome *< *\"/","\" | $n_nome<\"",$testo);*/
} # fine for $num1

} # fine if ($v_nome and $v_nome != $n_nome)
return($testo);
} # fine function rinomina_testo_variabile_pers




function rinomina_cond_variabile_pers ($nome_var_pers,$numero_var_pers,$n_nome_var_pers,$n_numero_var_pers,$num_contr,$tablecontratti) {
global $LIKE;
if ($num_contr) $condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond".(int) $num_contr."' order by numero");
else $condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
$num_cond_rinom = 0;
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione_orig = $condizione;
$condizione = explode("#@?",$condizione);
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($se_cond_corr[0] == $nome_var_pers) $se_cond_corr[0] = $n_nome_var_pers;
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $nome_var_pers) $se_cond_corr[3] = $n_nome_var_pers;
$se_cond[$num2] = implode("#%?",$se_cond_corr);
} # fine for $num2
$condizione[1] = implode("#$?",$se_cond);
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and $azione[1] == $numero_var_pers) $azione[1] = $n_numero_var_pers;
if ($azione[0] == "set" and $azione[3] == "var" and $azione[4] == $nome_var_pers) $azione[4] = $n_nome_var_pers;
if ($azione[0] == "set" and $azione[5] == "var" and $azione[6] == $nome_var_pers) $azione[6] = $n_nome_var_pers;
if ($azione[0] == "set" and $azione[7] == "var" and $azione[8] == $nome_var_pers) $azione[8] = $n_nome_var_pers;
if ($azione[0] == "trunc" and $azione[1] == $numero_var_pers) $azione[1] = $n_numero_var_pers;
if ($azione[0] == "oper" and $azione[1] == $numero_var_pers) $azione[1] = $n_numero_var_pers;
if ($azione[0] == "oper" and $azione[2] == $nome_var_pers) $azione[2] = $n_nome_var_pers;
if ($azione[0] == "oper" and $azione[4] == "var" and $azione[5] == $nome_var_pers) $azione[5] = $n_nome_var_pers;
if ($azione[0] == "date" and $azione[1] == $numero_var_pers) $azione[1] = $n_numero_var_pers;
if ($azione[0] == "date" and $azione[2] == $nome_var_pers) $azione[2] = $n_nome_var_pers;
if ($azione[0] == "opdat" and $azione[1] == $numero_var_pers) $azione[1] = $n_numero_var_pers;
if ($azione[0] == "opdat" and $azione[3] == $nome_var_pers) $azione[3] = $n_nome_var_pers;
if ($azione[0] == "opdat" and $azione[4] == $nome_var_pers) $azione[4] = $n_nome_var_pers;
$condizione[2] = implode("#%?",$azione);
$condizione = implode("#@?",$condizione);
if ($condizione_orig != $condizione) {
$num_cond = risul_query($condizioni,$num1,'numero');
$tipo_cond = risul_query($condizioni,$num1,'tipo');
esegui_query("update $tablecontratti set testo = '".aggslashdb($condizione)."' where numero = '$num_cond' and tipo = '$tipo_cond' ");
$num_cond_rinom++;
} # fine if ($condizione_orig != $condizione)
} # fine for $num1

if ($num_contr) $array = esegui_query("select * from $tablecontratti where tipo = 'vett".(int) $num_contr."' and testo $LIKE '%;$nome_var_pers%' ");
else $array = esegui_query("select * from $tablecontratti where tipo $LIKE 'vett%' and testo $LIKE '%;$nome_var_pers%' ");
$num_array = numlin_query($array);
for ($num1 = 0 ; $num1 < $num_array ; $num1++) {
$var_arr = explode(";",risul_query($array,$num1,'testo'));
if ($var_arr[1] == $nome_var_pers) {
$num_arr = risul_query($array,$num1,'numero');
$nome_arr_pers = $var_arr[0]."(".$var_arr[1].")";
$n_nome_arr_pers = $var_arr[0]."($n_nome_var_pers)";
$var_arr[1] = $n_nome_var_pers;
$var_arr = implode(";",$var_arr);
esegui_query("update $tablecontratti set testo = '".aggslashdb($var_arr)."' where numero = '$num_arr' and tipo $LIKE 'vett%'");
if ($num_contr) $condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond".(int) $num_contr."' order by numero");
else $condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
for ($num2 = 0 ; $num2 < $num_condizioni ; $num2++) {
$condizione = risul_query($condizioni,$num2,'testo');
$condizione_orig = $condizione;
$condizione = explode("#@?",$condizione);
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num3 = 1 ; $num3 < $num_se_cond ; $num3++) {
$se_cond_corr = explode("#%?",$se_cond[$num3]);
if ($se_cond_corr[0] == $nome_arr_pers) $se_cond_corr[0] = $n_nome_arr_pers;
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $nome_arr_pers) $se_cond_corr[3] = $n_nome_arr_pers;
$se_cond[$num3] = implode("#%?",$se_cond_corr);
} # fine for $num3
$condizione[1] = implode("#$?",$se_cond);
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and $azione[3] == "var" and $azione[4] == $nome_arr_pers) $azione[4] = $n_nome_arr_pers;
if ($azione[0] == "set" and $azione[5] == "var" and $azione[6] == $nome_arr_pers) $azione[6] = $n_nome_arr_pers;
if ($azione[0] == "set" and $azione[7] == "var" and $azione[8] == $nome_arr_pers) $azione[8] = $n_nome_arr_pers;
if ($azione[0] == "oper" and $azione[2] == $nome_arr_pers) $azione[2] = $n_nome_arr_pers;
if ($azione[0] == "oper" and $azione[4] == "var" and $azione[5] == $nome_arr_pers) $azione[5] = $n_nome_arr_pers;
$condizione[2] = implode("#%?",$azione);
$condizione = implode("#@?",$condizione);
if ($condizione_orig != $condizione) {
$num_cond = risul_query($condizioni,$num2,'numero');
$tipo_cond = risul_query($condizioni,$num2,'tipo');
esegui_query("update $tablecontratti set testo = '".aggslashdb($condizione)."' where numero = '$num_cond' and tipo = '$tipo_cond' ");
$num_cond_rinom++;
} # fine if ($condizione_orig != $condizione)
} # fine for $num2
} # fine if ($var_arr[1] == $nome_var_pers)
} # fine for $num1

return $num_cond_rinom;
} # fine function rinomina_cond_variabile_pers





?>