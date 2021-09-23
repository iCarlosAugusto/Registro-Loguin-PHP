<?php
require_once "Conexao.php";

$conexao = novaConexao(null);

$sql = "CREATE DATABASE IF NOT EXISTS RegistroUsuarios";

$resultado = $conexao->query($sql);

if($resultado){
    echo "Sucesso :)";
}else{
    echo "Error: ". $conexao->error;
}
$conexao->close();