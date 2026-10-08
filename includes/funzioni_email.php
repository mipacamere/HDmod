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



# Queste chiamate devono essere in global e non possono stare dentro a un "if" o altro
#use PHPMailer\PHPMailer\PHPMailer;
#use PHPMailer\PHPMailer\SMTP;

global $dati_manda_email;
$dati_manda_email = array();



function controlla_spf ($dom_emal,$passa_spf=false) {

$maschera = "SI";
#if (strstr($dom_emal,"@")) $dom_emal = substr(strstr($dom_emal,"@"),1);
if (!function_exists('dns_get_record') or !defined('C_MASCHERA_EMAIL')) $maschera = "NO";
else {
$spf = strtolower(trim(substr(C_MASCHERA_EMAIL,4)));
if (!$spf) $maschera = "NO";
else {
$spf_dom = "";
$dns_txt = @dns_get_record($dom_emal,DNS_TXT);
if (!is_array($dns_txt)) $maschera = "NO";
else {
$n_dns_txt = count($dns_txt);
for ($num1 = 0 ; $num1 < $n_dns_txt ; $num1++) {
if (isset($dns_txt[$num1]['txt']) and strtolower(substr(trim($dns_txt[$num1]['txt']),0,6)) == "v=spf1") {
$spf_dom = " ".strtolower($dns_txt[$num1]['txt'])." ";
break;
} # fine if (isset($dns_txt[$num1]['txt'] and strtolower(substr(trim($dns_txt[$num1]['txt']),0,6)) == "v=spf1")
} # fine for $num1
if (!$spf_dom) $maschera = "NO";
else {
$spf = explode(" ",$spf);
$n_spf = count($spf);
for ($num1 = 0 ; $num1 < $n_spf ; $num1++) {
if (substr($spf[$num1],0,1) == "+" and strlen($spf[$num1]) > 1) $spf2 = substr($spf[$num1],1);
else $spf2 = "+".$spf[$num1];
if (str_replace(" ".$spf[$num1]." ","",$spf_dom) == $spf_dom and str_replace(" $spf2 ","",$spf_dom) == $spf_dom) {
$maschera = "NO";
break;
} # fine if (str_replace(" ".$spf[$num1]." ","",$spf_dom) == $spf_dom and...
} # fine for $num1
if ($passa_spf) $maschera .= $spf_dom;
} # fine else if (!$spf_dom)
} # fine else if (!is_array($dns_txt))
} # fine else if (!$spf)
} # fine else if (!function_exists('dns_get_record') or...

return $maschera;

} # fine function controlla_spf



function manda_email ($mittente_email,$reply_to,$nome_mittente,$destinatario_email,$nome_destinatario,$bcc_mittente,$bcc_indirizzo,$oggetto_email,$testo_email,$cont_type,$dati_allegato,$allega,$tablepersonalizza,$tablecache="") {
global $dati_manda_email;

$inviato = array('ok' => 0);
if (strstr($mittente_email,"<")) {
$mittente_email = explode("<",$mittente_email);
$mittente_email = explode(">",$mittente_email[1]);
$mittente_email = $mittente_email[0];
} # fine if (strstr($mittente_email,"<"))
if (!$dati_allegato) $dati_allegato = array('num' => 0, 'num_iim' => 0);

if (empty($dati_manda_email['modo'])) {
if ($tablepersonalizza) {
$dati_manda_email['modo'] = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'modo_invio_email' and idutente = '1'");
$dati_manda_email['modo'] = risul_query($dati_manda_email['modo'],0,'valpersonalizza');
} # fine if ($tablepersonalizza)
else $dati_manda_email['modo'] = "locale";
} # fine if (empty($dati_manda_email['modo']))


if ($dati_manda_email['modo'] == "remoto") {

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
if (defined("C_PERCORSO_PHPMAILER") and C_PERCORSO_PHPMAILER != "") $dati_manda_email['percorso_phpmailer'] = C_PERCORSO_PHPMAILER;
elseif (empty($dati_manda_email['percorso_phpmailer'])) {
$dati_manda_email['percorso_phpmailer'] = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'percorso_phpmailer' and idutente = '1'");
if (numlin_query($dati_manda_email['percorso_phpmailer'])) $dati_manda_email['percorso_phpmailer'] = risul_query($dati_manda_email['percorso_phpmailer'],0,'valpersonalizza');
else $dati_manda_email['percorso_phpmailer'] = "";
} # fine elseif (empty($dati_manda_email['percorso_phpmailer']))
if ($dati_manda_email['percorso_phpmailer'] and is_file($dati_manda_email['percorso_phpmailer'])) include($dati_manda_email['percorso_phpmailer']);
} # fine if (!class_exists('PHPMailer\PHPMailer\PHPMailer'))

if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) $dati_manda_email['modo'] = "locale";
else {
if (empty($dati_manda_email['server'])) {
if (!$tablepersonalizza) $dati_manda_email['modo'] = "locale";
else {
$server_smtp = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'server_smtp' and idutente = '1' ");
if (!numlin_query($server_smtp)) $dati_manda_email['modo'] = "locale";
else {
$server_smtp = explode("#@#",(string) risul_query($server_smtp,0,'valpersonalizza'));
$dati_manda_email['server'] = $server_smtp[0];
$dati_manda_email['username'] = $server_smtp[1];
$dati_manda_email['sicurezza'] = $server_smtp[2];
$dati_manda_email['porta'] = $server_smtp[3];
$dati_manda_email['password'] = $server_smtp[4];
} # fine else if (!numlin_query($server_smtp_esist))
} # fine else if (!$tablepersonalizza)
} # fine if (empty($dati_manda_email['server']))
} # fine else if (!class_exists('PHPMailer\PHPMailer\PHPMailer'))

if ($dati_manda_email['modo'] == "remoto") {

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
#SMTP::DEBUG_OFF = off
#SMTP::DEBUG_CLIENT = client messages
#SMTP::DEBUG_SERVER = client and server messages
#$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
$mail->Host = $dati_manda_email['server'];
if ($dati_manda_email['sicurezza'] == "ssl_tls") {
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
if (empty($dati_manda_email['porta'])) $dati_manda_email['porta'] = "465";
} # fine if ($dati_manda_email['sicurezza'] == "ssl_tls")
if ($dati_manda_email['sicurezza'] == "starttls") {
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
if (empty($dati_manda_email['porta'])) $dati_manda_email['porta'] = "587";
} # fine if ($dati_manda_email['sicurezza'] == "starttls")
if (empty($dati_manda_email['porta'])) $dati_manda_email['porta'] = "25";
$mail->Port = $dati_manda_email['porta'];
if (strcmp($dati_manda_email['username'],"")) {
$mail->SMTPAuth = true;
$mail->Username = $dati_manda_email['username'];
if (strcmp($dati_manda_email['password'],"")) $mail->Password = $dati_manda_email['password'];
} # fine if (strcmp($dati_manda_email['username'],""))

$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
if ($reply_to) {
$mail->setFrom($mittente_email);
$mail->addReplyTo($reply_to,$nome_mittente);
} # fine if ($reply_to)
else $mail->setFrom($mittente_email,$nome_mittente);
$mail->addAddress($destinatario_email,$nome_destinatario);
#$mail->addCC('cc@example.com');
if ($bcc_mittente == "SI") $mail->addBCC($mittente_email);
if ($bcc_indirizzo) $mail->addBCC($bcc_indirizzo);
$mail->Subject = $oggetto_email;
# non usare MsgHTML e AltBody se si usa Body
if ($cont_type == "multipart/alternative") $mail->msgHTML($testo_email);
else {
$mail->Body = $testo_email;
if ($cont_type == "text/plain") $mail->IsHTML(false);
else $mail->IsHTML(true);
#$mail->AltBody = $testo_email;
} # fine else if ($cont_type == "multipart/alternative")
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
if ($allega[$num1] == "SI") $mail->AddStringAttachment(base64_decode(str_replace("\r\n","",$dati_allegato[$num1]['valo'])),$dati_allegato[$num1]['nome'],'base64',$dati_allegato[$num1]['tipo']);
} # fine for $num1
for ($num1 = 1 ; $num1 <= $dati_allegato['num_iim'] ; $num1++) $mail->addStringEmbeddedImage(base64_decode(str_replace("\r\n","",$dati_allegato['iim'][$num1]['valo'])),$dati_allegato['iim'][$num1]['cid'],$dati_allegato['iim'][$num1]['nome'],'base64',$dati_allegato['iim'][$num1]['tipo']);

if (!$mail->send()) $inviato['err'] = $mail->ErrorInfo;
else $inviato['ok'] = 1;
$mail->SmtpClose();

} # fine if ($dati_manda_email['modo'] == "remoto")
else $inviato['err_remoto'] = 1;
} # fine if ($dati_manda_email['modo'] == "remoto")


if ($dati_manda_email['modo'] == "locale") {

if (defined('C_MASSIMO_NUM_EMAIL_GIORNALIERE') and C_MASSIMO_NUM_EMAIL_GIORNALIERE > 0 and $tablecache) {
$tabelle_lock = array($tablecache);
if ($tablepersonalizza) $altre_tab_lock = array($tablepersonalizza);
else $altre_tab_lock = array();
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$lim_oggi = aggslashdb(date("Y-m-d",(time() + (C_DIFF_ORE * 3600)))." 00:00:00");
esegui_query("delete from $tablecache where tipo = 'mail_inv' and datainserimento < '$lim_oggi' ");
$email_oggi = esegui_query("select numero from $tablecache where tipo = 'mail_inv' ");
if (numlin_query($email_oggi)) $email_oggi = risul_query($email_oggi,0,'numero');
else $email_oggi = 0;
if (isset($dati_manda_email['num_app'])) {
$max_email = floor((float) C_MASSIMO_NUM_EMAIL_GIORNALIERE / 10);
$max_email = C_MASSIMO_NUM_EMAIL_GIORNALIERE + ($max_email * $dati_manda_email['num_app']);
} # fine if (isset($dati_manda_email['num_app']))
else $max_email = C_MASSIMO_NUM_EMAIL_GIORNALIERE;
if ($email_oggi >= $max_email) $inviato['err_max_email'] = $max_email;
} # fine if (defined('C_MASSIMO_NUM_EMAIL_GIORNALIERE') and C_MASSIMO_NUM_EMAIL_GIORNALIERE > 0 and... 
else $tablecache = "";
if (empty($inviato['err_max_email'])) {

if (empty($dati_manda_email['maschera'])) {
$dati_manda_email['maschera'] = "";
if (defined('C_MASCHERA_EMAIL') and C_MASCHERA_EMAIL != "" and substr(C_MASCHERA_EMAIL,0,4) != "spf:") $dati_manda_email['maschera'] = C_MASCHERA_EMAIL;
elseif ($tablepersonalizza) {
$dati_manda_email['maschera'] = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'maschera_email' and idutente = '1'");
$dati_manda_email['maschera'] = risul_query($dati_manda_email['maschera'],0,'valpersonalizza');
} # fine elseif ($tablepersonalizza)
} # fine if (empty($dati_manda_email['maschera']))
$maschera_corr = $dati_manda_email['maschera'];
if ($maschera_corr == "SI" and defined('C_MASCHERA_EMAIL') and substr(C_MASCHERA_EMAIL,0,4) == "spf:") {
$dominio = substr(strstr($mittente_email,"@"),1);
if (isset($dati_manda_email['spf'][$dominio])) $maschera_corr = $dati_manda_email['spf'][$dominio];
else {
$maschera_corr = controlla_spf($dominio);
if ($maschera_corr != "SI") $inviato['err_spf'] = 1;
$dati_manda_email['spf'][$dominio] = $maschera_corr;
} # fine else if (isset($dati_manda_email['spf'][$dominio]))
} # fine if ($maschera_corr == "SI" and defined('C_MASCHERA_EMAIL') and

if (function_exists('version_compare') and version_compare(PHP_VERSION,'8.0.0','>=')) $mailh_nl = "\r\n";
else {
switch (strtoupper(substr(PHP_OS,0,3))) {
case "WIN": $mailh_nl = "\r\n"; break;
case "MAC": $mailh_nl = "\r"; break;
default: $mailh_nl = "\n"; break;
} # fine switch (strtoupper(substr(PHP_OS,0,3)))
} # fine else if (function_exists('version_compare') and version_compare(PHP_VERSION, '8.0.0', '>='))
$mailb_nl = $mailh_nl;
$num_boundary = 1;
if ($nome_mittente and !$reply_to) $headers = "From: \"".str_replace('"','',$nome_mittente)."\" <$mittente_email>$mailh_nl";
else $headers = "From: <$mittente_email>$mailh_nl";
if ($reply_to) {
if ($nome_mittente) $headers .= "Reply-To: \"".str_replace('"','',$nome_mittente)."\" <$reply_to>$mailh_nl";
else $headers .= "Reply-To: <$reply_to>$mailh_nl";
} # fine if ($reply_to)
if ($bcc_mittente == "SI") $headers .= "Bcc: $mittente_email$mailh_nl";
if ($bcc_indirizzo) $headers .= "Bcc: $bcc_indirizzo$mailh_nl";
#$headers .= "X-Sender: $mittente_email$mailh_nl";
#$headers .= "X-Mailer: PHP$mailh_nl";
#$headers .= "X-Priority: 3$mailh_nl";
#$headers .= "Return-Path: $mittente_email$mailh_nl";
if ($cont_type == "multipart/alternative") {
$testo_plain = strip_tags($testo_email,"<br><p>");
$testo_plain = str_replace("<BR>","<br>",$testo_plain);
$testo_plain = str_replace("<br>\r\n",$mailb_nl,$testo_plain);
$testo_plain = str_replace("<br>\r",$mailb_nl,$testo_plain);
$testo_plain = str_replace("<br>\n",$mailb_nl,$testo_plain);
$testo_plain = str_replace("<br>",$mailb_nl,$testo_plain);
$testo_plain = chunk_split(base64_encode($testo_plain));
if (substr($testo_plain,-1) != $mailb_nl and substr($testo_plain,-2) != $mailb_nl) $testo_plain .= $mailb_nl;
} # fine if ($cont_type == "multipart/alternative")
$testo_email = chunk_split(base64_encode($testo_email));
$allegato_presente = 0;
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) if ($allega[$num1] == "SI") $allegato_presente = 1;
if ($allegato_presente or $dati_allegato['num_iim'] or $cont_type == "multipart/alternative") {
$boundary = "b$num_boundary=_".crea_val_casuale(22);
$num_boundary++;
$boundary_iim = "";
$boundary_alt = "";
$cont_type_char_enc = "$cont_type; charset=utf-8$mailb_nl"."Content-Transfer-Encoding: base64";
$headers .= "MIME-Version: 1.0$mailh_nl";
if ($allegato_presente) {
$headers .= "Content-Type: multipart/mixed;$mailh_nl";
if ($cont_type == "multipart/alternative") {
$boundary_alt = "b$num_boundary=_".crea_val_casuale(22);
$num_boundary++;
$cont_type_char_enc = "$cont_type;$mailb_nl boundary=\"$boundary_alt\"";
} # fine if ($cont_type == "multipart/alternative")
if ($dati_allegato['num_iim']) {
$boundary_iim = "b$num_boundary=_".crea_val_casuale(22);
$num_boundary++;
if (!$boundary_alt) $cont_type_char_enc = "multipart/related;$mailb_nl boundary=\"$boundary_iim\"";
} # fine if ($dati_allegato['num_iim'])
} # fine if ($allegato_presente)
else {
if ($cont_type == "multipart/alternative") {
$headers .= "Content-Type: multipart/alternative;$mailh_nl";
$boundary_alt = $boundary;
$cont_type_char_enc = "Content-Type: text/plain; charset=UTF-8$mailb_nl"."Content-Transfer-Encoding: base64;";
if ($dati_allegato['num_iim']) {
$boundary_iim = "b$num_boundary=_".crea_val_casuale(22);
$num_boundary++;
} # fine if ($dati_allegato['num_iim'])
} # fine if ($cont_type == "multipart/alternative")
else {
$headers .= "Content-Type: multipart/related;$mailh_nl";
$boundary_iim = $boundary;
} # fine else if ($cont_type == "multipart/alternative")
} # fine else if ($allegato_presente)
$headers .= " boundary=\"$boundary\"$mailh_nl";
if ($boundary_iim and $boundary_iim != $boundary) {
$testo_email = "--$boundary_iim$mailb_nl"."Content-Type: text/html; charset=UTF-8$mailb_nl"."Content-Transfer-Encoding: base64$mailb_nl$mailb_nl$testo_email";
if (!$allegato_presente or $boundary_alt) $testo_email = "Content-type: multipart/related;$mailb_nl boundary=\"$boundary_iim\"$mailb_nl$mailb_nl$testo_email";
} # fine if ($boundary_iim and $boundary_iim != $boundary)
if ($boundary_alt) {
if (!$boundary_iim) $testo_email = "Content-Type: text/html; charset=UTF-8$mailb_nl"."Content-Transfer-Encoding: base64$mailb_nl$mailb_nl$testo_email";
if ($boundary_alt == $boundary) $testo_email = "$testo_plain$mailb_nl--$boundary_alt$mailb_nl$testo_email";
else $testo_email = "--$boundary_alt$mailb_nl"."Content-Type: text/plain; charset=UTF-8$mailb_nl"."Content-Transfer-Encoding: base64$mailb_nl$mailb_nl$testo_plain$mailb_nl--$boundary_alt$mailb_nl$testo_email";
} # fine if ($boundary_alt)
$testo_email = "This is a multi-part message in MIME format.$mailb_nl--$boundary$mailb_nl"."Content-type: $cont_type_char_enc$mailb_nl$mailb_nl$testo_email";
if ($boundary_iim) {
for ($num1 = 1 ; $num1 <= $dati_allegato['num_iim'] ; $num1++) {
if (substr($testo_email,-1) == $mailb_nl or substr($testo_email,-2) == $mailb_nl) $testo_email .= "$mailb_nl--$boundary_iim$mailb_nl";
else $testo_email .= "$mailb_nl$mailb_nl--$boundary_iim$mailb_nl";
$testo_email .= "Content-Type: ".$dati_allegato['iim'][$num1]['tipo'].";$mailb_nl";
$testo_email .= " name=\"".$dati_allegato['iim'][$num1]['nome']."\"$mailb_nl";
$testo_email .= "Content-Transfer-Encoding: base64$mailb_nl";
$testo_email .= "Content-ID: ".$dati_allegato['iim'][$num1]['cid']."$mailb_nl";
$testo_email .= "Content-Disposition: inline;$mailb_nl";
$testo_email .= " filename=\"".$dati_allegato['iim'][$num1]['nome']."\"$mailb_nl$mailb_nl";
$testo_email .= $dati_allegato['iim'][$num1]['valo'];
} # fine for $num1
if ($boundary_iim != $boundary) {
if (substr($testo_email,-1) == $mailb_nl or substr($testo_email,-2) == $mailb_nl) $testo_email .= "$mailb_nl--$boundary_iim--$mailb_nl";
else $testo_email .= "$mailb_nl$mailb_nl--$boundary_iim--$mailb_nl$mailb_nl";
} # fine if ($boundary_iim != $boundary)
} # fine if ($boundary_iim)
if ($boundary_alt and $boundary_alt != $boundary) {
if (substr($testo_email,-1) == $mailb_nl or substr($testo_email,-2) == $mailb_nl) $testo_email .= "$mailb_nl--$boundary_alt--$mailb_nl";
else $testo_email .= "$mailb_nl$mailb_nl--$boundary_alt--$mailb_nl$mailb_nl";
} # fine if ($boundary_alt and $boundary_alt != $boundary)
for ($num1 = 1 ; $num1 <= $dati_allegato['num'] ; $num1++) {
if ($allega[$num1] == "SI") {
if (substr($testo_email,-1) == $mailb_nl or substr($testo_email,-2) == $mailb_nl) $testo_email .= "$mailb_nl--$boundary$mailb_nl";
else $testo_email .= "$mailb_nl$mailb_nl--$boundary$mailb_nl";
$testo_email .= "Content-Type: ".$dati_allegato[$num1]['tipo'].";$mailb_nl";
$testo_email .= " name=\"".$dati_allegato[$num1]['nome']."\"$mailb_nl";
$testo_email .= "Content-Transfer-Encoding: base64$mailb_nl";
$testo_email .= "Content-Disposition: attachment;$mailb_nl";
$testo_email .= " filename=\"".$dati_allegato[$num1]['nome']."\"$mailb_nl$mailb_nl";
$testo_email .= $dati_allegato[$num1]['valo'];
} # fine if ($allega[$num1] == "SI")
} # fine for $num1
if (substr($testo_email,-1) == $mailb_nl or substr($testo_email,-2) == $mailb_nl) $testo_email .= "$mailb_nl--$boundary--";
else $testo_email .= "$mailb_nl$mailb_nl--$boundary--";
} # fine if ($allegato_presente or $dati_allegato['num_iim'] or...
else {
if ($cont_type != "text/plain") $headers .= "MIME-Version: 1.0$mailh_nl";
$headers .= "Content-type: $cont_type; charset=utf-8$mailh_nl";
$headers .= "Content-Transfer-Encoding: base64$mailh_nl";
} # fine else if ($allegato_presente or $dati_allegato['num_iim'])
if (function_exists('mb_encode_mimeheader')) {
$oggetto_email = mb_encode_mimeheader($oggetto_email,'UTF-8','B',$mailh_nl,strlen('Subject: '));
if ($nome_destinatario) $destinatario_email = "\"".addcslashes(mb_encode_mimeheader($nome_destinatario,"UTF-8","Q"),'"')."\" <$destinatario_email>";
} # fine if (function_exists('mb_encode_mimeheader'))
elseif ($nome_destinatario) $destinatario_email = "\"".str_replace('"','',$nome_destinatario)."\" <$destinatario_email>";

if ($maschera_corr == "SI") $inviato['ok'] = mail($destinatario_email,$oggetto_email,$testo_email,$headers,"-f$mittente_email");
else $inviato['ok'] = mail($destinatario_email,$oggetto_email,$testo_email,$headers);

} # fine if (empty($inviato['err_max_email']))
if ($tablecache) {
if ($inviato['ok']) {
if ($email_oggi) esegui_query("update $tablecache set numero = '".($email_oggi + 1)."' where tipo = 'mail_inv' ");
else esegui_query("insert into $tablecache (numero,tipo,datainserimento) values ('1','mail_inv','$lim_oggi') ");
} # fine if ($inviato['ok'])
unlock_tabelle($tabelle_lock);
} # fine if ($tablecache)

} # fine if ($dati_manda_email['modo'] == "locale")


return $inviato;

} # fine function manda_email



?>