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



if (function_exists('mb_convert_case')) {
function par_maius ($str,$modo="ma1mi") {
if ($modo == "maiu") $str = mb_convert_case($str,MB_CASE_UPPER,'UTF-8');
if ($modo == "minu") $str = mb_convert_case($str,MB_CASE_LOWER,'UTF-8');
if ($modo == "ma1mi") $str = mb_convert_case($str,MB_CASE_TITLE,'UTF-8');
if ($modo == "ma1or") {
$str = explode(" ",$str);
for ($num1 = 0 ; $num1 < count($str) ; $num1++) $str[$num1] = mb_convert_case(mb_substr($str[$num1],0,1),MB_CASE_UPPER,'UTF-8').mb_substr($str[$num1],1);
$str = implode(" ",$str);
} # fine if ($modo == "ma1or")
return $str;
} # fine function par_maius
} # fine if (function_exists('mb_convert_case'))
else {
function par_maius ($str,$modo="ma1mi") {
if ($modo == "maiu") $str = strtoupper($str);
if ($modo == "ma1mi" or $modo == "minu") $str = strtolower($str);
if ($modo == "ma1mi" or $modo == "ma1or") $str = ucwords($str);
return $str;
} # fine function par_maius
} # fine else if (function_exists('mb_convert_case'))




function inserisci_dati_cliente (&$cognome,&$cognome2,&$nome,$soprannome,$titolo_cli,$sesso,$mesenascita,$giornonascita,$annonascita,&$nazionenascita,&$cittanascita,&$regionenascita,$documento,$tipodoc,$mesescaddoc,$giornoscaddoc,$annoscaddoc,&$cittadoc,&$regionedoc,&$nazionedoc,&$nazionalita,&$lingua_cli,&$nazione,&$citta,&$regione,&$via,$nomevia,$numcivico,$cap,$telefono,$telefono2,$telefono3,$fax,$email,$email2,$email_cert,$cod_fiscale,$partita_iva,$max_num_ordine,$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti,$idclienti="",$valida="",$campi_pers_vett="") {
global $lingua_mex,$HOSTNAME,$id_utente,$PHPR_TAB_PRE;
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";

$secondo_cognome = esegui_query("select idpersonalizza from $tablepersonalizza where idpersonalizza = 'sec_cogn' and idutente = '$id_utente' ");
if (numlin_query($secondo_cognome)) $secondo_cognome = 1;
else $secondo_cognome = 0;
if (isset($campi_pers_vett) and is_array($campi_pers_vett) and !empty($campi_pers_vett['num'])) $num_campi_pers = $campi_pers_vett['num'];
else $num_campi_pers = 0;
if (C_VERSIONE_ATTUALE < 3.06) $stile_nomi = "ma1or";
else {
$stile_nomi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'stile_nomi' and idutente = '$id_utente' ");
if (!numlin_query($stile_nomi)) $stile_nomi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'stile_nomi' and idutente = '1' ");
$stile_nomi = risul_query($stile_nomi,0,'valpersonalizza');
} # fine else if (C_VERSIONE_ATTUALE < 3.06)


if ($valida != "NO") {
if (@get_magic_quotes_gpc()) {
$cognome = stripslashes($cognome);
$cognome2 = stripslashes($cognome2);
$nome = stripslashes($nome);
$soprannome = stripslashes($soprannome);
$titolo_cli = stripslashes($titolo_cli);
$documento = stripslashes($documento);
$tipodoc = stripslashes($tipodoc);
$cittadoc = stripslashes($cittadoc);
$regionedoc = stripslashes($regionedoc);
$nazionedoc = stripslashes($nazionedoc);
$cittanascita = stripslashes($cittanascita);
$regionenascita = stripslashes($regionenascita);
$nazionenascita = stripslashes($nazionenascita);
$nazionalita = stripslashes($nazionalita);
$nazione = stripslashes($nazione);
$regione = stripslashes($regione);
$citta = stripslashes($citta);
$nomevia = stripslashes($nomevia);
$numcivico = stripslashes($numcivico);
$cap = stripslashes($cap);
$telefono = stripslashes($telefono);
$telefono2 = stripslashes($telefono2);
$telefono3 = stripslashes($telefono3);
$fax = stripslashes($fax);
$email = stripslashes($email);
$email2 = stripslashes($email2);
$email_cert = stripslashes($email_cert);
$cod_fiscale = stripslashes($cod_fiscale);
$partita_iva = stripslashes($partita_iva);
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) $campi_pers_vett['val'][$num1] = stripslashes($campi_pers_vett['val'][$num1]);
} # fine if (@get_magic_quotes_gpc())
$cognome = htmlspecialchars($cognome,ENT_COMPAT);
$cognome2 = htmlspecialchars((string) $cognome2,ENT_COMPAT);
$nome = htmlspecialchars((string) $nome,ENT_COMPAT);
$soprannome = htmlspecialchars((string) $soprannome,ENT_COMPAT);
$titolo_cli = htmlspecialchars((string) $titolo_cli,ENT_COMPAT);
$documento = htmlspecialchars((string) $documento,ENT_COMPAT);
$tipodoc = htmlspecialchars((string) $tipodoc,ENT_COMPAT);
$cittadoc = htmlspecialchars((string) $cittadoc,ENT_COMPAT);
$regionedoc = htmlspecialchars((string) $regionedoc,ENT_COMPAT);
$nazionedoc = htmlspecialchars((string) $nazionedoc,ENT_COMPAT);
$cittanascita = htmlspecialchars((string) $cittanascita,ENT_COMPAT);
$regionenascita = htmlspecialchars((string) $regionenascita,ENT_COMPAT);
$nazionenascita = htmlspecialchars((string) $nazionenascita,ENT_COMPAT);
$nazionalita = htmlspecialchars((string) $nazionalita,ENT_COMPAT);
$nazione = htmlspecialchars((string) $nazione,ENT_COMPAT);
$regione = htmlspecialchars((string) $regione,ENT_COMPAT);
$citta = htmlspecialchars((string) $citta,ENT_COMPAT);
$nomevia = htmlspecialchars((string) $nomevia,ENT_COMPAT);
$numcivico = htmlspecialchars((string) $numcivico,ENT_COMPAT);
$cap = htmlspecialchars((string) $cap,ENT_COMPAT);
$telefono = htmlspecialchars((string) $telefono,ENT_COMPAT);
$telefono2 = htmlspecialchars((string) $telefono2,ENT_COMPAT);
$telefono3 = htmlspecialchars((string) $telefono3,ENT_COMPAT);
$fax = htmlspecialchars((string) $fax,ENT_COMPAT);
$email = htmlspecialchars((string) $email,ENT_COMPAT);
$email2 = htmlspecialchars((string) $email2,ENT_COMPAT);
$email_cert = htmlspecialchars((string) $email_cert,ENT_COMPAT);
$cod_fiscale = htmlspecialchars((string) $cod_fiscale,ENT_COMPAT);
$partita_iva = htmlspecialchars((string) $partita_iva,ENT_COMPAT);
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) $campi_pers_vett['val'][$num1] = htmlspecialchars((string) $campi_pers_vett['val'][$num1],ENT_COMPAT);
} # fine if ($valida != "NO")

if (!$idclienti) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idclienti = esegui_query("select idclienti from $tableclienti where idclienti = '1'");
if (numlin_query($idclienti) == 0) $idclienti = 1;
else {
$idclienti = esegui_query("select max(idclienti) from $tableclienti");
$idclienti = risul_query($idclienti,0,0) + 1;
} # fine else if (numlin_query($idclienti) == 0)
$cognome_maius = trim(par_maius($cognome,$stile_nomi));
if ($attiva_prefisso_clienti == "p") $cognome_maius = $prefisso_clienti.$cognome_maius;
if ($attiva_prefisso_clienti == "s") $cognome_maius = $cognome_maius.$prefisso_clienti;
esegui_query("insert into $tableclienti (idclienti,cognome,idclienti_compagni,datainserimento,hostinserimento,utente_inserimento) values ('$idclienti','".aggslashdb(par_taglia($cognome_maius,0,70))."',',','$datainserimento','$HOSTNAME','$id_utente_ins')");
} # fine if (!$idclienti)
elseif ($cognome) {
$cognome_maius = trim(par_maius($cognome,$stile_nomi));
if ($attiva_prefisso_clienti == "p") $cognome_maius = $prefisso_clienti.$cognome_maius;
if ($attiva_prefisso_clienti == "s") $cognome_maius = $cognome_maius.$prefisso_clienti;
esegui_query("update $tableclienti set cognome = '".aggslashdb(par_taglia($cognome_maius,0,70))."' where idclienti = '$idclienti' ");
if (!$secondo_cognome and C_VERSIONE_ATTUALE > 3.07) esegui_query("update $tableclienti set cognome2 = NULL where idclienti = '$idclienti' ");
} # fine elseif ($cognome)
if ($secondo_cognome and $cognome2) {
$cognome2_maius = trim(par_maius($cognome2,$stile_nomi));
esegui_query("update $tableclienti set cognome2 = '".aggslashdb(par_taglia($cognome2_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($secondo_cognome and $cognome2)
if ($nome) {
$nome_maius = trim(par_maius($nome,$stile_nomi));
esegui_query("update $tableclienti set nome = '".aggslashdb(par_taglia($nome_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nome)
if ($soprannome) {
esegui_query("update $tableclienti set soprannome = '".aggslashdb(par_taglia($soprannome,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($soprannome)
if ($titolo_cli) {
$titoli_cliente = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'titoli_cliente' and idutente = '$id_utente'");
if (numlin_query($titoli_cliente) == 1) {
$titoli_cliente = risul_query($titoli_cliente,0,'valpersonalizza');
$titoli_cliente = explode(">",$titoli_cliente);
for ($num1 = 0 ; $num1 < count($titoli_cliente) ; $num1++) {
$tito = explode("<",$titoli_cliente[$num1]);
if ($titolo_cli == $tito[0]) {
esegui_query("update $tableclienti set titolo = '".aggslashdb(par_taglia($titolo_cli,0,30))."' where idclienti = '$idclienti' ");
if ($tito[1] and !$sesso) $sesso = $tito[1];
break;
} # fine if ($titolo_cli == $opt[0])
} # fine for $num1
} # fine if (numlin_query($titoli_cliente) == 1)
} # fine if ($titolo_cli)
if ($sesso) {
if ($sesso != "f") $sesso = "m";
esegui_query("update $tableclienti set sesso = '$sesso' where idclienti = '$idclienti' ");
} # fine if ($sesso)
if ($mesenascita and $giornonascita and strlen($annonascita) == 4) {
if ($annonascita > 1970) $datanascita = date("Y-m-d",mktime(0,0,0,(int) $mesenascita,(int) $giornonascita,(int) $annonascita));
else {
$mesenascita = (int) $mesenascita;
$giornonascita = (int) $giornonascita;
$annonascita = (int) $annonascita;
if ($mesenascita > 12) $mesenascita = 12;
if ($giornonascita > 31) $giornonascita = 31;
if ($giornonascita > 30 and ($mesenascita == 4 or $mesenascita == 6 or $mesenascita == 9 or $mesenascita == 11)) $giornonascita = 30;
if ($giornonascita > 28 and $mesenascita == 2) {
if ($annonascita == 1968 or $annonascita == 1964 or $annonascita == 1960 or $annonascita == 1956 or $annonascita == 1952 or $annonascita == 1948 or $annonascita == 1944 or $annonascita == 1940 or $annonascita == 1936 or $annonascita == 1932) $giornonascita = 29;
else $giornonascita = 28;
} # fine if ($giornonascita > 28 and $mesenascita == 2)
if (strlen($mesenascita) < 2) $mesenascita = "0".$mesenascita;
if (strlen($giornonascita) < 2) $giornonascita = "0".$giornonascita;
while (strlen($annonascita) < 4) $annonascita = "1".$annonascita;
$datanascita = $annonascita."-".$mesenascita."-".$giornonascita;
} # fine else if ($annonascita > 1970)
$datanascita = aggslashdb($datanascita);
esegui_query("update $tableclienti set datanascita = '$datanascita' where idclienti = '$idclienti' ");
} # fine if ($mesenascita and ...
if ($nazionenascita) {
$nazionenascita_maius = trim(par_maius($nazionenascita,$stile_nomi));
esegui_query("update $tableclienti set nazionenascita = '".aggslashdb(par_taglia($nazionenascita_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nazionenascita)
if ($cittanascita) {
$cittanascita_maius = trim(par_maius($cittanascita,$stile_nomi));
esegui_query("update $tableclienti set cittanascita = '".aggslashdb(par_taglia($cittanascita_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($cittanascita)
if ($regionenascita) {
$regionenascita_maius = trim(par_maius($regionenascita,$stile_nomi));
esegui_query("update $tableclienti set regionenascita = '".aggslashdb(par_taglia($regionenascita_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($regionenascita)
if ($documento) {
if ($tipodoc) esegui_query("update $tableclienti set tipodoc = '".aggslashdb(par_taglia($tipodoc,0,70))."' where idclienti = '$idclienti' ");
esegui_query("update $tableclienti set documento = '".aggslashdb(par_taglia($documento,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($documento)
if ($mesescaddoc and $giornoscaddoc and $annoscaddoc) {
$datascaddoc = date("Y-m-d",mktime(0,0,0,(int) $mesescaddoc,(int) $giornoscaddoc,(int) $annoscaddoc));
$datascaddoc = aggslashdb($datascaddoc);
esegui_query("update $tableclienti set scadenzadoc = '$datascaddoc' where idclienti = '$idclienti' ");
} # fine if ($mesescaddoc and ...
if ($nazionedoc) {
$nazionedoc_maius = trim(par_maius($nazionedoc,$stile_nomi));
esegui_query("update $tableclienti set nazionedoc = '".aggslashdb(par_taglia($nazionedoc_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nazionedoc)
if ($cittadoc) {
$cittadoc_maius = trim(par_maius($cittadoc,$stile_nomi));
esegui_query("update $tableclienti set cittadoc = '".aggslashdb(par_taglia($cittadoc_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($cittadoc)
if ($regionedoc) {
$regionedoc_maius = trim(par_maius($regionedoc,$stile_nomi));
esegui_query("update $tableclienti set regionedoc = '".aggslashdb(par_taglia($regionedoc_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($regionedoc)
if ($nazionalita) {
$nazionalita_maius = trim(par_maius($nazionalita,$stile_nomi));
esegui_query("update $tableclienti set nazionalita = '".aggslashdb(par_taglia($nazionalita_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nazionalita)
if ($lingua_cli) {
if (preg_replace("/[a-z]{2,3}/","",$lingua_cli) == "") {
if ($lingua_cli == $lingua_mex or $lingua_cli == "ita" or @is_dir("./includes/lang/$lingua_cli")) {
esegui_query("update $tableclienti set lingua = '".aggslashdb(par_taglia($lingua_cli,0,14))."' where idclienti = '$idclienti' ");
} # fine if ($lingua == $lingua_mex or $lingua_cli == "ita" or...
else $lingua_cli = "";
} # fine if (preg_replace("/[a-z]{2,3}/","",$lingua_cli) == "")
else $lingua_cli = "";
} # fine if ($lingua_cli)
if ($nazione) {
$nazione_maius = trim(par_maius($nazione,$stile_nomi));
esegui_query("update $tableclienti set nazione = '".aggslashdb(par_taglia($nazione_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nazione)
if ($citta) {
$citta_maius = trim(par_maius($citta,$stile_nomi));
esegui_query("update $tableclienti set citta = '".aggslashdb(par_taglia($citta_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($citta)
if ($regione) {
$regione_maius = trim(par_maius($regione,$stile_nomi));
esegui_query("update $tableclienti set regione = '".aggslashdb(par_taglia($regione_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($regione)
if ($nomevia) {
if (strcmp((string) $via,"")) {
if ($lingua_mex != "ita") include("./includes/lang/$lingua_mex/ordine_frasi.php");
if (isset($ordine_strada) and $ordine_strada == 2) $via = $nomevia." ".$via;
else $via = $via." ".$nomevia;
} # fine if (strcmp((string) $via,""))
else $via = $nomevia;
$via_maius = trim(par_maius($via,$stile_nomi));
esegui_query("update $tableclienti set via = '".aggslashdb(par_taglia($via_maius,0,70))."' where idclienti = '$idclienti' ");
} # fine if ($nomevia)
if ($numcivico) esegui_query("update $tableclienti set numcivico = '".aggslashdb(par_taglia($numcivico,0,30))."' where idclienti = '$idclienti' ");
if ($cap) esegui_query("update $tableclienti set cap = '".aggslashdb(par_taglia($cap,0,30))."' where idclienti = '$idclienti' ");
if ($telefono) esegui_query("update $tableclienti set telefono = '".aggslashdb(par_taglia($telefono,0,50))."' where idclienti = '$idclienti' ");
if ($telefono2) esegui_query("update $tableclienti set telefono2 = '".aggslashdb(par_taglia($telefono2,0,50))."' where idclienti = '$idclienti' ");
if ($telefono3) esegui_query("update $tableclienti set telefono3 = '".aggslashdb(par_taglia($telefono3,0,50))."' where idclienti = '$idclienti' ");
if ($fax) esegui_query("update $tableclienti set fax = '".aggslashdb(par_taglia($fax,0,50))."' where idclienti = '$idclienti' ");
$email = aggslashdb($email);
if ($email) esegui_query("update $tableclienti set email = '$email' where idclienti = '$idclienti' ");
$email2 = aggslashdb($email2);
if ($email2) esegui_query("update $tableclienti set email2 = '$email2' where idclienti = '$idclienti' ");
$email_cert = aggslashdb($email_cert);
if ($email_cert) esegui_query("update $tableclienti set email3 = '$email_cert' where idclienti = '$idclienti' ");
$cod_fiscale = aggslashdb(par_taglia($cod_fiscale,0,50));
if ($cod_fiscale) esegui_query("update $tableclienti set cod_fiscale = '$cod_fiscale' where idclienti = '$idclienti' ");
$partita_iva = aggslashdb(par_taglia($partita_iva,0,50));
if ($partita_iva) esegui_query("update $tableclienti set partita_iva = '$partita_iva' where idclienti = '$idclienti' ");
if ($max_num_ordine) esegui_query("update $tableclienti set max_num_ordine = '".aggslashdb($max_num_ordine)."' where idclienti = '$idclienti' ");

for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
if (strcmp((string) $campi_pers_vett['val'][$num1],"")) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tablerelclienti (idclienti,numero,tipo,testo1,testo2,testo3,datainserimento,hostinserimento,utente_inserimento) values ('$idclienti','1','campo_pers','".$campi_pers_vett[$num1]."','".$campi_pers_vett['tipo'][$num1]."','".aggslashdb($campi_pers_vett['val'][$num1])."','$datainserimento','$HOSTNAME','$id_utente_ins') ");
} # fine if (strcmp((string) $campi_pers_vett['val'][$num1],""))
} # fine for $num1

return $idclienti;

} # fine function inserisci_dati_cliente





function mostra_dati_cliente (&$dati_cliente,&$dcognome,&$dcognome2,&$dnome,&$dsoprannome,&$dtitolo_cli,&$dsesso,&$ddatanascita,&$ddatanascita_f,&$dnazionenascita,&$dcittanascita,&$dregionenascita,&$ddocumento,&$dscadenzadoc,&$dscadenzadoc_f,&$dtipodoc,&$dnazionedoc,&$dregionedoc,&$dcittadoc,&$dnazionalita,&$dlingua_cli,&$dnazione,&$dregione,&$dcitta,&$dvia,&$dnumcivico,&$dtelefono,&$dtelefono2,&$dtelefono3,&$dfax,&$dcap,&$demail,&$demail2,&$demail_cert,&$dcod_fiscale,&$dpartita_iva,$mostra_num="",$priv_ins_clienti="",$silenzio="",$mostra_commento="") {
global $pag,$id_utente,$PHPR_TAB_PRE,$stile_data,$lingua_mex,$locale_mex,$locale;

if ($id_utente == 1 or !$id_utente) {
$priv_vedi_telefoni = "s";
$priv_vedi_indirizzo = "s";
} # fine if ($id_utente == 1 or !$id_utente)
else {
if (!$priv_ins_clienti) {
$privilegi_globali_utente = esegui_query("select * from $PHPR_TAB_PRE"."privilegi where idutente = '$id_utente' and anno = '1'");
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
} # fine if (!$priv_ins_clienti)
$priv_vedi_telefoni = substr($priv_ins_clienti,3,1);
$priv_vedi_indirizzo = substr($priv_ins_clienti,4,1);
} # fine else if ($id_utente == 1 or !$id_utente)

$didclienti = risul_query($dati_cliente,0,'idclienti');
$dcognome = risul_query($dati_cliente,0,'cognome');
$dcognome2 = risul_query($dati_cliente,0,'cognome2');
$dnome = risul_query($dati_cliente,0,'nome');
$dsoprannome = risul_query($dati_cliente,0,'soprannome');
$dtitolo_cli = risul_query($dati_cliente,0,'titolo');
$dsesso = risul_query($dati_cliente,0,'sesso');
$ddatanascita = risul_query($dati_cliente,0,'datanascita');
$ddatanascita_f = formatta_data($ddatanascita,$stile_data);
$ddocumento = risul_query($dati_cliente,0,'documento');
$dtipodoc = risul_query($dati_cliente,0,'tipodoc');
$dscadenzadoc = risul_query($dati_cliente,0,'scadenzadoc');
$dscadenzadoc_f = formatta_data($dscadenzadoc,$stile_data);
$dcittadoc = risul_query($dati_cliente,0,'cittadoc');
$dregionedoc = risul_query($dati_cliente,0,'regionedoc');
$dnazionedoc = risul_query($dati_cliente,0,'nazionedoc');
$dcittanascita = risul_query($dati_cliente,0,'cittanascita');
$dregionenascita = risul_query($dati_cliente,0,'regionenascita');
$dnazionenascita = risul_query($dati_cliente,0,'nazionenascita');
$dnazionalita = risul_query($dati_cliente,0,'nazionalita');
$dlingua_cli = risul_query($dati_cliente,0,'lingua');
$dnazione = risul_query($dati_cliente,0,'nazione');
$dregione = risul_query($dati_cliente,0,'regione');
$dcitta = risul_query($dati_cliente,0,'citta');
if ($priv_vedi_indirizzo == "s") {
$dvia = risul_query($dati_cliente,0,'via');
$dnumcivico = risul_query($dati_cliente,0,'numcivico');
$dcap = risul_query($dati_cliente,0,'cap');
} # fine if ($priv_vedi_indirizzo == "s")
if ($priv_vedi_telefoni == "s") {
$dtelefono = risul_query($dati_cliente,0,'telefono');
$dtelefono2 = risul_query($dati_cliente,0,'telefono2');
$dtelefono3 = risul_query($dati_cliente,0,'telefono3');
$dfax = risul_query($dati_cliente,0,'fax');
$demail = risul_query($dati_cliente,0,'email');
$demail2 = risul_query($dati_cliente,0,'email2');
$demail_cert = risul_query($dati_cliente,0,'email3');
} # fine if ($priv_vedi_telefoni == "s")
$dcod_fiscale = risul_query($dati_cliente,0,'cod_fiscale');
$dpartita_iva = risul_query($dati_cliente,0,'partita_iva');
if ($mostra_commento) $dcommento = risul_query($dati_cliente,0,'commento');
else $dcommento = "";

if ($dlingua_cli) {
if ($dlingua_cli == "ita") $d_nome_lingua = "italiano";
elseif (preg_replace("/[a-z]{2,3}/","",$dlingua_cli) == "") {
if (@is_file("./includes/lang/$dlingua_cli/l_n")) {
$d_nome_lingua = file("./includes/lang/$dlingua_cli/l_n");
$d_nome_lingua = togli_acapo($d_nome_lingua[0]);
} # fine if (@is_file("./includes/lang/$dlingua_cli/l_n"))
if (!isset($locale)) {
if ($lingua_mex == "ita") include("./includes/locale.php");
else {
if ($locale_mex and is_file("./includes/lang/$lingua_mex/locales/$locale_mex/locale.php")) include("./includes/lang/$lingua_mex/locales/$locale_mex/locale.php");
else include("./includes/lang/$lingua_mex/locale.php");
} # fine else if ($lingua_mex == "ita")
} # fine if (!isset($locale))
if (!empty($locale['language_capitalization'])) {
if (function_exists('mb_convert_case')) $d_nome_lingua = mb_convert_case($d_nome_lingua,MB_CASE_TITLE,'UTF-8');
else $d_nome_lingua = ucwords($d_nome_lingua);
} # fine if (!empty($locale['language_capitalization']))
} # fine elseif (preg_replace("/[a-z]{2,3}/","",$dlingua_cli) == "")
if (empty($d_nome_lingua)) $dlingua_cli = "";
} # fine if ($dlingua_cli)

$output = "";

$O = "o";
#$O2 = "o";
if ($dsesso == "f") $O = "a";
#if ($dsesso2 == "f") $O2 = "a";
if ($mostra_num == "SI") {
$output .= "$didclienti. <em>$dcognome</em> ";
if ($dcognome2) $output .= "<em>$dcognome2</em> ";
if ($dnome) $output .= "<em>$dnome</em> ";
} # fine if ($mostra_num == "SI")
else {
if ($dtitolo_cli) $output .= "$dtitolo_cli ";
$output .= "<b>$dcognome</b>";
if ($dcognome2) $output .= "<b> $dcognome2</b>";
if ($dnome) $output .= " $dnome";
if ($dsoprannome) $output .= " ($dsoprannome)";
} # fine else if ($mostra_num == "SI")
if ($ddatanascita or $dcittanascita) $output .= " ".mex("nat$O",$pag);
if ($ddatanascita) $output .= " ".mex("il",$pag)." $ddatanascita_f";
if ($dcittanascita) $output .= mex(" a",$pag)." $dcittanascita";
if ($dregionenascita or $dnazionenascita) {
$output .= " ($dregionenascita";
if ($dregionenascita and $dnazionenascita) $output .= ", ";
$output .= "$dnazionenascita)";
} # fine if ($dregionenascita or $dnazionenascita)

$lin = "";
if ($dnazionalita) $lin .= "$dnazionalita";
if ($dnazionalita and !empty($d_nome_lingua)) $lin .= " ";
if (!empty($d_nome_lingua)) $lin .= "(".mex("ln.",$pag)." <em>$d_nome_lingua</em>)";
if ($lin and $ddocumento) {
if (!$dnazionedoc or $dnazionedoc == $dnazionalita) $lin .= " - ";
else {
$output .= "<br>$lin";
$lin = "";
} # fine else if (!$dnazionedoc or...
} # fine if ($lin and $ddocumento)
if ($ddocumento) {
if ($dtipodoc) $lin .= "$dtipodoc ";
$lin .= "$ddocumento";
if ($dscadenzadoc) {
if ($dcittadoc or ($dnazionedoc and $dnazionedoc != $dnazionalita)) {
$lin .= " ($dcittadoc";
if ($dcittadoc and $dnazionedoc and $dnazionedoc != $dnazionalita) $lin .= ", ";
if ($dnazionedoc and $dnazionedoc != $dnazionalita) $lin .= "$dnazionedoc";
$lin .= ")";
} # fine if ($dcittadoc or...
if (date("Ymd",(time() + (C_DIFF_ORE * 3600))) <= str_replace("-","",$dscadenzadoc)) $lin .= " ".mex("scade",$pag)." $dscadenzadoc_f";
else $lin .= " ".mex("scade",$pag)." <font color=\"red\">$dscadenzadoc_f</font>";
} # fine if ($dscadenzadoc)
} # fine if ($ddocumento)
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dcitta) {
$lin .= "$dcitta";
if ($dvia or $dnumcivico or $dcap) $lin .= ",";
$lin .= " ";
} # fine if ($dcitta)
if ($dvia) $lin .= "$dvia ";
if ($dnumcivico) $lin .= "nº $dnumcivico ";
if ($dcap) $lin .= mex("CAP",$pag)." $dcap ";
if ($dnazione or $dregione) $lin .= "(";
if ($dregione) $lin .= $dregione;
if ($dnazione and $dregione) $lin .= ", ";
if ($dnazione) $lin .= $dnazione;
if ($dnazione or $dregione) $lin .= ") ";
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dtelefono) $lin .= mex("Telefono",$pag).": $dtelefono, ";
if ($dtelefono2 or $dtelefono3 or $dfax) {
if ($dtelefono2) $lin .= mex("2º telefono",$pag).": $dtelefono2, ";
if ($dtelefono3) $lin .= mex("3º telefono",$pag).": $dtelefono3, ";
if ($dfax) $lin .= "fax: $dfax, ";
if ($lin) $output .= "<br>".substr($lin,0,-2);
$lin = "";
} # fine if ($dtelefono2 or $dtelefono3 or $dfax)

if ($demail) $lin .= mex("email",$pag).": <a href=\"mailto:$demail\">$demail</a> ";
if ($demail2) $lin .= mex("2ª email",$pag).": <a href=\"mailto:$demail2\">$demail2</a> ";
if ($demail_cert) {
if (strstr($demail_cert,"@")) $lin .= mex("PEC/codice",$pag).": <a href=\"mailto:$demail_cert\">$demail_cert</a> ";
else $lin .= mex("PEC/codice",$pag).": $demail_cert ";
} # fine if ($demail_cert)
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dcod_fiscale) $lin .= mex("Codice fiscale",$pag).": $dcod_fiscale ";
if ($dcod_fiscale and $dpartita_iva) $lin .= ", ";
if ($dpartita_iva) $lin .= mex("Partita iva",$pag).": $dpartita_iva ";
if ($lin) $output .= "<br>$lin";

if ($dcommento) $output = "<div style=\"float: left; padding: 0 60px 0 0;\">$output</div>
<div style=\"float: left; max-width: 400px;\"><br>".mex("Commento",$pag).": $dcommento</div><div style=\"clear: both;\"></div>";

if (!$silenzio) echo $output;
else return $output;

} # fine function mostra_dati_cliente



function mostra_funzjs_cpval () {
echo "
<script type=\"text/javascript\">
<!--

function cp_val (id,idcp,def) {
var elem = document.getElementById(id);
var elemcp = document.getElementById(idcp);
var valcp = elemcp.value;
if (valcp != '') elem.value = valcp;
else elem.value = def;
if (elem.onchange) elem.onchange();
} // fine function cp_val

-->
</script>
";
} # fine function mostra_funzjs_cpval



function mostra_funzjs_dati_rel ($mostra_cod,$pieno,$id_sessione,$anno,$var_extra = "",$pag_relutenti = "") {
if (!$pag_relutenti) $pag_relutenti = "./dati_relutenti.php";
echo "
<script type=\"text/javascript\">
<!--
var campo_agg = '';
function agg_dati_remoti (ifrm,campo_agg) {
if (campo_agg) {
var docfrm = '';
if (document.getElementById(ifrm).contentDocument) {
docfrm = document.getElementById(ifrm).contentDocument; 
}
else docfrm = document.frames[ifrm].document;
var dati_remoti = docfrm.getElementById(ifrm);
var inner_elem = document.getElementById(campo_agg.substr(2));
var funz_oc = inner_elem.onchange;
var val_oc = inner_elem.value;
document.getElementById(campo_agg).innerHTML = dati_remoti.innerHTML;
inner_elem = document.getElementById(campo_agg.substr(2));
if (funz_oc) {
inner_elem.onchange = funz_oc;
inner_elem.onchange();
}
if (val_oc) {
var val_orig = inner_elem.value;
inner_elem.value = val_oc;
if (inner_elem.value != val_oc) inner_elem.value = val_orig;
}
}
}
function ricarica_ifrm (ifrm,campo,sel,rel,rel_sup,id_ut) {
campo_agg = 'b_'+campo;
var sele = document.getElementById(sel);
var sel_ind = sele.selectedIndex;
if (sel_ind) { var val_sel = sele.options[sel_ind].value; }
else { var val_sel = sele.value }
//document.getElementById(ifrm).src = '$pag_relutenti?id_sessione=...
top.frames[ifrm].location.replace('$pag_relutenti?id_sessione=$id_sessione&anno=$anno&id='+val_sel+'&rel='+rel+'&rel_sup='+rel_sup+'&id_ut_sel='+id_ut+'&cmp='+campo+'&mostra_cod=$mostra_cod&pieno=$pieno&d=".date("siHd")."$var_extra');
}
-->
</script>
<iframe name=\"dati_rel\" id=\"dati_rel\" onload=\"agg_dati_remoti('dati_rel',campo_agg);\" style=\"width:0px; height:0px; border: 0px; visibility: hidden;\"></iframe>
";
} # fine function mostra_funzjs_dati_rel



unset($liste_relutente);
function mostra_lista_relutenti ($nome,$sel,$id_utente,$nomelista,$idlista,$idrelutenti,$tablelista,$tablerelutenti,$size="",$javascript="",$campo_opzionale="",$rel_inf_sing="",$id_rel_inf="",$rel_sup_sing="",$id_sup_sel="") {
if (!$id_sup_sel) $id_sup_sel = 0;
global $liste_relutente;
if (empty($liste_relutente[$id_sup_sel][$nomelista])) {
if (!$rel_sup_sing and !$id_sup_sel) $lista_utente = esegui_query("select distinct $tablelista.$nomelista from $tablerelutenti inner join $tablelista on $tablerelutenti.$idrelutenti = $tablelista.$idlista where $tablerelutenti.idutente = '$id_utente' order by $tablelista.$nomelista");
else {
if ($id_sup_sel) $is_id = "= '$id_sup_sel'";
else $is_id = "is NULL";
$lista_utente = esegui_query("select distinct $tablelista.$nomelista from $tablerelutenti inner join $tablelista on $tablerelutenti.$idrelutenti = $tablelista.$idlista where $tablerelutenti.idutente = '$id_utente' and $tablerelutenti.idsup $is_id order by $tablelista.$nomelista");
} # fine else if (!$rel_sup_sing and !$id_sup_sel)
$num_lista_utente = numlin_query($lista_utente);
if (!$num_lista_utente) {
if (!$campo_opzionale) {
if ($size) $size = " size=\"$size\"";
else $size = "";
$liste_relutente[$id_sup_sel][$nomelista] = "<input type=\"text\" name=\"\"$size>";
} # fine if (!$campo_opzionale)
} # fine if (!$num_lista_utente)
else {
$liste_relutente[$id_sup_sel][$nomelista] = "<select name=\"\">\n<option value=\"\">------</option>";
for ($num1 = 0 ; $num1 < $num_lista_utente ; $num1++) {
$opzione = risul_query($lista_utente,$num1,$nomelista,$tablelista);
$liste_relutente[$id_sup_sel][$nomelista] .= "<option value=\"$opzione\">$opzione</option>";
} # fine for $num1
$liste_relutente[$id_sup_sel][$nomelista] .= "</select>";
} # fine else if (!$num_lista_utente)
} # fine if (empty($liste_relutente[$id_sup_sel][$nomelista]))
$lista_return = fixstr($liste_relutente[$id_sup_sel][$nomelista]);
if ($sel) {
if (substr($lista_return,0,7) == "<select") $lista_return = str_replace("<option value=\"$sel\">","<option value=\"$sel\" selected>",$lista_return);
else $lista_return = str_replace(" name=\"\""," name=\"\" value=\"$sel\"",$lista_return);
} # fine if ($sel)
if ($rel_inf_sing) {
$inf_esist = esegui_query("select idsup from $tablerelutenti where idutente = '$id_utente' and id$rel_inf_sing is not NULL and idsup is not NULL limit 1 ");
if (!numlin_query($inf_esist)) $rel_inf_sing = "";
} # fine if ($rel_inf_sing)
if ($javascript) {
$lista_return = str_replace("\\","\\\\'",$lista_return);
$lista_return = str_replace("\n","\\\n",$lista_return);
$lista_return = str_replace("'","\'",$lista_return);
$lista_return = str_replace("</","<\/",$lista_return);
if ($rel_inf_sing) $lista_return = str_replace(" name=\"\""," name=\"\" onchange=\"ricarica_ifrm(\'dati_rel\',\'$id_rel_inf\',\'$nome\',\'$rel_inf_sing\',\'".substr($idrelutenti,2)."\',\'$id_utente\')\"",$lista_return);
if ($rel_sup_sing) $lista_return = "<b id=\"b_$nome\" style=\"font-weight: normal;\">".$lista_return."<\/b>";
} # fine if ($javascript)
else {
if ($rel_inf_sing) $lista_return = str_replace(" name=\"\""," name=\"\" onchange=\"ricarica_ifrm('dati_rel','$id_rel_inf','$nome','$rel_inf_sing','".substr($idrelutenti,2)."','$id_utente')\"",$lista_return);
if ($rel_sup_sing) $lista_return = "<b id=\"b_$nome\" style=\"font-weight: normal;\">".$lista_return."</b>";
} # fine else if ($javascript)
return str_replace(" name=\"\""," name=\"$nome\" id=\"$nome\"",$lista_return);
} # fine function mostra_lista_relutenti


?>
