<?php

$host = "192.168.10.79";
$banco = "manutencao";
$senha = "1234";
$usuario = "postgres";

$pdo = NEW PDO(
    "pgsql:host=$host;port=5432;dbname=$banco",
    $usuario,
    $senha  
);