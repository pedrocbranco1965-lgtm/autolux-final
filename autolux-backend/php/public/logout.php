<?php
/**
 * Termina a sessão do funcionário. Aceita apenas POST (com token CSRF) para
 * que um simples link malicioso não consiga fazer logout ao utilizador.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

Auth::sair();
session_start();
flash('sucesso', 'Sessão terminada. Até breve!');
redirecionar('login.php');
