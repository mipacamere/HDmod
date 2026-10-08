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



$pag = "inizio.php";
$titolo = "HotelDruid: Crea Anno";

$var_pag = array();
$var_pag[0] = 'nuovo_mess';
$var_pag[1] = 'creaanno';
$var_pag[2] = 'tipo_periodi';
$var_pag[3] = 'giorno_ini_fine';
$var_pag[4] = 'mese_ini';
$var_pag[5] = 'mese_fine';
$var_pag[6] = 'importa_anno_prec';
$n_var_pag = 7;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/funzioni_costi_agg.php");

$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente and $id_utente == 1 and (!defined('C_CREA_ANNO_NON_ATTUALE') or C_CREA_ANNO_NON_ATTUALE != "NO" or $anno == $anno_corrente)) {


$show_bar = "NO";
$titolo = "HotelDruid: ".mex("Crea Anno",$pag);
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente])) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$anno_attuale = date("Y",(time() + (C_DIFF_ORE * 3600) - (C_GIORNI_NUOVO_ANNO * 86400)));
if (@is_file(C_DATI_PATH."/selectperiodi$anno_attuale.1.php")) $anno_attuale_esist = 1;
else $anno_attuale_esist = 0;
if ((!$anno_attuale_esist and $anno == $anno_attuale) or $anno < $anno_attuale or (defined('C_CREA_ANNO_MANUALMENTE') and (C_CREA_ANNO_MANUALMENTE == "SI" or (C_CREA_ANNO_MANUALMENTE == "NUOVO" and $anno == $anno_corrente)))) {

include("./includes/funzioni_anno.php");
crea_nuovo_anno($anno,$PHPR_TAB_PRE,$DATETIME,fixset($tipo_periodi),fixset($giorno_ini_fine),fixset($mese_ini),fixset($mese_fine),fixset($importa_anno_prec),"",$pag);

} # fine if ((!$anno_attuale_esist and $anno == $anno_attuale) or $anno < $anno_attuale or...


echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"nuovo_mess\" value=\"".htmlspecialchars(fixstr($nuovo_mess))."\">
<input class=\"sbutton\" type=\"submit\" value=\"OK\"><br>
</div></form>";



if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente])) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and $id_utente == 1 and (!defined('C_CREA_ANNO_NON_ATTUALE') or C_CREA_ANNO_NON_ATTUALE != "NO" or $anno == $anno_attuale))



?>
