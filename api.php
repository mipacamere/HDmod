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

$pag = "api.php";
$titolo = "HotelDruid: API";

$var_pag = array();
$var_pag[0] = 'doc';
$var_pag[1] = 'pass';
$var_pag[2] = 'res_year';
$var_pag[3] = 'res_num';
$var_pag[4] = 'res_from';
$var_pag[5] = 'res_to';
$var_pag[6] = 'res_ins_from';
$var_pag[7] = 'res_ins_to';
$var_pag[8] = 'res_arr';
$var_pag[9] = 'res_dep';
$var_pag[10] = 'clients';
$var_pag[11] = 'cashbox';
$var_pag[12] = 'session_data';
$var_pag[13] = 'cli_id';
$var_pag[14] = 'priv_r';
$var_pag[15] = 'azione';
$var_pag[16] = 'tipo_ic';
$var_pag[17] = 'valore_xml';
$var_pag[18] = 'token';
$var_pag[19] = 'ord_ic';
$var_pag[20] = 'tweb';
$var_pag[21] = 'del';
$n_var_pag = 22;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/funzioni_email.php");
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$tableanni = $PHPR_TAB_PRE."anni";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tabletransazioniweb = $PHPR_TAB_PRE."transazioniweb";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableversioni = $PHPR_TAB_PRE."versioni";
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";
$tablecache = $PHPR_TAB_PRE."cache";
$tablecasse = $PHPR_TAB_PRE."casse";





# API Documenti
if (!empty($doc)) {
$session_data = "";
$id_sessione = "";
$numero_contratto = aggslashdb((int) $doc);
$api_esistente = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'api'");
if (numlin_query($api_esistente)) {
$pass_api = risul_query($api_esistente,0,'testo');
$id_utente = explode(";",$pass_api);
$indirizzi_ip_api = $id_utente[1];
$id_utente = $id_utente[0];
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$REMOTE_ADDR = aggslashdb($REMOTE_ADDR);
if (!$indirizzi_ip_api or strstr(",$indirizzi_ip_api,",",$REMOTE_ADDR,")) {
$max_log_sbagliati = 10;
$minuti_durata_sessione = 30;
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioniweb where tipo_transazione = 'api_l' and ultimo_accesso <= '$limite_transazioni_vecchie'");
$login_sbagliati = esegui_query("select tipo_transazione from $tabletransazioniweb where tipo_transazione = 'api_l' and dati_transazione1 = 'doc$numero_contratto' and dati_transazione2 = '$REMOTE_ADDR' ");
$login_sbagliati = numlin_query($login_sbagliati);
if ($login_sbagliati >= $max_log_sbagliati) echo "Excessive number of failed logins";
else {

if (@get_magic_quotes_gpc()) $pass = stripslashes(fixstr($pass));
$pass_api = substr($pass_api,(strlen($id_utente) + strlen($indirizzi_ip_api) + 2));
if (fixset($pass) == $pass_api) {

$raggiunto_limite = 0;
if (defined('C_LIMITE_CHIAMATE_API_AL_MINUTO') and C_LIMITE_CHIAMATE_API_AL_MINUTO > 0) {
$tabelle_lock = array($tablecache);
$tabelle_lock = lock_tabelle($tabelle_lock);
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$minuto = substr($adesso,0,16);
esegui_query("delete from $tablecache where tipo = 'lim_api' and datainserimento < '$minuto:00' ");
$lim_api = esegui_query("select * from $tablecache where tipo = 'lim_api' and datainserimento >= '$minuto:00' ");
if (!numlin_query($lim_api)) esegui_query("insert into $tablecache (numero,tipo,data_modifica,datainserimento) values ('1','lim_api','$adesso','$adesso') ");
else {
$num_lim_api = risul_query($lim_api,0,'numero');
if ($num_lim_api >= C_LIMITE_CHIAMATE_API_AL_MINUTO) $raggiunto_limite = 1;
else esegui_query("update $tablecache set numero = '".($num_lim_api + 1)."', data_modifica = '$adesso' where tipo = 'lim_api' and datainserimento >= '$minuto:00' ");
} # fine else if (!numlin_query($lim_api))
unlock_tabelle($tabelle_lock);
} # fine if (defined('C_LIMITE_CHIAMATE_API_AL_MINUTO') and C_LIMITE_CHIAMATE_API_AL_MINUTO > 0)
if ($raggiunto_limite) echo "REACHED LIMIT IN LAST MINUTE (".C_LIMITE_CHIAMATE_API_AL_MINUTO.").\n";
else {


if (!empty($res_year)) {
if (preg_match("/[0-9]{4,4}/",$res_year)) {
if (@is_file(C_DATI_PATH."/selectperiodi$res_year.1.php")) $anno = $res_year;
else {
if (@is_file(C_DATI_PATH."/selectperiodi".($res_year - 1).".1.php")) $anno = $res_year - 1;
else $res_year = "";
} # fine else if (@is_file(C_DATI_PATH."/selectperiodi$res_year.1.php"))
} # fine if (preg_match("/[0-9]{4,4}/",$res_year))
else $res_year = "";
} # fine if (!empty($res_year))

$anno_esist = 1;
# Se ci troviamo dopo il periodo di creazione dell'anno ma questo non è stato ancora creato
if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php")) {
$anno_prec = $anno - 1;
if (@is_file(C_DATI_PATH."/selectperiodi$anno_prec.1.php")) $anno = $anno_prec;
else $anno_esist = 0;
} # fine if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php"))
if ($anno_esist) {
$tariffe_consentite_vett = array();

$tablebeniinventario = $PHPR_TAB_PRE."beniinventario";
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;


if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
$prendi_gruppi = "";
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
$priv_inventario = risul_query($privilegi_globali_utente,0,'priv_inventario');
$priv_vedi_beni_inv = substr($priv_inventario,0,1);
$priv_vedi_inv_app = substr($priv_inventario,6,1);
if ($priv_vedi_beni_inv == "g" or $priv_vedi_inv_app == "g") $prendi_gruppi = "SI";
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
if ($priv_vedi_tab_mesi == "q" or $priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_costi = substr($priv_vedi_tab,2,1);
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if ($priv_vedi_tab_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
if ($priv_vedi_tab_appartamenti == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_doc = substr($priv_vedi_tab,7,1);
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app = substr($priv_ins_prenota,1,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_assegnazione_app = substr($priv_mod_prenota,2,1);
$priv_mod_tariffa = substr($priv_mod_prenota,3,1);
$priv_mod_sconto = substr($priv_mod_prenota,6,1);
$priv_mod_caparra = substr($priv_mod_prenota,7,1);
$priv_mod_costi_agg = substr($priv_mod_prenota,8,1);
$priv_mod_utente_ins = substr($priv_mod_prenota,9,1);
$priv_mod_pagato = substr($priv_mod_prenota,10,1);
$priv_mod_codice = substr($priv_mod_prenota,21,1);
$priv_vedi_commento = substr($priv_mod_prenota,25,1);
$priv_vedi_commenti_pers = substr($priv_mod_prenota,26,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$priv_vedi_telefoni = substr($priv_ins_clienti,3,1);
$priv_vedi_indirizzo = substr($priv_ins_clienti,4,1);
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
if ($attiva_regole1_consentite != "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
$tariffe_consentite = risul_query($privilegi_annuali_utente,0,'tariffe_consentite');
$attiva_tariffe_consentite = substr($tariffe_consentite,0,1);
if ($attiva_tariffe_consentite == "s") {
$tariffe_consentite = explode(",",substr($tariffe_consentite,2));
for ($num1 = 0 ; $num1 < count($tariffe_consentite) ; $num1++) if ($tariffe_consentite[$num1]) $tariffe_consentite_vett[$tariffe_consentite[$num1]] = "SI";
} # fine if ($attiva_tariffe_consentite == "s")
unset($tariffe_consentite);
$casse_consentite = risul_query($privilegi_globali_utente,0,'casse_consentite');
$attiva_casse_consentite = substr($casse_consentite,0,1);
if ($attiva_casse_consentite == "s") {
$casse_consentite = explode(",",substr($casse_consentite,2));
unset($casse_consentite_vett);
for ($num1 = 0 ; $num1 < count($casse_consentite) ; $num1++) if ($casse_consentite[$num1]) $casse_consentite_vett[$casse_consentite[$num1]] = "SI";
} # fine if ($attiva_casse_consentite == "s")
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
$contratti_consentiti_vett = array();
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
if ($contratti_consentiti_vett[$numero_contratto] != "SI") $anno_utente_attivato = "NO";
} # fine if ($attiva_contratti_consentiti == "s")
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)

if ($priv_vedi_tab_appartamenti == "g" or $priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g" or $priv_vedi_inv_app == "g") {
$priv_app_gruppi = "SI";
$attiva_regole1_consentite_gr = array($id_utente => $attiva_regole1_consentite);
$regole1_consentite_gr = array($id_utente => $regole1_consentite);
$attiva_tariffe_consentite_gr = array($id_utente => $attiva_tariffe_consentite);
$tariffe_consentite_vett_gr = array($id_utente => $tariffe_consentite_vett);
$priv_ins_nuove_prenota_gr = array($id_utente => $priv_ins_nuove_prenota);
$priv_ins_assegnazione_app_gr = array($id_utente => $priv_ins_assegnazione_app);
$priv_mod_prenotazioni_gr = array($id_utente => $priv_mod_prenotazioni);
$priv_mod_assegnazione_app_gr = array($id_utente => $priv_mod_assegnazione_app);
} # fine if ($priv_vedi_tab_appartamenti == "g" or $priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g" or $priv_vedi_inv_app == "g")
else $priv_app_gruppi = "NO";
$utenti_gruppi = array($id_utente => 1);
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) {
$idutente_gruppo = risul_query($utenti_gruppo,$num2,'idutente');
if ($idutente_gruppo != $id_utente and !isset($utenti_gruppi[$idutente_gruppo])) {
$utenti_gruppi[$idutente_gruppo] = 1;

if ($priv_app_gruppi == "SI") {
$priv_anno_ut_gr = esegui_query("select * from $tableprivilegi where idutente = '$idutente_gruppo' and anno = '$anno'");
if (numlin_query($priv_anno_ut_gr) == 1) {
$regole1_consentite_gr[$idutente_gruppo] = risul_query($priv_anno_ut_gr,0,'regole1_consentite');
$attiva_regole1_consentite_gr[$idutente_gruppo] = substr($regole1_consentite_gr[$idutente_gruppo],0,1);
if ($attiva_regole1_consentite_gr[$idutente_gruppo] != "n") $regole1_consentite_gr[$idutente_gruppo] = explode("#@^",substr($regole1_consentite_gr[$idutente_gruppo],3));
$tariffe_consentite_tmp = risul_query($priv_anno_ut_gr,0,'tariffe_consentite');
$attiva_tariffe_consentite_gr[$idutente_gruppo] = substr($tariffe_consentite_tmp,0,1);
if ($attiva_tariffe_consentite_gr[$idutente_gruppo] == "s") {
$tariffe_consentite_tmp = explode(",",substr($tariffe_consentite_tmp,2));
$tariffe_consentite_vett_gr[$idutente_gruppo] = array();
for ($num3 = 0 ; $num3 < count($tariffe_consentite_tmp) ; $num3++) if ($tariffe_consentite_tmp[$num3]) $tariffe_consentite_vett_gr[$idutente_gruppo][$tariffe_consentite_tmp[$num3]] = "SI";
} # fine if ($attiva_tariffe_consentite_gr[$idutente_gruppo] == "s")
$priv_ins_prenota_tmp = risul_query($priv_anno_ut_gr,0,'priv_ins_prenota');
$priv_ins_nuove_prenota_gr[$idutente_gruppo] = substr($priv_ins_prenota_tmp,0,1);
$priv_ins_assegnazione_app_gr[$idutente_gruppo] = substr($priv_ins_prenota_tmp,1,1);
$priv_mod_prenota_tmp = risul_query($priv_anno_ut_gr,0,'priv_mod_prenota');
$priv_mod_prenotazioni_gr[$idutente_gruppo] = substr($priv_mod_prenota_tmp,0,1);
$priv_mod_assegnazione_app_gr[$idutente_gruppo] = substr($priv_mod_prenota_tmp,2,1);
unset($tariffe_consentite_tmp);
unset($priv_ins_prenota_tmp);
unset($priv_mod_prenota_tmp);
} # fine if (numlin_query($priv_anno_ut_gr) == 1)
else {
$priv_ins_nuove_prenota_gr[$idutente_gruppo] = "n";
$priv_mod_prenotazioni_gr[$idutente_gruppo] = "n";
} # fine else if (numlin_query($priv_anno_ut_gr) == 1)
chiudi_query($priv_anno_ut_gr);
} # fine if ($priv_app_gruppi == "SI")

} # fine if ($idutente_gruppo != $id_utente and !isset($utenti_gruppi[$idutente_gruppo]))
} # fine for $num2
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_pers = "SI";
$priv_vedi_beni_inv = "s";
$priv_vedi_inv_app = "s";
$priv_vedi_tab_mesi = "s";
$priv_vedi_tab_costi = "s";
$priv_vedi_tab_prenotazioni = "s";
$priv_vedi_tab_appartamenti = "s";
$priv_vedi_tab_doc = "s";
$priv_ins_nuove_prenota = "s";
$priv_ins_assegnazione_app = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_assegnazione_app = "s";
$priv_mod_tariffa = "s";
$priv_mod_sconto = "s";
$priv_mod_caparra = "s";
$priv_mod_costi_agg = "s";
$priv_mod_utente_ins = "s";
$priv_mod_pagato = "s";
$priv_mod_codice = "s";
$vedi_clienti = "SI";
$priv_vedi_telefoni = "s";
$priv_vedi_indirizzo = "s";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$attiva_casse_consentite = "n";
$attiva_contratti_consentiti = "n";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI") {

include("./includes/funzioni_contratti.php");
include("./includes/funzioni_testo.php");
$priv_cancella_contratti = "n";
@include(C_DATI_PATH."/lingua.php");
$locale_mex = substr(strstr($lingua[1],"-"),1);
$lingua_mex = str_replace("-$locale_mex","",$lingua[1]);
$pag_orig = $pag;
$pag = "visualizza_contratto.php";
$nomi_contratti = trova_nomi_contratti($max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag);
$nome_contratto = $nomi_contratti['salv'][$numero_contratto];
$cont_salva = "SI";
$sovrascrivi = "";

define('C_ID_UTENTE_CONTR',$id_utente);
$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) {
$campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
$num_commenti_pers = count($campi_pers_comm);
} # fine if (numlin_query($campi_pers_comm) == 1)
else $num_commenti_pers = 0;
$campi_pers_cliente = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente' ");
if (numlin_query($campi_pers_cliente)) {
$campi_pers_cliente = explode(">",risul_query($campi_pers_cliente,0,'valpersonalizza'));
$num_campi_pers_cliente = count($campi_pers_cliente);
} # fine if (numlin_query($campi_pers_cliente))
else $num_campi_pers_cliente = 0;
include_once("./includes/funzioni_tariffe.php");
$dati_cat_pers = dati_cat_pers($id_utente,$tablepersonalizza,$lingua_mex,"v",0,1);
$dati_allegato = array('num' => 0, 'num_iim' => 0);
/*# PER ORA IL FORMATO EMAIL NON PUÒ ESSERE API (poi sostituire con nuova funzione dati_allegato($num_contr), da sostituire anche in visualizza_contratto, in funzioni_contratti?)
$allegati_email = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'allegato'");
if (numlin_query($allegati_email)) {
$allegati_email = risul_query($allegati_email,0,'testo');
if ($allegati_email) {
if (strstr($allegati_email,":")) $contr_multilingua = 1;
else $contr_multilingua = 0;
$allegati_email = explode(",",$allegati_email);
$dati_allegato['num'] = (count($allegati_email) - 2);
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
$dati_allegato['ln_corr'][$num1] = "SI";
if ($contr_multilingua) {
$all_vett = explode(">",$allegati_email[$num1]);
$num_ln = count($all_vett);
} # fine if ($contr_multilingua)
else $num_ln = 1;
for ($num2 = 0 ; $num2 < $num_ln ; $num2++) {
if ($contr_multilingua) {
$num_all = substr(strstr($all_vett[$num2],":"),1);
$ln_corr = substr(str_replace(":$num_all","",$all_vett[$num2]),4);
} # fine if ($contr_multilingua)
else $num_all = $allegati_email[$num1];
$allegato_email = esegui_query("select * from $tablecontratti where numero = '$num_all' and tipo = 'file_all'");
$allegato_email = risul_query($allegato_email,0,'testo');
$allegato_email = explode(",",$allegato_email);
if ($contr_multilingua) $dati_allegato["ln$ln_corr"][$num1] = $allegato_email[0];
else $dati_allegato['corr'][$num1] = $allegato_email[0];
} # fine for $num2
} # fine for $num1
unset($ln_corr);
unset($num_all);
unset($all_vett);
unset($num_ln);
unset($allegato_email);
} # fine if ($allegati_email)
} # fine if (numlin_query($allegati_email))
unset($allegati_email);*/
$commento_personalizzato_ = "commento_personalizzato_";
$campo_personalizzato_ = "campo_personalizzato_";
$num_persone_tipo_ = "num_persone_tipo_";
$allegato_ = "allegato";
include("./includes/variabili_contratto.php");

$tableprenotacanc = $PHPR_TAB_PRE."prenotacanc".$anno;
if (isset($del) and $del == 1) $tableprenota = $tableprenotacanc;
else $tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tablecosti = $PHPR_TAB_PRE."costi".$anno;
$query_prenota = "";
$query_clienti = "";
$query_cassa = "";
$lista_prenota = 0;
$sec_oggi = time() + (C_DIFF_ORE * 3600);


if (!empty($res_from) or !empty($res_to)) {
$res_from = fixset($res_from);
$res_to = fixset($res_to);
if ($res_from == "today") $res_from = date("Y-m-d",$sec_oggi);
if ($res_from == "tomorrow") $res_from = date("Y-m-d",($sec_oggi + 86400));
if ($res_from == "yesterday") $res_from = date("Y-m-d",($sec_oggi - 86400));
if ($res_to == "today") $res_to = date("Y-m-d",$sec_oggi);
if ($res_to == "tomorrow") $res_to = date("Y-m-d",($sec_oggi + 86400));
if ($res_to == "yesterday") $res_to = date("Y-m-d",($sec_oggi - 86400));
if (!$res_from or !$res_to or $res_from <= $res_to) {
$primo_periodo = 1;
if ($res_from and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_from)) {
$data_inizio_selezione = $res_from;
$primo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio >= '$res_from' order by idperiodi ");
if (numlin_query($primo_periodo)) $primo_periodo = risul_query($primo_periodo,0,'idperiodi');
else $primo_periodo = "";
} # fine if ($res_from and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_from))
if ($primo_periodo) {
$ultimo_periodo = "";
if ($res_to and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_to)) {
$data_fine_selezione = $res_to;
$ultimo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio < '$res_to' order by idperiodi desc ");
if (numlin_query($ultimo_periodo)) $ultimo_periodo = risul_query($ultimo_periodo,0,'idperiodi');
else $ultimo_periodo = -1;
} # fine if ($res_to and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_to))
if (!$ultimo_periodo or $ultimo_periodo >= $primo_periodo) {
$query_prenota = "select idprenota from $tableprenota where iddatafine >= '$primo_periodo'";
if ($ultimo_periodo) $query_prenota .= " and iddatainizio <= '$ultimo_periodo'";
$query_prenota .= " order by iddatainizio";
} # fine if (!$ultimo_periodo or $ultimo_periodo >= $primo_periodo)
} # fine if ($primo_periodo)
} # fine if (!$res_from or !$res_to or $res_from <= $res_to)
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if (!empty($res_from) or !empty($res_to))

if (!empty($res_num) and !$query_prenota) {
$prenota_in_lista = array();
$anno_num_pren = 0;
$res_num = explode(",",togli_acapo($res_num));
$num_res = count($res_num);
for ($num1 = 0 ; $num1 < $num_res ; $num1++) {
$num_pren = $res_num[$num1];
if (str_replace("-","",$num_pren) != $num_pren) {
$anno_pren = "";
$num_pren = explode("-",$num_pren);
if (preg_match("/^([0-9]{4,4}\.)?[0-9]+$/",$num_pren[0])) $res_ini = $num_pren[0];
else $res_ini = "";
if (preg_match("/^([0-9]{4,4}\.)?[0-9]+$/",$num_pren[1])) $res_fine = $num_pren[1];
else $res_fine = "";
if ($res_ini or (!$res_ini and $res_fine)) {
if (!$res_ini) $ini_for = 1;
else {
if (strstr($res_ini,".")) {
$anno_pren = substr($res_ini,0,4);
if ($anno_pren >= $anno) $anno_pren = "";
$ini_for = substr($res_ini,5);
} # fine if (strstr($res_ini,"."))
else $ini_for = $res_ini;
} # fine else if (!$res_ini)
if (!$res_fine) {
$fine_for = esegui_query("select max(idprenota) from $tableprenota ");
if (numlin_query($fine_for)) $fine_for = risul_query($fine_for,0,0);
else $fine_for = 0;
if (isset($del) and $del == "2") {
$fine_for2 = esegui_query("select max(idprenota) from $tableprenotacanc ");
if (numlin_query($fine_for2)) $fine_for2 = risul_query($fine_for,0,0);
else $fine_for2 = 0;
if ($fine_for2 > $fine_for) $fine_for = $fine_for2;
} # fine if (isset($del) and $del == "2")
} # fine if (!$res_fine)
else {
if (strstr($res_fine,".")) {
if ($anno_pren and $anno_pren != substr($res_fine,0,4)) $fine_for = 0;
else $fine_for = substr($res_fine,5);
if (!$anno_pren) {
$anno_pren = substr($res_fine,0,4);
if ($anno_pren >= $anno) $anno_pren = "";
} # fine if (!$anno_pren)
} # fine if (strstr($res_fine,"."))
else $fine_for = $res_fine;
} # fine else if (!$res_fine)
for ($num2 = $ini_for ; $num2 <= $fine_for ; $num2++) {
if ($anno_pren) $num_pren = "$anno_pren.$num2";
else $num_pren = $num2;
if (empty($prenota_in_lista[$num_pren])) {
$prenota_in_lista[$num_pren] = 1;
$query_prenota .= ",$num_pren";
} # fine if (empty($prenota_in_lista[$num_pren]))
} # fine for $num2
} # fine if ($res_ini or (!$res_ini and $res_fine))
} # fine if (str_replace("-","",$num_pren) != $num_pren)
else {
if (preg_match("/^([0-9]{4,4}\.)?[0-9]+$/",$num_pren) and empty($prenota_in_lista[$num_pren])) {
if (strstr($num_pren,".") and (substr($num_pren,0,4) >= $anno)) $num_pren = substr($num_pren,5);
$prenota_in_lista[$num_pren] = 1;
$query_prenota .= ",$num_pren";
} # fine if (preg_match("/[0-9]*/",$num_pren) or preg_match("/[0-9]{4,4}\+[0-9]*/",$num_pren)) and...
} # fine else if (str_replace("-","",$num_pren) != $num_pren)
} # fine for $num1
if ($query_prenota) {
if (strstr($query_prenota,".")) {
$num_pren = explode(",",$query_prenota);
$num_p = count($num_pren);
$query_prenota = "select idprenota from $tableprenota where ";
for ($num1 = 1 ; $num1 < $num_p ; $num1++) {
if (strstr($num_pren[$num1],".")) $query_prenota .= "id_anni_prec $LIKE '%;".str_replace(".",",",$num_pren[$num1]).";%' or ";
else $query_prenota .= "idprenota = '".$num_pren[$num1]."' or ";
} # fine for $num1
$query_prenota = substr($query_prenota,0,-3)."order by iddatainizio";
} # fine if (strstr($query_prenota,"."))
else $lista_prenota = 1;
} # fine if ($query_prenota)
else $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if (!empty($res_num) and !$query_prenota)

if ((!empty($res_ins_from) or !empty($res_ins_to)) and !$query_prenota) {
if ($res_ins_from == "today") $res_ins_from = date("Y-m-d",$sec_oggi);
if ($res_ins_from == "tomorrow") $res_ins_from = date("Y-m-d",($sec_oggi + 86400));
if ($res_ins_from == "yesterday") $res_ins_from = date("Y-m-d",($sec_oggi - 86400));
if ($res_ins_to == "today") $res_ins_to = date("Y-m-d",$sec_oggi);
if ($res_ins_to == "tomorrow") $res_ins_to = date("Y-m-d",($sec_oggi + 86400));
if ($res_ins_to == "yesterday") $res_ins_to = date("Y-m-d",($sec_oggi - 86400));
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_ins_from)) $res_ins_from = "";
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_ins_to)) $res_ins_to = "";
if ($res_ins_from or $res_ins_to) {
if (!$res_ins_from) $res_ins_from = "1980-01-01";
if (!$res_ins_to) $res_ins_to = date("Y-m-d",mktime(0,0,0,date("m"),(date("d") + 2),date("Y")));
if ($res_ins_from <= $res_ins_to) {
if (isset($del) and $del == "2") $query_prenota = "(select idprenota,iddatainizio from $tableprenota where datainserimento >= '$res_ins_from 00:00:00' and datainserimento <= '$res_ins_to 23:59:59') union (select idprenota,iddatainizio from $tableprenotacanc where (datainserimento >= '$res_ins_from 00:00:00' and datainserimento <= '$res_ins_to 23:59:59') or (data_modifica >= '$res_ins_from 00:00:00' and data_modifica <= '$res_ins_to 23:59:59')) order by iddatainizio";
else {
if (isset($del) and $del == "1") $query_prenota = "select idprenota from $tableprenota where (datainserimento >= '$res_ins_from 00:00:00' and datainserimento <= '$res_ins_to 23:59:59') or (data_modifica >= '$res_ins_from 00:00:00' and data_modifica <= '$res_ins_to 23:59:59') order by iddatainizio";
else $query_prenota = "select idprenota from $tableprenota where datainserimento >= '$res_ins_from 00:00:00' and datainserimento <= '$res_ins_to 23:59:59' order by iddatainizio";
} # fine else if (isset($del) and $del == "2")
} # fine if ($res_ins_from <= $res_ins_to)
} # fine if ($res_ins_from or $res_ins_to)
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if ((!empty($res_ins_from) or !empty($res_ins_to)) and !$query_prenota)

if (!empty($res_arr) and !$query_prenota) {
if ($res_arr == "today") $res_arr = date("Y-m-d",$sec_oggi);
if ($res_arr == "tomorrow") $res_arr = date("Y-m-d",($sec_oggi + 86400));
if ($res_arr == "yesterday") $res_arr = date("Y-m-d",($sec_oggi - 86400));
if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_arr)) {
$arrivo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$res_arr' ");
if (numlin_query($arrivo)) {
$data_inizio_selezione = $res_arr;
$data_fine_selezione = date("Y-m-d",mktime(0,0,0,substr($res_arr,5,2),(substr($res_arr,8,2) + 1),substr($res_arr,0,4)));
$arrivo = risul_query($arrivo,0,'idperiodi');
$query_prenota = "select idprenota from $tableprenota where iddatainizio = '$arrivo' order by iddatainizio";
} # fine if (numlin_query($arrivo))
} # fine if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_arr))
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if (!empty($res_arr) and !$query_prenota)

if (!empty($res_dep) and !$query_prenota) {
if ($res_dep == "today") $res_dep = date("Y-m-d",$sec_oggi);
if ($res_dep == "tomorrow") $res_dep = date("Y-m-d",($sec_oggi + 86400));
if ($res_dep == "yesterday") $res_dep = date("Y-m-d",($sec_oggi - 86400));
if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_dep)) {
$partenza = esegui_query("select idperiodi from $tableperiodi where datafine = '$res_dep' ");
if (numlin_query($partenza)) {
$data_inizio_selezione = $res_dep;
$data_fine_selezione = date("Y-m-d",mktime(0,0,0,substr($res_dep,5,2),(substr($res_dep,8,2) + 1),substr($res_dep,0,4)));
$partenza = risul_query($partenza,0,'idperiodi');
$query_prenota = "select idprenota from $tableprenota where iddatafine = '$partenza' order by iddatainizio";
} # fine if (numlin_query())
} # fine if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_dep))
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if (!empty($res_dep) and !$query_prenota)

if (!empty($clients) and !$query_prenota) {
if ($clients == "all") $query_clienti = "select idclienti from $tableclienti where max_num_ordine = '1' order by cognome ";
if ($clients == "guests") $query_clienti = "select idclienti from $tableclienti order by cognome ";
if (!$query_clienti) $query_clienti = "select idclienti from $tableclienti where idclienti < '-3'";
} # fine if (!empty($clients) and !$query_prenota)

if (isset($cashbox) and strcmp((string) $cashbox,"") and !$query_prenota and !$query_clienti) {
$query_cassa = "select idcasse from $tablecasse where nome_cassa = '".aggslashdb($cashbox)."' ";
} # fine if (isset($cashbox) and strcmp((string) $cashbox,"") and !$query_prenota and !$query_clienti)

if (!$query_prenota and !$query_clienti and !$query_cassa) {
if (empty($res_year)) $res_year = $anno;
$primo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$res_year-01-01' ");
if (numlin_query($primo_periodo)) {
$primo_periodo = risul_query($primo_periodo,0,'idperiodi');
$ultimo_periodo = esegui_query("select max(idperiodi) from $tableperiodi where datainizio <= '$res_year-12-31' ");
$ultimo_periodo = risul_query($ultimo_periodo,0,0);
$query_prenota = "select idprenota from $tableprenota where iddatafine >= '$primo_periodo' and iddatainizio <= '$ultimo_periodo' order by iddatainizio";
} # fine if (numlin_query($primo_periodo))
} # fine if (!$query_prenota and !$query_clienti and !$query_cassa)


if ($query_prenota or $query_clienti or $query_cassa) {
$num_ripeti = 0;

if ($query_prenota and ($priv_vedi_tab_prenotazioni != "n" or ($priv_vedi_tab_mesi != "n" and $priv_mod_prenotazioni != "n"))) {
if ($lista_prenota) {
$lista_prenota = explode(",",$query_prenota);
$num_ripeti = count($lista_prenota) - 1;
} # fine if ($lista_prenota)
else {
if (isset($del) and $del == "2" and substr($query_prenota,-21) == "order by iddatainizio" and substr($query_prenota,0,(strlen($tableprenota) + 29)) == "select idprenota from $tableprenota where ") {
$query_prenota = substr($query_prenota,0,-21);
$query_prenota = "(select idprenota,iddatainizio".substr($query_prenota,16).") union (select idprenota,iddatainizio from $tableprenotacanc where ".substr($query_prenota,(strlen($tableprenota) + 29)).") order by iddatainizio";
} # fine if (isset($del) and $del == "2" and substr($query_prenota,-21) == "order by iddatainizio" and...
$prenotazioni = esegui_query($query_prenota);
$num_ripeti = numlin_query($prenotazioni);
$lista_prenota = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) $lista_prenota[$num1] = risul_query($prenotazioni,($num1 - 1),'idprenota');
chiudi_query($prenotazioni);
} # fine else if ($lista_prenota)
if (isset($del) and $del == "2") $pcanc = "+";
else $pcanc = "";
include("./includes/dati_lista_prenota.php");
} # fine if ($query_prenota and ($priv_vedi_tab_prenotazioni != "n" or...

if ($query_clienti and $vedi_clienti != "NO") {
$clienti = esegui_query($query_clienti);
$num_ripeti = numlin_query($clienti);
$lista_clienti = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) $lista_clienti[$num1] = risul_query($clienti,($num1 - 1),'idclienti');
chiudi_query($clienti);
include("./includes/dati_lista_clienti.php");
} # fine if ($query_clienti and $vedi_clienti != "NO")

if ($query_cassa and $priv_vedi_tab_costi != "n") {
$cassa = esegui_query($query_cassa);
if (numlin_query($cassa)) $lista_cassa = risul_query($cassa,0,'idcasse');
else $lista_cassa = 1;
if ($attiva_casse_consentite == "n" or $casse_consentite_vett[$lista_cassa] == "SI") {
$num_ripeti = 1;
include("./includes/dati_lista_cassa.php");
} # fine if ($attiva_casse_consentite == "n" or $casse_consentite_vett[$lista_cassa] == "SI")
} # fine if ($query_cassa and $priv_vedi_tab_costi != "n")


$mostra_headers = "SI";
$messaggio_di_errore = "";
$num_contr_esist = 0;
$contratto = crea_contratto($numero_contratto,$tipo_contratto,"1","","","");
if (!empty($nome_file_contr['downl']['num'])) $n_file = $nome_file_contr['downl']['num'];
else $n_file = 1;

if ($messaggio_di_errore) {
$tipo_contratto = "contrhtm";
$dir_salva = "";
$contratto = "<div style=\"padding: 5px;\">
<br><span class=\"colred\">".mex("Errore",$pag)."</span>:<br>
<br><div style=\"padding: 0 0 0 10px;\">
$messaggio_di_errore
</div></div>";
} # fine if ($messaggio_di_errore)

if ($dir_salva and !$num_contr_esist) $mostra_headers = "NO";
$foothtm = "";

$dati_download = "";
if (!empty($nome_file_contr['downl']['datdownl'])) $dati_download = explode(">",$nome_file_contr['downl']['datdownl']);
else {
$risul = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'datdownl'");
if (numlin_query($risul)) $dati_download = explode(">",(string) risul_query($risul,0,'testo'));
} # fine else if (!empty($nome_file_contr['downl']['datdownl']))
if ($tipo_contratto == "contrhtm") {
$show_bar = "NO";
$headhtm = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'headhtm'");
if (numlin_query($headhtm) == 1) {
$mostra_headers = "NO";
echo risul_query($headhtm,0,'testo');
$foothtm = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'foothtm'");
if (numlin_query($foothtm)) $foothtm = risul_query($foothtm,0,'testo');
} # fine if (numlin_query($headhtm) == 1)
} # fine if ($tipo_contratto == "contrhtm")
if ($tipo_contratto == "contrrtf") {
$mostra_headers = "NO";
if (isset($nome_file_contr[$n_file])) $nome_file = $nome_file_contr[$n_file];
else $nome_file = str_replace("\"","_",str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nome_contratto)))).".rtf";
if (substr($nome_file,-3) == ".gz") $nome_file = substr($nome_file,0,-3);
if (isset($dati_download[1]) and $dati_download[1] == "var" and isset($nome_file_contr['downl'][$n_file]) and strcmp($nome_file_contr['downl'][$n_file],"")) $nome_file = $nome_file_contr['downl'][$n_file].substr($nome_file,-4);
header("Pragma: public");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Content-Transfer-Encoding: none");
header("Content-Type: application/rtf; name=\"$nome_file\"");
#header("Content-Type: application/octetstream; name=\"$nome_file\"");
#header("Content-Type: application/octet-stream; name=\"$nome_file\"");
header("Content-Disposition: inline; filename=\"$nome_file\"");
#header("Content-length: $lunghezza_file");
} # fine if ($tipo_contratto == "contrrtf")
if ($tipo_contratto == "contrtxt") {
$mostra_headers = "NO";
if (isset($nome_file_contr[$n_file])) {
$nome_file = $nome_file_contr[$n_file];
if (substr($nome_file,-3) == ".gz") $nome_file = substr($nome_file,0,-3);
} # fine if (isset($nome_file_contr[$n_file]))
else $nome_file = str_replace("\"","_",str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nome_contratto)))).".txt";
if ($dati_download) {
if (isset($dati_download[1]) and $dati_download[1] == "var" and isset($nome_file_contr['downl'][$n_file]) and strcmp($nome_file_contr['downl'][$n_file],"")) $nome_file = $nome_file_contr['downl'][$n_file].substr($nome_file,-4);
if ($dati_download[0]) $nome_file = substr($nome_file,0,-4).".".$dati_download[0];
} # fine if ($dati_download)
header("Pragma: public");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Content-Transfer-Encoding: none");
header("Content-Type: application/text; name=\"$nome_file\"");
#header("Content-Type: application/octetstream; name=\"$nome_file\"");
#header("Content-Type: application/octet-stream; name=\"$nome_file\"");
header("Content-Disposition: inline; filename=\"$nome_file\"");
#header("Content-length: $lunghezza_file");
} # fine if ($tipo_contratto == "contrtxt")

if ($mostra_headers == "SI") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
} # fine if ($mostra_headers == "SI")

echo $contratto;

if ($mostra_headers == "SI") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");
} # fine if ($mostra_headers == "SI")
if ($foothtm) echo $foothtm;

} # fine if ($query_prenota or $query_clienti or $query_cassa)

$pag = $pag_orig;
} # fine if ($anno_utente_attivato == "SI")
} # fine if ($anno_esist)
} # fine else if ($raggiunto_limite)
} # fine if (fixset($pass) == $pass_api)
else {
$id_transazione = crea_id_sessione("",$tabletransazioniweb,16,0,"idtransazioni","anno");
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tabletransazioniweb (idtransazioni,tipo_transazione,dati_transazione1,dati_transazione2,ultimo_accesso) values ('$id_transazione','api_l','doc$numero_contratto','$REMOTE_ADDR','$ultimo_accesso')");
} # fine else if (fixset($pass) == $pass_api)

} # fine else if ($login_sbagliati >= $max_log_sbagliati)
} # fine if (!$indirizzi_ip_api or strstr(",$indirizzi_ip_api,",",$REMOTE_ADDR,"))
else echo "$REMOTE_ADDR not allowed.";
} # fine if (numlin_query($api_esistente))
} # fine if (!empty($doc))





# Conferma sessione da modulo carte di credito
if (!empty($session_data) and $id_sessione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "") {
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1")) {
$cli_pass = 0;
$cli_presfut = 0;
$cli_id = fixstr($cli_id);
# Non fare lock in scrittura sulle stesse tabelle dalle funzioni delle interconnessioni, altrimenti si blocca quando si attiva cc_hd_token
if (!empty($tweb)) $tabelle_lock = array($tabletransazioniweb);
else {
if (strstr($cli_id,",")) $tabelle_lock = array($tablesessioni,$tabletransazioni);
else $tabelle_lock = array($tablesessioni);
} # fine else if (!empty($tweb))
if (($cli_id == "pass" or $cli_id == "pres_fut") and empty($tweb)) {
$anni_esist = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni_esist);
unset($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) $anni[$num1] = risul_query($anni_esist,$num1,'idanni');
$anno_corr = $anno_corrente;
if ($cli_id == "pres_fut") {
# Mantenere carte degli ultimi 4 mesi (120gg)
$data_limite_passato = date("Y-m-d",(time() - (120 * 86400) + (C_DIFF_ORE * 3600)));
$anno_corr = substr($data_limite_passato,0,4);
} # fine if ($cli_id == "pres_fut")
else $data_limite_passato = "";
if ($anni[0] > $anno_corr) $anno_corr = $anni[0];
if ($anni[($num_anni - 1)] < $anno_corr) $anno_corr = $anni[($num_anni - 1)];
$altre_tab_lock = array($tableanni);
$num_lock = 1;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
if ($anni[$num1] >= $anno_corr) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."prenota".$anni[$num1];
$num_lock++;
} # fine if ($anni[$num1] >= $anno_corr)
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
if ($anni[$num1] >= $anno_corr and (!defined('C_VERSIONE_ATTUALE') or C_VERSIONE_ATTUALE > 2.99)) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."prenotacanc".$anni[$num1];
$num_lock++;
} # fine if ($anni[$num1] >= $anno_corr and (!defined('C_VERSIONE_ATTUALE') or C_VERSIONE_ATTUALE > 2.99))
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
if ($anni[$num1] >= $anno_corr) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."rclientiprenota".$anni[$num1];
$num_lock++;
} # fine if ($anni[$num1] >= $anno_corr)
} # fine for $num1
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."periodi".$anno_corr;
$num_lock++;
$altre_tab_lock[$num_lock] = $tableclienti;
$num_lock++;
$altre_tab_lock[$num_lock] = $tablepersonalizza;
$num_lock++;
$altre_tab_lock[$num_lock] = $tableprivilegi;
$num_lock++;
$altre_tab_lock[$num_lock] = $tablerelgruppi;
} # fine if (($cli_id == "pass" or $cli_id == "pres_fut") and empty($tweb))
else {
if ($cli_id or !empty($priv_r)) $altre_tab_lock = array($tableclienti,$tablepersonalizza,$tableprivilegi,$tablerelgruppi);
else $altre_tab_lock = array($tablepersonalizza);
} # fine else if (($cli_id == "pass" or $cli_id == "pres_fut") and empty($tweb))
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$sessione_trovata = 0;
if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC and $cli_id == "pres_fut") $sessione_trovata = 1;
else {
$minuti_durata_sessione = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_sessione' and idutente = '1'");
$minuti_durata_sessione = risul_query($minuti_durata_sessione,0,'valpersonalizza_num');
$limite_sessioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
if (!empty($tweb)) {
esegui_query("delete from $tabletransazioniweb where tipo_transazione = 'pay_c' and ultimo_accesso <= '$limite_sessioni_vecchie'");
$sessione = esegui_query("select * from $tabletransazioniweb where tipo_transazione = 'pay_c' and idtransazioni = '".aggslashdb($id_sessione)."' ");
$sessione_trovata = numlin_query($sessione);
} # fine if (!empty($tweb))
else {
esegui_query("delete from $tablesessioni where ultimo_accesso <= '$limite_sessioni_vecchie'");
$sessione = esegui_query("select * from $tablesessioni where idsessioni = '$id_sessione'");
$sessione_trovata = numlin_query($sessione);
} # fine else if (!empty($tweb))
} # fine else if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC and $cli_id == "pres_fut")
if ($sessione_trovata) {

@include(C_DATI_PATH."/lingua.php");
@include(C_DATI_PATH."/tema.php");
if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC) $id_utente = 1;
else {
if (!empty($tweb)) $id_utente = "t";
else $id_utente = risul_query($sessione,0,'idutente');
if ($id_utente == -1 or $id_utente == "t") {
$lingua[$id_utente] = $lingua[1];
$tema[$id_utente] = $tema[1];
} # fine if ($id_utente == -1 or $id_utente == "t")
} # fine else if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC)
echo "<user_id>$id_utente<user_id>
<lang>".$lingua[$id_utente]."</lang>
<theme>".$tema[$id_utente]."</theme>";
if (defined('C_FILE_CSS_PERS') and C_FILE_CSS_PERS != "" and @is_file(C_FILE_CSS_PERS)) echo "<css_pers>".C_FILE_CSS_PERS."</css_pers>";
if (defined('C_FILE_MOB_CSS_PERS') and C_FILE_MOB_CSS_PERS != "" and @is_file(C_FILE_MOB_CSS_PERS)) echo "<mob_css_pers>".C_FILE_MOB_CSS_PERS."</mob_css_pers>";
$idclienti = aggslashdb($cli_id);

if (!empty($priv_r) or ($cli_id and $id_utente != 1 and empty($tweb))) {
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
$prendi_gruppi = "";
$priv_gest_pass_cc = "";
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
$priv_gest_pass_cc = substr($priv_mod_pers,5,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$modifica_clienti = "NO";
if (substr($priv_ins_clienti,1,1) == "s") $modifica_clienti = "SI";
if (substr($priv_ins_clienti,1,1) == "p") $modifica_clienti = "PROPRI";
if (substr($priv_ins_clienti,1,1) == "g") { $modifica_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,'idutente')] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if (!empty($priv_r) or ($cli_id and $id_utente != 1 and empty($tweb)))

if (($cli_id == "pass" or $cli_id == "pres_fut") and empty($tweb)) {
if ($cli_id == "pass") $cli_pass = 1;
if ($cli_id == "pres_fut") $cli_presfut = 1;
$cli_id = "";
if ($id_utente != 1 and ($modifica_clienti == "PROPRI" or $modifica_clienti == "GRUPPI")) {
$condizione_utente_canc = "where ( utente_inserimento = '$id_utente'";
if ($modifica_clienti == "GRUPPI") {
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_utente_canc .= " or utente_inserimento = '$idut_gr'";
} # fine if ($modifica_clienti == "GRUPPI")
$condizione_utente_canc .= " )";
} # fine if ($id_utente != 1 and ($modifica_clienti == "PROPRI" or $modifica_clienti == "GRUPPI"))
else $condizione_utente_canc = "";
} # fine if (($cli_id == "pass" or $cli_id == "pres_fut") and empty($tweb))

if ($cli_id and $id_utente != 1) {
$cli_priv = 0;
if (!empty($tweb)) {
$idclienti_tweb = risul_query($sessione,0,'dati_transazione14');
if ($idclienti_tweb == $idclienti) $cli_priv = 1;
} # fine if (!empty($tweb))
else {
if (strstr($idclienti,",")) {
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - (15 * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioni where ultimo_accesso <= '$limite_transazioni_vecchie' and tipo_transazione = 'cn_cc' ");
$transaz_esist = esegui_query("select * from $tabletransazioni where idsessione = '$id_sessione' and tipo_transazione = 'cn_cc' and dati_transazione1 = '$idclienti' ");
if (numlin_query($transaz_esist)) {
$cli_priv = 1;
esegui_query("delete from $tabletransazioni where idsessione = '$id_sessione' and tipo_transazione = 'cn_cc' and dati_transazione1 = '$idclienti' ");
} # fine if (numlin_query($transaz_esist))
} # fine if (strstr($idclienti,","))
else {
if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI") {
$cliente_proprio = esegui_query("select idclienti from $tableclienti where idclienti = '$idclienti' and utente_inserimento = '$id_utente'");
if (numlin_query($cliente_proprio) == 0) $modifica_clienti = "NO";
} # fine if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI")
elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI") {
$cliente_proprio = esegui_query("select utente_inserimento from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente_proprio) == 0) $utente_inserimento = "0";
else $utente_inserimento = risul_query($cliente_proprio,0,'utente_inserimento');
if (!$utenti_gruppi[$utente_inserimento]) $modifica_clienti = "NO";
} # fine elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI")
if ($anno_utente_attivato == "SI" and $modifica_clienti != "NO" and $vedi_clienti != "NO" and $idclienti and controlla_num_pos($idclienti) == "SI") $cli_priv = 1;
} # fine else if (strstr($idclienti,","))
} # fine else if (!empty($tweb))
echo "<cli_priv>$cli_priv</cli_priv>";
} # fine if ($cli_id and $id_utente != 1)

if ($cli_pass or $cli_presfut) {
$idperiodocorrente = (calcola_id_periodo_corrente($anno_corr,"NO",$data_limite_passato) - 1);
$cliente_attivo = array();
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = $anni[$num1];
if ($anno_mostra >= $anno_corr) {
$tableprenota_mostra = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tablerclientiprenota_mostra = $PHPR_TAB_PRE."rclientiprenota".$anno_mostra;
if ($anno_mostra == $anno_corr) $prenota = esegui_query("select idprenota,idclienti from $tableprenota_mostra where iddatafine > '$idperiodocorrente' or (pagato is NULL and tariffa_tot > 0) or pagato < tariffa_tot ");
else  $prenota = esegui_query("select idprenota,idclienti from $tableprenota_mostra");
$num_prenota = numlin_query($prenota);
for ($num2 = 0 ; $num2 < $num_prenota ; $num2++) {
$cliente_attivo[risul_query($prenota,$num2,'idclienti')] = 1;
$idprenota = risul_query($prenota,$num2,'idprenota');
$ospiti = esegui_query("select idclienti from $tablerclientiprenota_mostra where idprenota = '$idprenota' ");
for ($num3 = 0 ; $num3 < numlin_query($ospiti) ; $num3++) $cliente_attivo[risul_query($ospiti,$num3,'idclienti')] = 1;
} # fine for $num2
if (!defined('C_VERSIONE_ATTUALE') or C_VERSIONE_ATTUALE > 2.99) {
$tableprenotacanc_mostra = $PHPR_TAB_PRE."prenotacanc".$anno_mostra;
if ($anno_mostra == $anno_corr) $prenotacanc = esegui_query("select idprenota,idclienti from $tableprenotacanc_mostra where iddatafine > '".($idperiodocorrente - 2)."' ");
else  $prenotacanc = esegui_query("select idprenota,idclienti from $tableprenotacanc_mostra");
$num_prenotacanc = numlin_query($prenotacanc);
for ($num2 = 0 ; $num2 < $num_prenotacanc ; $num2++) {
$cliente_attivo[risul_query($prenotacanc,$num2,'idclienti')] = 1;
$idprenota = risul_query($prenotacanc,$num2,'idprenota');
$ospiti = esegui_query("select idclienti from $tablerclientiprenota_mostra where idprenota = '$idprenota' ");
for ($num3 = 0 ; $num3 < numlin_query($ospiti) ; $num3++) $cliente_attivo[risul_query($ospiti,$num3,'idclienti')] = 1;
} # fine for $num2
} # fine if (!defined('C_VERSIONE_ATTUALE') or C_VERSIONE_ATTUALE > 2.99)
} # fine if ($anno_mostra >= $anno_corr)
} # fine for $num1
if ($cli_pass) {
$clienti = esegui_query("select * from $tableclienti $condizione_utente_canc");
$num_clienti = numlin_query($clienti);
$lista_clienti_canc = "";
for ($num1 = 0; $num1 < $num_clienti; $num1++) {
$idclienti = risul_query($clienti,$num1,'idclienti');
if (empty($cliente_attivo[$idclienti])) $lista_clienti_canc .= ",$idclienti";
} # fine for $num1
echo "<cli_pass>".substr($lista_clienti_canc,1)."</cli_pass>";
} # fine if ($cli_pass)
if ($cli_presfut) {
$lista_clienti_att = "";
foreach ($cliente_attivo as $idcli_at => $val) $lista_clienti_att .= ",$idcli_at";
echo "<cli_presfut>".substr($lista_clienti_att,1).";</cli_presfut>";
} # fine if ($cli_presfut)
} # fine if ($cli_pass or $cli_presfut)

if (!empty($priv_r)) {
$val_priv_r = "";
if ($priv_r == "priv_gest_pass_cc") $val_priv_r = $priv_gest_pass_cc;
if ($val_priv_r) echo "<privr_$priv_r>$val_priv_r</privr_$priv_r>";
} # fine if (!empty($priv_r))

} # fine if ($sessione_trovata)
unlock_tabelle($tabelle_lock);
} # fine if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1"))
exit();
} # fine if (!empty($session_data) and $id_sessione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "")





# Azioni da modulo carte di credito
if (!empty($azione) and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "") {
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1")) {


if ($azione == "pull_xml" and isset($tipo_ic) and ($tipo_ic == "boo" or $tipo_ic == "eqc" or $tipo_ic == "oct") and !empty($valore_xml) and !empty($token)) {

$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$funz_import_reservations = "";

$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and $mod_ext == $tipo_ic.fixset($ord_ic) and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if (isset($ic_present[$interconnection_name]) and $ic_present[$interconnection_name] == "SI") {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
} # fine if (isset($ic_present[$interconnection_name]) and $ic_present[$interconnection_name] == "SI")
break;
} # fine if ($mod_ext != "." and $mod_ext != ".." and $mod_ext == $tipo_ic.fixset($ord_ic) and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
if ($funz_import_reservations) {

$anno_esist = 1;
# Se ci troviamo dopo il periodo di creazione dell'anno ma questo non è stato ancora creato
if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php")) {
$anno_prec = $anno - 1;
if (@is_file(C_DATI_PATH."/selectperiodi$anno_prec.1.php")) $anno = $anno_prec;
else $anno_esist = 0;
} # fine if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php"))
if ($anno_esist) {

if (substr(C_URL_PULL_TOKEN,0,17) == "https://localhost") $token_res_id = file(C_URL_PULL_TOKEN."$tipo_ic$token.php",false,stream_context_create(array("ssl" => array("verify_peer" => true,"allow_self_signed" => true))));
else $token_res_id = @file(C_URL_PULL_TOKEN."$tipo_ic$token.php");
if (is_array($token_res_id)) $token_res_id = trim(implode("",$token_res_id));
else $token_res_id = "";
$res_id = 0;
if ($tipo_ic == "boo" and stristr($valore_xml,"<id>$token_res_id<")) $res_id = 1;
if ($tipo_ic == "eqc" and stristr($valore_xml," id=\"$token_res_id\"")) $res_id = 1;
if ($tipo_ic == "oct" and stristr($valore_xml,"<ResId>$token_res_id<")) $res_id = 1;
if ($res_id) {

@include(C_DATI_PATH."/lingua.php");
$locale_mex = substr(strstr($lingua[1],"-"),1);
$lingua_mex = str_replace("-$locale_mex","",$lingua[1]);
$id_utente_sessione = "-1";
$id_sessione = crea_id_sessione($anno,$tableversioni);
if (C_VERSIONE_ATTUALE < 3.04) $id_sessione = substr($id_sessione,0,20).substr($id_sessione,30);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if (!empty($_SERVER['REMOTE_ADDR'])) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$REMOTE_ADDR = aggslashdb(fixstr($REMOTE_ADDR));
if (!empty($_SERVER['REMOTE_PORT'])) $REMOTE_PORT = $_SERVER['REMOTE_PORT'];
$REMOTE_PORT = aggslashdb(fixstr($REMOTE_PORT));
if (!empty($_SERVER['HTTP_USER_AGENT'])) $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$HTTP_USER_AGENT = aggslashdb(fixstr($HTTP_USER_AGENT));
if (fixset($_SERVER['HTTPS']) == "on" or fixset($_SERVER['SERVER_PORT']) == "443") $tipo_conn = "HTTPS";
else $tipo_conn = "HTTP";
esegui_query("insert into $tablesessioni (idsessioni,idutente,indirizzo_ip,tipo_conn,user_agent,ultimo_accesso) values ('$id_sessione','$id_utente_sessione','$REMOTE_ADDR','$tipo_conn','$HTTP_USER_AGENT','$ultimo_accesso')","",1);
$id_utente_origi = $id_utente;
$id_utente = 1;
$testo = $funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME,$valore_xml);
$id_utente = $id_utente_origi;

} # fine if ($res_id)

} # fine if ($anno_esist)
} # fine if ($funz_import_reservations)
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))
} # fine if ($azione == "pull_xml" and isset($tipo_ic) and ($tipo_ic == "boo" or $tipo_ic == "eqc" or $tipo_ic == "oct") and...


} # fine if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1"))
} # fine if (!empty($azione) and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "")




?>
