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

$pag = "interconnessioni.php";
$titolo = "HotelDruid: Interconnessioni";

$var_pag = array();
$var_pag[0] = 'user';
$var_pag[1] = 'password';
$var_pag[2] = 'modifica_interconnessione';
$var_pag[3] = 'modulo';
$var_pag[4] = 'modifica';
$var_pag[5] = 'utente_mess';
$var_pag[6] = 'email_mess';
$var_pag[7] = 'chiudi_periodi_pieni';
$var_pag[8] = 'restrizioni_per_sola_lettura';
$var_pag[9] = 'prenota_vicine';
$var_pag[10] = 'origine_prenota';
$var_pag[11] = 'conferma_prenota';
$var_pag[12] = 'utente_ins_prenota';
$var_pag[13] = 'utente_chiusura_arrpart';
$var_pag[14] = 'tariffe_uso_singola';
$var_pag[15] = 'importa_prezzo_sbagliato';
$var_pag[16] = 'cancella_prenota_permanente';
$var_pag[17] = 'nuova_pass';
$var_pag[18] = 'cancella';
$var_pag[19] = 'aggiorna_prezzi';
$var_pag[20] = 'aggiorna_disponibilita';
$var_pag[21] = 'aggiorna_restrizioni';
$var_pag[22] = 'aggiorna';
$var_pag[23] = 'data_agg';
$var_pag[24] = 'commenti_prenota';
$var_pag[25] = 'scarica_future';
$var_pag[26] = 'cont';
$var_pag[27] = 'utente_azione_ic';
$var_pag[28] = 'aggiorna_cache';
$var_pag[29] = 'interc_dir_passa';
$var_pag[30] = 'int_ric';
$var_pag[31] = 'id_ric';
$var_pag[32] = 'url_subordinazione';
$var_pag[33] = 'utente_subordinazione';
$var_pag[34] = 'password_subordinazione';
$var_pag[35] = 'commento_subordinazione';
$var_pag[36] = 'compresso_subordinazione';
$var_pag[37] = 'crea_subordinazione';
$var_pag[38] = 'mantieni_anni';
$var_pag[39] = 'modifica_utente_agg';
$var_pag[40] = 'n_utente_agg';
$var_pag[41] = 'continua';
$var_pag[42] = 'cancella_subordinazione';
$var_pag[43] = 'aggiorna_subordinazione';
$var_pag[44] = 'agg_disp';
$var_pag[45] = 'agg_tar';
$var_pag[46] = 'azione_ic';
$n_var_pag = 47;
$num1 = "";
if (isset($_POST['interc_dir_passa'])) $num1 = $_POST['interc_dir_passa'];
elseif (isset($_GET['interc_dir_passa'])) $num1 = $_GET['interc_dir_passa'];
if ($num1 and strlen($num1) < 7 and preg_match("/[a-z]{2,4}[0-9]{0,2}/",$num1) and @is_file("includes/interconnect/$num1/var.php")) include("includes/interconnect/$num1/var.php");

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
include("./includes/funzioni_backup.php");
include("./includes/funzioni_email.php");
$numconnessione = connetti_db_per_backup($PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata);
if (!isset($anno)) {
if (isset($_GET['anno'])) $anno = $_GET['anno'];
if (isset($_POST['anno'])) $anno = $_POST['anno'];
if (!isset($anno)) {
@include_once(C_DATI_PATH."/versione.php");
$anno = date("Y",(time() + (C_DIFF_ORE * 3600)));
if (@is_file(C_DATI_PATH."/selectperiodi".($anno + 1).".1.php")) $anno = $anno + 1;
elseif (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php") and @is_file(C_DATI_PATH."/selectperiodi".($anno - 1).".1.php")) $anno = $anno - 1;
} # fine if (!isset($anno))
} # fine if (!isset($anno))
include("./includes/funzioni.php");
if (function_exists('ini_set')) @ini_set('opcache.enable',0);
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablegruppi = $PHPR_TAB_PRE."gruppi";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tableversioni = $PHPR_TAB_PRE."versioni";
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableinterconnessioni = $PHPR_TAB_PRE."interconnessioni";
$tablecache = $PHPR_TAB_PRE."cache";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";

if (isset($user)) $nome_utente_phpr = $user;
if (isset($password)) $password_phpr = $password;
$id_utente_azione_ic = "";
fixset($mostra_form_iniziale);
fixset($nuova_pass);

if (defined('C_UTENTE_AZIONE_IC') and C_UTENTE_AZIONE_IC != "" and isset($utente_azione_ic) and $utente_azione_ic == C_UTENTE_AZIONE_IC) {
if (defined('C_FILE_SCADENZA_ACCOUNT') and C_FILE_SCADENZA_ACCOUNT != "") {
$scadenza = trim(@implode("",@file("./".C_FILE_SCADENZA_ACCOUNT)));
#$adesso = date("YmdHis");
$adesso = date("Ymd")."000000";
if (!$scadenza or $scadenza < $adesso) exit();
} # fine if (defined('C_FILE_SCADENZA_ACCOUNT') and C_FILE_SCADENZA_ACCOUNT != "")
#$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
#if (!numlin_query($id_utente_az)) {
$id_utente = "a";
$id_utente_azione_ic = "a";
include(C_DATI_PATH."/lingua.php");
$locale_mex = substr(strstr($lingua[1],"-"),1);
$lingua_mex = str_replace("-$locale_mex","",$lingua[1]);
if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
$id_utente_sessione = "-1";
$id_sessione = crea_id_sessione($anno,$tableversioni);
if (C_VERSIONE_ATTUALE < 3.04) $id_sessione = substr($id_sessione,0,20).substr($id_sessione,30);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if (!empty($_SERVER['REMOTE_ADDR'])) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$REMOTE_ADDR = aggslashdb(fixset($REMOTE_ADDR));
if (!empty($_SERVER['REMOTE_PORT'])) $REMOTE_PORT = $_SERVER['REMOTE_PORT'];
$REMOTE_PORT = aggslashdb(fixset($REMOTE_PORT));
if (!empty($_SERVER['HTTP_USER_AGENT'])) $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$HTTP_USER_AGENT = aggslashdb(fixset($HTTP_USER_AGENT));
if ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == "on") or (isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == "443")) $tipo_conn = "HTTPS";
else $tipo_conn = "HTTP";
$minuti_durata_sessione = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_sessione' and idutente = '1'");
$minuti_durata_sessione = risul_query($minuti_durata_sessione,0,'valpersonalizza_num');
$limite_sessioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tablesessioni where ultimo_accesso <= '$limite_sessioni_vecchie'");
esegui_query("insert into $tablesessioni (idsessioni,idutente,indirizzo_ip,tipo_conn,user_agent,ultimo_accesso) values ('$id_sessione','$id_utente_sessione','$REMOTE_ADDR','$tipo_conn','$HTTP_USER_AGENT','$ultimo_accesso')","",1);
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
#} # fine if (!numlin_query($id_utente_az))
} # fine if (defined('C_UTENTE_AZIONE_IC') and C_UTENTE_AZIONE_IC != "" and isset($utente_azione_ic) and $utente_azione_ic == C_UTENTE_AZIONE_IC)

if (!$id_utente_azione_ic) $id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);

if ($id_utente != 1 and $id_utente != "a") {
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
if (numlin_query($privilegi_globali_utente)) $priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
else $priv_mod_pers = "nnnnnn";
$priv_crea_interconnessioni = substr($priv_mod_pers,3,1);
$priv_gest_pass_cc = substr($priv_mod_pers,5,1);
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$priv_ins_tariffe = risul_query($privilegi_annuali_utente,0,'priv_ins_tariffe');
$priv_mod_reg2 = substr($priv_ins_tariffe,5,1);
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
$applica_regole1 = substr($regole1_consentite,1,1);
if ($attiva_regole1_consentite != "n" or $applica_regole1 == "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
$tariffe_consentite = risul_query($privilegi_annuali_utente,0,'tariffe_consentite');
$attiva_tariffe_consentite = substr($tariffe_consentite,0,1);
if ($attiva_tariffe_consentite == "s") {
$tariffe_consentite = explode(",",substr($tariffe_consentite,2));
$tariffe_consentite_vett = array();
for ($num1 = 0 ; $num1 < count($tariffe_consentite) ; $num1++) if ($tariffe_consentite[$num1]) $tariffe_consentite_vett[$tariffe_consentite[$num1]] = "SI";
} # fine if ($attiva_tariffe_consentite == "s")
} # fine if else if (numlin_query($privilegi_annuali_utente) == 0)
} # fine if ($id_utente != 1 and $id_utente != "a")
else {
if ($id_utente == 1) {
$priv_crea_interconnessioni = "s";
$priv_gest_pass_cc = "s";
$anno_utente_attivato = "SI";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$priv_mod_reg2 = "s";
} # fine if ($id_utente == 1)
else {
$priv_crea_interconnessioni = "n";
$priv_gest_pass_cc = "n";
$anno_utente_attivato = "SI";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$priv_mod_reg2 = "n";
} # fine else if ($id_utente == 1)
} # fine else if ($id_utente != 1 and $id_utente != "a")

if ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI") and !$id_utente_azione_ic) {
$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) == 1) $id_utente_azione_ic = risul_query($id_utente_az,0,'idlocale');
} # fine if ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI") and !$id_utente_azione_ic)

if ($id_utente and (($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI") or $id_utente == $id_utente_azione_ic) and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO")) {

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
$installazione_subordinata = "SI";
$priv_crea_interconnessioni = "n";
$priv_gest_pass_cc = "n";
$priv_mod_reg2 = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))
unset($ic_present);


$titolo = "HotelDruid: ".mex("Interconnessioni",$pag);
if (!empty($tema[$id_utente]) and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");



function scarica_backup_subordinazione ($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione) {
global $tableversioni,$pag;
$errore = "NO";
if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&crea_backup=SI")) {
$backup = @fopen($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&mostra_header=NO&salva_backup=SI&compresso=$compresso_subordinazione","r");
if ($backup) {
if ($fileaperto = fopen($file_backup,"w")) {
flock($fileaperto,2);
while (!feof($backup)) {
$linee = fread($backup,524288);
fwrite($fileaperto,$linee);
} # fine while (!feof($fbackup))
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($fileaperto = fopen($file_backup,"w"))
else {
$errore = "SI";
echo mex("Non ho i permassi di scrittura sulla cartella dati",$pag).".<br>";
} # fine else if ($fileaperto = fopen($file_backup,"w"))
fclose ($backup);
if ($errore != "SI") {
if ($compresso_subordinazione == "SI") {
if (rename($file_backup,$file_backup.".gz")) {
if ($fileaperto = fopen($file_backup,"w")) {
flock($fileaperto,2);
$gzbackup = gzopen($file_backup.".gz","r");
if ($gzbackup) {
while (!feof($gzbackup)) {
$linee = gzread($gzbackup,524288);
fwrite($fileaperto,$linee);
} # fine while (!feof($gzbackup))
} # fine if ($gzbackup)
gzclose($gzbackup);
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($fileaperto = fopen($file_backup,"w"))
unlink($file_backup.".gz");
} # fine if (rename($file_backup,$file_backup.".gz"))
} # fine if ($compresso_subordinazione == "SI")
$linee = "";
if ($fbackup = fopen($file_backup,"r")) {
$linee = fread($fbackup,524288);
fclose($fbackup);
} # fine if ($fbackup = fopen($file,"r"))
$versione_corrente = esegui_query("select * from $tableversioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
if (str_replace("<versione>$versione_corrente</versione>","",$linee) == $linee) {
$errore = "SI";
if (str_replace("<versione>","",$linee) == $linee) echo mex("Non si è potuto scaricare il backup remoto",$pag).".<br>";
else echo mex("Versione locale diversa da quella remota",$pag).".<br>";
include("./includes/funzioni_aggiorna.php");
global $agg_cod_sorg;
unset($linee);
$agg_cod_sorg = aggiorna_codice_sorgente_phpr($url_subordinazione);
if ($agg_cod_sorg) echo "<br>".mex("Aggiornamento codice sorgente eseguito con successo",$pag)."!<br><br>";
} # fine if (str_replace("<versione>$versione_corrente</versione>","",$linee) == $linee)
unset($linee);
} # fine if ($errore != "SI")
} # fine if ($backup)
else {
$errore = "SI";
echo mex("Non si è potuto stabilire la connessione",$pag).".<br>";
} # fine else if ($backup)
} # fine if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione"))
else {
$errore = "SI";
echo mex("Non si è potuto stabilire la connessione",$pag).".<br>";
} # fine else if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione"))
if ($errore != "SI") $output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&cancella_backup=SI");
return $errore;
} # fine function scarica_backup_subordinazione


function variabili_x_form (&$lista_tutti_app,&$maxocc,&$lista_regola2,&$lista_regola3,&$origini_prenota,&$campi_pers_comm,&$num_appartamenti,&$pass_cc,$priv_gest_pass_cc,$id_sessione,$tableappartamenti,$tableregole,$tablepersonalizza) {

$lista_tutti_app = "";
$maxocc = array();
$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxocc[$idapp] = risul_query($appartamenti,$num1,'maxoccupanti');
$lista_tutti_app .= "$idapp,";
} # fine for $num1
$lista_tutti_app = substr($lista_tutti_app,0,-1);
asort($maxocc);

$lista_regola2 = array();
$regole2 = esegui_query("select * from $tableregole where tariffa_per_app != ''");
$num_regole2 = numlin_query($regole2);
for ($num1 = 0 ; $num1 < $num_regole2 ; $num1++) {
$tariffa = risul_query($regole2,$num1,'tariffa_per_app');
$lista_app = risul_query($regole2,$num1,'motivazione');
if (strcmp((string) $lista_app,"")) {
$lista_regola2[$tariffa] = explode(",",$lista_app);
$lista_regola2[$tariffa]['num'] = count($lista_regola2[$tariffa]);
} # fine if (strcmp((string) $lista_app,""))
else $lista_regola2[$tariffa]['num'] = 0;
$lista_regola2[$tariffa]['txt'] = $lista_app;
$lista_app2 = risul_query($regole2,$num1,'motivazione2');
$lista_regola2[$tariffa]['txt2'] = $lista_app;
if ($lista_app and $lista_app2) $lista_regola2[$tariffa]['txt2'] .= ",$lista_app2";
$lista_regola2[$tariffa]['napp'] = risul_query($regole2,$num1,'motivazione3');
if (substr(fixstr($lista_regola2[$tariffa]['napp']),0,1) == "v") {
$lista_regola2[$tariffa]['vapp'] = 1;
$lista_regola2[$tariffa]['napp'] = substr($lista_regola2[$tariffa]['napp'],1);
} # fine if (substr($lista_regola2[$tariffa]['napp'],0,1) == "v")
} # fine for $num1

$lista_regola3 = array();
$regole3 = esegui_query("select * from $tableregole where tariffa_per_persone != '' order by tariffa_per_persone");
$num_regole3 = numlin_query($regole3);
for ($num1 = 0 ; $num1 < $num_regole3 ; $num1++) {
if (risul_query($regole3,$num1,'tariffa_per_persone')) {
$tariffa = risul_query($regole3,$num1,'tariffa_per_persone');
if (risul_query($regole3,$num1,'iddatainizio')) $lista_regola3[$tariffa] = risul_query($regole3,$num1,'iddatainizio');
if (risul_query($regole3,$num1,'iddatafine')) $lista_regola3['min'][$tariffa] = risul_query($regole3,$num1,'iddatafine');
} # fine if (risul_query($regole3,$num1,'tariffa_per_persone'))
} # fine for $num1

$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '1' ");
$origini_prenota = (string) risul_query($origini_prenota,0,"valpersonalizza");
$origini_prenota = explode(",",$origini_prenota);

$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '1' ");
if (numlin_query($campi_pers_comm) == 1) $campi_pers_comm = $campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
else $campi_pers_comm = array();

$pass_cc = 0;
if ($priv_gest_pass_cc == "s") {
if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
if (substr(C_URL_MOD_EXT_CARTE_CREDITO,0,17) == "https://localhost") $ext_html = file(C_URL_MOD_EXT_CARTE_CREDITO."personalizza.php?id_sessione=$id_sessione&stato_cc=1",false,stream_context_create(array("ssl" => array("verify_peer" => true,"allow_self_signed" => true))));
else $ext_html = @file(C_URL_MOD_EXT_CARTE_CREDITO."personalizza.php?id_sessione=$id_sessione&stato_cc=1");
if ($ext_html and strstr(implode("",$ext_html),"pass_cc_attiva")) $pass_cc = 1;
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
else {
if (function_exists('openssl_pkey_new')) {
$cert_cc = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'cert_cc' and idutente = '1'");
if (numlin_query($cert_cc)) $pass_cc = 1;
} # fine if (function_exists('openssl_pkey_new'))
} # fine else if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
} # fine if ($priv_gest_pass_cc == "s") {

} # fine function variabili_x_form



if ((isset($azione_ic) and $azione_ic == "SI") or $id_utente_azione_ic) {
$mostra_form_iniziale = "NO";
$modifica_interconnessione = "NO";


if ((!empty($aggiorna_subordinazione) or $id_utente_azione_ic) and @is_file(C_DATI_PATH."/dati_subordinazione.php")) {
include(C_DATI_PATH."/dati_subordinazione.php");
$file_backup = C_DATI_PATH."/sub_backup.php";
$filelock = crea_lock_file($file_backup);
$errore = scarica_backup_subordinazione($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione);


if ($errore != "SI") {
$file_sub = @fopen(C_DATI_PATH."/dati_subordinazione.php","w+");
if ($file_sub) {
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
flock($file_sub,2);
fwrite($file_sub,"<?php
\$url_subordinazione = \"$url_subordinazione\";
\$utente_subordinazione = \"".aggiungi_slash($utente_subordinazione)."\";
\$password_subordinazione = \"".aggiungi_slash($password_subordinazione)."\";
\$commento_subordinazione = \"".aggiungi_slash($commento_subordinazione)."\";
\$compresso_subordinazione = \"".aggiungi_slash($compresso_subordinazione)."\";
\$mantieni_anni = \"".aggiungi_slash($mantieni_anni)."\";
\$ultimo_aggiornamento = \"$adesso\";
?>");
flock($file_sub,3);
fclose($file_sub);
$N_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$N_PHPR_DB_NAME = $PHPR_DB_NAME;
$N_PHPR_DB_HOST = $PHPR_DB_HOST;
$N_PHPR_DB_PORT = $PHPR_DB_PORT;
$N_PHPR_DB_USER = $PHPR_DB_USER;
$N_PHPR_DB_PASS = $PHPR_DB_PASS;
$N_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$N_PHPR_TAB_PRE = $PHPR_TAB_PRE;
if ($id_sessione) {
$dati_sessione = esegui_query("select * from $tablesessioni where idsessioni = '$id_sessione'");
$num_lin_dati_sessione = numlin_query($dati_sessione);
} # fine if ($id_sessione)
else $num_lin_dati_sessione = 0;
if ($num_lin_dati_sessione == 1) {
$idutente = risul_query($dati_sessione,0,'idutente');
$indirizzo_ip = risul_query($dati_sessione,0,'indirizzo_ip');
$user_agent = risul_query($dati_sessione,0,'user_agent');
$ultimo_accesso = risul_query($dati_sessione,0,'ultimo_accesso');
} # fine if ($num_lin_dati_sessione == 1)
ripristina_backup($file_backup,"SI",$pag,$numconnessione,"","",$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni);
esegui_query("update $tablepersonalizza set valpersonalizza = 'SI' where idpersonalizza = 'subordinazione' and idutente = '1'");
if (@is_file(C_DATI_PATH."/dati_interconnessioni.php")) unlink(C_DATI_PATH."/dati_interconnessioni.php");
if ($num_lin_dati_sessione == 1) esegui_query("insert into $tablesessioni (idsessioni,idutente,indirizzo_ip,user_agent,ultimo_accesso) values ('$id_sessione','$idutente','$indirizzo_ip','$user_agent','$ultimo_accesso')","",1);
echo mex("Aggiornamento eseguito con successo",$pag).".<br>";
} # fine if ($file_sub)
} # fine if ($errore != "SI")
elseif (!$agg_cod_sorg) echo mex("Non si è potuto portare a termine l'<div style=\"display: inline; color: red;\">aggiornamento</div>",$pag).".<br>";
@unlink($file_backup);
distruggi_lock_file($filelock,$file_backup);
} # fine if ((!empty($aggiorna_subordinazione) or...


if ($id_utente_azione_ic) {
$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if (isset($ic_present[$interconnection_name]) and $ic_present[$interconnection_name] == "SI" and (empty($id_ric) or $int_ric == $interconnection_name)) {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
$id_utente_origi = $id_utente;
$id_utente = 1;
if (!isset($id_ric)) $id_ric = "";
$testo = $funz_import_reservations("",$id_ric,$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME);
echo $testo;

$ultimo_aggiornamento_tariffe = esegui_query("select idremoto1 from $tableinterconnessioni where tipoid = 'u_agg_tar' and nome_ic = '$interconnection_name' ");
if (numlin_query($ultimo_aggiornamento_tariffe)) $ultimo_aggiornamento_tariffe = risul_query($ultimo_aggiornamento_tariffe,0,"idremoto1");
else {
$ultimo_aggiornamento_tariffe = "19820711215100";
esegui_query("insert into $tableinterconnessioni (tipoid,nome_ic,idremoto1) values ('u_agg_tar','$interconnection_name','$ultimo_aggiornamento_tariffe') ");
} # fine else if (numlin_query($ultimo_aggiornamento_tariffe))
# Aggiorno ogni 2 giorni
$limite = date("YmdHis",mktime(date("H"),date("i"),(date("s") + (C_DIFF_ORE * 3600)),date("m"),(date("d") - 2),date("Y")));
if ($ultimo_aggiornamento_tariffe < $limite) {
$agg_tariffe_da_remoto = 1;
variabili_x_form($lista_tutti_app,$maxocc,$lista_regola2,$lista_regola3,$origini_prenota,$campi_pers_comm,$num_appartamenti,$pass_cc,$priv_gest_pass_cc,$id_sessione,$tableappartamenti,$tableregole,$tablepersonalizza);
include("./includes/interconnect/$mod_ext/form.php");
$ultimo_aggiornamento_tariffe = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
esegui_query("update $tableinterconnessioni set idremoto1 = '$ultimo_aggiornamento_tariffe' where tipoid = 'u_agg_tar' and nome_ic = '$interconnection_name' ");
} # fine if ($ultimo_aggiornamento_tariffe < $limite)

$id_utente = $id_utente_origi;
} # fine if (isset($ic_present[$interconnection_name]) and $ic_present[$interconnection_name] == "SI" and (empty($id_ric) or...
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);

if (empty($agg_disp) or empty($agg_tar)) {
$minuti_aspetta_agg = 25;
$limite_aspetta_aggiornamento = date("Y-m-d H:i:s",(time() - ($minuti_aspetta_agg * 60) + (C_DIFF_ORE * 3600)));
$errore_aggiornamento = esegui_query("select * from $tablecache where (numero = '11' or numero = '12') and tipo = 'error_ic' and data_modifica < '$limite_aspetta_aggiornamento' ");
$num_err = numlin_query($errore_aggiornamento);
for ($num1 = 0 ; $num1 < $num_err ; $num1++) {
$tipo_err = risul_query($errore_aggiornamento,$num1,'numero');
if ($tipo_err == '11') $agg_disp = 1;
if ($tipo_err == '12') $agg_tar = 1;
} # fine for $num1
} # fine if (empty($agg_disp) or empty($agg_tar))
else $num_err = 0;
if (!empty($agg_disp) or !empty($agg_tar)) {
$lock = 1;
if (!empty($agg_disp)) $aggiorna_disp = 1;
else $aggiorna_disp = 0;
if (!empty($agg_tar)) $aggiorna_tar = 1;
else $aggiorna_tar = 0;
if (@function_exists('pcntl_fork')) include("./includes/interconnect/aggiorna_ic_fork.php");
else include("./includes/interconnect/aggiorna_ic.php");
} # fine if (!empty($agg_disp) or !empty($agg_tar))
if ($num_err) {
if (!empty($agg_disp)) esegui_query("delete from $tablecache where numero = '11' and tipo = 'error_ic' ");
if (!empty($agg_tar)) esegui_query("delete from $tablecache where numero = '12' and tipo = 'error_ic' ");
} # fine if ($num_err)

} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))
} # fine if ($id_utente_azione_ic)


if ($mostra_form_iniziale == "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input class=\"sbutton\" type=\"submit\" name=\"indietro\" value=\"".mex("OK",$pag)."\">
</div></form>";
} # fine if ($mostra_form_iniziale == "NO")


} # fine if ((isset($azione_ic) and $azione_ic == "SI") or $id_utente_azione_ic)



variabili_x_form($lista_tutti_app,$maxocc,$lista_regola2,$lista_regola3,$origini_prenota,$campi_pers_comm,$num_appartamenti,$pass_cc,$priv_gest_pass_cc,$id_sessione,$tableappartamenti,$tableregole,$tablepersonalizza);



if (fixset($modifica_interconnessione) == "SI") {
$mostra_form_iniziale = "NO";


if (!empty($modulo) and $priv_crea_interconnessioni == "s") {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($modulo == $interconnection_name) {
$agg_tariffe_da_remoto = 0;
include("./includes/interconnect/$mod_ext/form.php");
} # fine if ($modulo == $interconnection_name)
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (!empty($modulo) and $priv_crea_interconnessioni == "s")


else {

if (!empty($crea_subordinazione)) {
$file_backup = C_DATI_PATH."/sub_backup.php";
$filelock = crea_lock_file($file_backup);
$errore = "NO";
if (@get_magic_quotes_gpc()) {
$url_subordinazione = stripslashes($url_subordinazione);
$utente_subordinazione = stripslashes($utente_subordinazione);
$password_subordinazione = stripslashes($password_subordinazione);
} # fine if (@get_magic_quotes_gpc())
if ($compresso_subordinazione != "SI") $compresso_subordinazione = "";
if (!isset($mantieni_anni) or $mantieni_anni != 1) $mantieni_anni = "";
if (substr($url_subordinazione,0,7) != "http://" and substr($url_subordinazione,0,8) != "https://") $errore = "SI";
else {
if (substr($url_subordinazione,-1) != "/") $url_subordinazione .= "/";
$errore = scarica_backup_subordinazione($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione);
} # fine else if (substr($url_subordinazione,0,7) != "http://")

if ($errore != "SI") {
if (empty($continua)) {
echo "<br><big>".mex("<div style=\"display: inline; color: red;\"><b>ATTENZIONE</b></div>: premendo su <b>\"<i>Continua</i>\"</b> tutti i dati del <i>database attuale</i> verranno <b>cancellati</b>",$pag)."!</big><br><br>
<div style=\"text-align: center;\"><form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input type=\"hidden\" name=\"crea_subordinazione\" value=\"SI\">
<input type=\"hidden\" name=\"url_subordinazione\" value=\"$url_subordinazione\">
<input type=\"hidden\" name=\"utente_subordinazione\" value=\"$utente_subordinazione\">
<input type=\"hidden\" name=\"password_subordinazione\" value=\"$password_subordinazione\">
<input type=\"hidden\" name=\"commento_subordinazione\" value=\"$commento_subordinazione\">
<input type=\"hidden\" name=\"compresso_subordinazione\" value=\"$compresso_subordinazione\">
<input type=\"hidden\" name=\"mantieni_anni\" value=\"$mantieni_anni\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Continua",$pag)."\">
</div></form></div><br>";
} # fine if (empty($continua))
else {
$file_sub = @fopen(C_DATI_PATH."/dati_subordinazione.php","w+");
if ($file_sub) {
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
flock($file_sub,2);
fwrite($file_sub,"<?php
\$url_subordinazione = \"$url_subordinazione\";
\$utente_subordinazione = \"".aggiungi_slash($utente_subordinazione)."\";
\$password_subordinazione = \"".aggiungi_slash($password_subordinazione)."\";
\$commento_subordinazione = \"".aggiungi_slash(htmlspecialchars($commento_subordinazione,ENT_COMPAT))."\";
\$compresso_subordinazione = \"".aggiungi_slash($compresso_subordinazione)."\";
\$mantieni_anni = \"".aggiungi_slash($mantieni_anni)."\";
\$ultimo_aggiornamento = \"$adesso\";
?>");
flock($file_sub,3);
fclose($file_sub);
$N_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$N_PHPR_DB_NAME = $PHPR_DB_NAME;
$N_PHPR_DB_HOST = $PHPR_DB_HOST;
$N_PHPR_DB_PORT = $PHPR_DB_PORT;
$N_PHPR_DB_USER = $PHPR_DB_USER;
$N_PHPR_DB_PASS = $PHPR_DB_PASS;
$N_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$N_PHPR_TAB_PRE = $PHPR_TAB_PRE;
ripristina_backup($file_backup,"SI",$pag,$numconnessione,"","",$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni);
esegui_query("update $tablepersonalizza set valpersonalizza = 'SI' where idpersonalizza = 'subordinazione' and idutente = '1'");
echo mex("Subordinazione creata",$pag).".<br>";
} # fine if ($file_sub)
} # fine else if (empty($continua))
} # fine if ($errore != "SI")

else {
echo mex("Impossibile effettuare il collegamento, controllare i dati immessi",$pag).".<br>";
} # fine else if ($errore != "SI")
@unlink($file_backup);
distruggi_lock_file($filelock,$file_backup);
} # fine if (!empty($crea_subordinazione))


if (!empty($cancella_subordinazione)) {
@unlink(C_DATI_PATH."/dati_subordinazione.php");
esegui_query("update $tablepersonalizza set valpersonalizza = 'NO' where idpersonalizza = 'subordinazione' and idutente = '1'");
echo mex("Subordinazione cancellata",$pag).".<br>";
} # fine if (!empty($cancella_subordinazione))


if (!empty($modifica_utente_agg) and !empty($n_utente_agg) and $n_utente_agg != "1" and controlla_num_pos($n_utente_agg) == "SI") {
$utente_esistente = esegui_query("select * from $tableutenti where idutenti = '".aggslashdb($n_utente_agg)."'");
if (numlin_query($utente_esistente) > 0) {
$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) > 0) esegui_query("update $tableinterconnessioni set idlocale = '$n_utente_agg' where tipoid = 'id_utente_az' ");
else esegui_query("insert into $tableinterconnessioni (idlocale,tipoid) values ('$n_utente_agg','id_utente_az') ");
echo "".mex("Utente per l'aggiornamento remoto modificato",$pag).".<br>";
} # fine if (numlin_query($utente_esistente) > 0)
} # fine if (!empty($modifica_utente_agg) and !empty($n_utente_agg) and...

} # fine else if (!empty($modulo) and $priv_crea_interconnessioni == "s")


if ($mostra_form_iniziale == "NO") {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";
} # fine if ($mostra_form_iniziale == "NO")

} # fine if (fixset($modifica_interconnessione) == "SI")




if ($mostra_form_iniziale != "NO") {
include("./includes/sett_gio.php");

# Pagina iniziale
echo "<h3 id=\"h_intc\"><span>".mex("Interconnessioni con sorgenti esterne di dati",$pag).".</span></h3>
<hr style=\"width: 95%\">";

$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) == 1) $id_utente_azione_ic = risul_query($id_utente_az,0,"idlocale");
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input type=\"hidden\" name=\"modifica_utente_agg\" value=\"SI\">
".mex("Utente per l'aggiornamento remoto delle interconnessioni",$pag).":
 <select name=\"n_utente_agg\">";
echo "<option value=\"\">----</option>";
$tutti_utenti = esegui_query("select idutenti,nome_utente from $tableutenti where idutenti != '1' order by idutenti");
$num_tutti_utenti = numlin_query($tutti_utenti);
for ($num1 = 0 ; $num1 < $num_tutti_utenti ; $num1++) {
$nome_utente = risul_query($tutti_utenti,$num1,'nome_utente');
$idutenti = risul_query($tutti_utenti,$num1,"idutenti");
if ($idutenti == $id_utente_azione_ic) {
$sel = " selected";
$nome_utente_agg = $nome_utente;
} # fine if ($idutenti == $id_utente_azione_ic)
else $sel = "";
echo "<option value=\"$idutenti\"$sel>$nome_utente</option>";
} # fine for $num1
echo "</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Modifica",$pag)."\">";
if (!empty($nome_utente_agg)) {
$url_corr = "http://";
if ($_SERVER['HTTPS']) $url_corr = "https://";
$url_corr .= $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
echo "<br><div class=\"separ\" style=\"height: 3px;\"></div>
<small>(URL: ".$url_corr."?user=$nome_utente_agg&amp;password=XXXXXX)</small>";
} # fine if (!empty($nome_utente_agg))
echo "</div></form><hr style=\"width: 95%\">";

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"cancella_subordinazione\" value=\"".mex("Cancella",$pag)."\">
 ".mex("la subordinazione",$pag).".<br>
</div></form>";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))
else {

if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") echo "<div style=\"text-align: center;\"><b>".mex("Crea la subordinazione",$pag)."</b></div><br>";
else echo "<div style=\"text-align: center;\"><b>".mex("Subordinazione ad un'altra installazione di hoteldruid",$pag)."</b></div><br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<div style=\"line-height: 1.3;\">
".mex("Con la subordinazione non si potranno inserire nuovi dati o apportare modifiche, ma solo importare i dati dall'installazione principale",$pag).".<br>";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") echo str_replace("hoteldruid","",mex("Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo",$pag)).":<br>";
else echo mex("Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo",$pag).":<br>";
echo "<input type=\"text\" name=\"url_subordinazione\" value=\"http://\" size=60><br>
".ucfirst(mex("amministratore o utente con i privilegi per creare backup",$pag)).":<br>
<table cellspacing=\"0\"><tr><td style=\"width: 30px;\"></td><td>
".ucfirst(mex("nome utente",$pag)).":
<input type=\"text\" name=\"utente_subordinazione\" size=20><br>
".mex("Password",$pag).":
<input type=\"text\" name=\"password_subordinazione\" size=20></td></tr></table>
".mex("Commento da aggiungere al titolo di questa installazione",$pag).":
<input type=\"text\" name=\"commento_subordinazione\" size=20><br>
<label><input type=\"checkbox\" name=\"compresso_subordinazione\" value=\"SI\" checked> ".mex("Usa compressione",$pag)."</label><br>
<label><input type=\"checkbox\" name=\"mantieni_anni\" value=\"1\"> ".mex("Prova a mantenere i dati degli anni non presenti nell'installazione remota",$pag)."</label><br></div>
<div style=\"text-align: center;\"><input class=\"sbutton\" type=\"submit\" name=\"crea_subordinazione\" value=\"".mex("Crea la subordinazione",$pag)."\">
</div></div></form>";

} # fine else if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))


if ($priv_crea_interconnessioni == "s") {

$interconn_dir = opendir("./includes/interconnect/");
$interconnessioni = array();
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) $interconnessioni[$mod_ext] = 1;
} # fine while ($mod_ext = readdir($interconn_dir))
ksort($interconnessioni);
reset($interconnessioni);
foreach ($interconnessioni as $mod_ext => $val_i) {
include("./includes/interconnect/$mod_ext/name.php");
if (!empty($interconnection_name_show[$lingua_mex])) $titolo = $interconnection_name_show[$lingua_mex];
else $titolo = $interconnection_name_show['en'];
echo "<hr style=\"width: 95%\"><div id=\"h$interconnection_data_dir\" style=\"text-align: center;\"><b>$titolo</b></div><br>";
$agg_tariffe_da_remoto = 0;
include("./includes/interconnect/$mod_ext/form.php");
} # fine foreach ($interconnessioni as $mod_ext => $val_i)
closedir($interconn_dir);

if (!empty($ic_present) and defined('C_URL_NUOVI_APP') and C_URL_NUOVI_APP) echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\"><br>".mex("Se vuoi collegare altri account per una interconnessione già attiva inquesta pagina, allora",$pag)." <a href=\"".C_URL_NUOVI_APP."\" target=\"_blank\">".mex("aggiungi altre interconnessioni",$pag)."</a>.<br><br>";


$templates_dir = opendir("./includes/templates/");
$modelli = array();
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) $modelli[$modello_ext] = 1;
} # fine while ($file = readdir($lang_dig))
ksort($modelli);
reset($modelli);
foreach ($modelli as $modello_ext => $val_i) {
include("./includes/templates/$modello_ext/name.php");
if (fixset($template_name_show['tpl_type']) == "interconnection") {
if (!function_exists('mex2')) {
$perc_cart_mod_sel = "";
include("./includes/templates/funzioni_modelli.php");
#$percorso_cartella_modello = $perc_cart_mod_vett[0];
$anno_modello = $anno;
$lingua_modello = $lingua_mex;
} # fine if (!function_exists('mex2'))
if (!empty($template_name_show[$lingua_mex])) $titolo = $template_name_show[$lingua_mex];
else {
$messaggio = "";
if (@is_file("./includes/lang/$lingua_mex/modt_$modello_ext.php")) {
$messaggio = $template_name_show['ita'];
include("./includes/lang/$lingua_mex/modt_$modello_ext.php");
if ($messaggio == $template_name_show['ita']) $messaggio = "";
} # fine if (@is_file("./includes/lang/$lingua_mex/modt_rat.php"))
if ($messaggio) $titolo = $messaggio;
else $titolo = $template_name_show['en'];
} # fine else if ($template_name_show[$lingua_mex])
echo "<hr style=\"width: 95%\"><div id=\"h".$modello_ext."\" style=\"text-align: center;\"><b>$titolo</b></div><br>";
$agg_tariffe_da_remoto = 0;
include("./includes/templates/$modello_ext/form.php");
} # fine if (fixset($template_name_show['tpl_type']) == "interconnection")
} # fine foreach ($modelli as $modello_ext => $val_i)
closedir($templates_dir);

} # fine if ($priv_crea_interconnessioni == "s")

echo "
<script type=\"text/javascript\">
<!--
var sel_memu = document.getElementsByTagName('select');
for (var n1 = 0 ; n1 < sel_memu.length ; n1++) {
if (sel_memu[n1].querySelector('option[selected]')) {
sel_memu[n1].querySelector('option[selected]').selected = true;
}
}
-->
</script>
<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";

} # fine if ($mostra_form_iniziale != "NO")



if (!empty($tema[$id_utente]) and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and (($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI") or $id_utente == $id_utente_azione_ic) and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO"))
elseif ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI")) {
$titolo = "HotelDruid: ".mex("Interconnessioni",$pag);
if (!empty($tema[$id_utente]) and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
echo "<br>".mex("Non hai i privilegi per accedere a questa pagina",'funzioni.php').". ".mex("Accedi come utente amministratore o altro utente abilitato",'funzioni.php').".<br>";
if (!empty($tema[$id_utente]) and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");
} # fine elseif ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI"))




?>
