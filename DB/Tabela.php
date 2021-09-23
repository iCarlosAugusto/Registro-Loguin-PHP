<?php
require_once "Conexao.php";

$conexao = novaConexao();

$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT ,
    userName VARCHAR (100) NOT NULL,
    email VARCHAR (100) NOT NULL,
    senha VARCHAR (100) NOT NULL,
    adm INT (1) NOT NULL
)";

$resultado = $conexao->query($sql);

if($resultado){
    echo "Sucesso :)";
}else{
    $conexao->error;
}
$conexao->close();