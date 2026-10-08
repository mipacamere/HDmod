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


$num2 = "";
if (isset($_POST['cambia_frasi'])) $num2 = $_POST['cambia_frasi'];
elseif (isset($_GET['cambia_frasi'])) $num2 = $_GET['cambia_frasi'];
$num3 = "";
if (isset($_POST['modello_esistente'])) $num3 = $_POST['modello_esistente'];
elseif (isset($_GET['modello_esistente'])) $num3 = $_GET['modello_esistente'];
if ($num2 or $num3 == "SI") {
if (!empty($num1) and strlen($num1) < 4 and preg_match("/[a-z]/",$num1) and @is_file("includes/templates/$num1/phrases.php")) {
$parola_settimana = "";
$parola_settimane = "";
$parola_alla = "";
include("includes/templates/$num1/phrases.php");
for ($num2 = 0 ; $num2 < $num_frasi ; $num2++) $var_pag[$n_var_pag++] = $fr_frase[$num2];
} # fine if (!empty($num1) and strlen($num1) < 4 and...
} # fine if ($num2 or $num3 == "SI")

$var_pag[$n_var_pag++] = 'mostra_date_passate';
$var_pag[$n_var_pag++] = 'stile_tabella_tariffe';
$var_pag[$n_var_pag++] = 'rates_table_template';



?>