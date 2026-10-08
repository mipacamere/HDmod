<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2021 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "dati_relutenti.php";
$titolo = "dati_rel";

$var_pag = array();
$var_pag[0] = 'id';
$var_pag[1] = 'rel';
$var_pag[2] = 'rel_sup';
$var_pag[3] = 'id_ut_sel';
$var_pag[4] = 'cmp';
$var_pag[5] = 'mostra_cod';
$var_pag[6] = 'pieno';
$var_pag[7] = 'd';
$n_var_pag = 8;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_dati_relutenti.php");
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
else $modifica_pers = "SI";
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
if (substr($priv_ins_clienti,0,1) == "s") $inserimento_nuovi_clienti = "SI";
else $inserimento_nuovi_clienti = "NO";
$modifica_clienti = "NO";
if (substr($priv_ins_clienti,1,1) == "s") $modifica_clienti = "SI";
if (substr($priv_ins_clienti,1,1) == "p") $modifica_clienti = "PROPRI";
if (substr($priv_ins_clienti,1,1) == "g") $modifica_clienti = "GRUPPI";
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_pers = "SI";
$inserimento_nuovi_clienti = "SI";
$modifica_clienti = "SI";
} # fine else if ($id_utente != 1)


if ($anno_utente_attivato == "SI" and ($modifica_pers != "NO" or $inserimento_nuovi_clienti != "NO" or $modifica_clienti != "NO")) {



if ($id_utente != 1) $id_ut_sel = $id_utente;
else $id_ut_sel = aggslashdb(fixset($id_ut_sel));

if (!isset($id)) $id = "";
if (!isset($rel)) $rel = "";
if (!isset($rel_sup)) $rel_sup = "";
if (!isset($cmp)) $cmp = "";
if (!isset($mostra_cod)) $mostra_cod = "";
if (!isset($pieno)) $pieno = "";



mostra_frame_rel($id,$rel,$rel_sup,$id_ut_sel,$cmp,$mostra_cod,$pieno,$titolo);



} # fine if ($anno_utente_attivato == "SI" and...
} # fine if ($id_utente)



?>
