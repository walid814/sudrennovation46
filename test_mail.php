<?php
$test = mail('walid.c69@outlook.fr', 'Test email', 'Ceci est un test');
echo $test ? 'Email envoyé' : 'Échec envoi';
?>