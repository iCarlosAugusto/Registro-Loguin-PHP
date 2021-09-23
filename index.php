<?php

require_once "./DB/Conexao.php";
session_start();

if(count($_POST) > 0){
    $l = new Loguin($_POST['email'], $_POST['password']);
}

class Loguin{
    private $email;
    private $password;
    
    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $this->password = $password;
    }

    public function __construct($email, $password){
        $GLOBALS['erros'] = array("email"=> "", "password" => "", "wrongUser" => "");
        $this->setEmail($email);
        $this->setPassword($password);
        $this->verifyEmptyFields();
    }

    private function verifyEmptyFields(){
        if(trim($this->getEmail()) == ""){
            $GLOBALS['erros']['email'] = "Campo email vazio está vazio. </br>";
            
        }else{
            if(!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)){
                $GLOBALS['erros']['email'] = "Email inválido. </br>";
            }
        }

        if(trim($this->getPassword()) == ""){
            $GLOBALS['erros']['password'] = "Campo senha vazio está vazio. </br>";
        }

        empty($GLOBALS['erros']['password']) && empty($GLOBALS['erros']['email']) ? $this->verifyIntoDB() : null;
    }

    private function verifyIntoDB(){
        require_once "./DB/Conexao.php";
        $conexao = novaConexao();

        $parametros = [
            $this->getEmail(),
            $this->getPassword()
        ];

        $sql = "SELECT email, senha from usuarios WHERE email = ? and senha = ? ";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", ...$parametros);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){            
            echo $parametros[0];
            $sql = "SELECT userName from usuarios WHERE email = '$parametros[0]' ";
            $result = $conexao->query($sql);
            $name = $result->fetch_assoc();
            $_SESSION['user'] = $name['userName'];
            $_SESSION['user'] == "Administrador" ? header('Location: ./admPage/') : header('Location: ./Home/');
        }else{
            $GLOBALS['erros']['email'] = "Senha ou email estão incorretos.";
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="erros">
        <?php
            if(isset($GLOBALS['erros'])){
                foreach($GLOBALS['erros'] as $erro){
                    echo $erro;
                }
            }
        ?>
    </div>


    <div class="conteiner">
        <header> Loguin PHP</header>

        <form action="#" method="POST">
            <input type="email" name="email" id="email" placeholder="Email">
            <input type="password" name="password" id="password" placeholder="Senha">
            <button>Fazer Loguin</button>
        </form>
        <a href="./Registrar/">Não tem conta? Faça o registro!</a>
    </div>

    <div class="obs">
        <p>Entre com o email: adm@adm.com e a senha: adm para poder excluir e editar usuários.</p>
    </div>

</body>
</html>