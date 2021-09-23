<?php

session_start();

    $edit = new Editar;

    if(count($_POST) > 0){
        $edit->verifyUserIntoDB();
    }

    if(count($_GET) > 0){
        $edit->getUser();
    }

    class Editar{
    
        private $email;
        private $userName;

        public function getEmail(){
            return $this->email;
        }

        public function setEmail($email){
            $this->email = $email;
        }

        public function getUserName(){
            return $this->userName;
        }

        public function setUserName($userName){
            $this->userName = $userName;
        }

        public function __construct(){
            $GLOBALS['feedback'] = array("noChange" => "", "changed" => "", "erroEmail" => "", "erroUserName" => "");

            if(!isset($_SESSION['user'])){
                header("Location: ../Logout/");
            }
        }

        public function verifyUserIntoDB(){
            require_once "../../DB/Conexao.php";

            $email = $_POST['email'];
            $userName = $_POST['userName'];

            $sql = "SELECT * FROM usuarios WHERE userName = '$userName' ";
            $conexao = novaConexao();
            $resultUserName = $conexao->query($sql);

            if($resultUserName->num_rows > 0){
                $GLOBALS['feedback']['erroUserName'] = "Usuário ja existente. </br>";
            }

            $sql = "SELECT * FROM usuarios WHERE email = '$email' ";
            $conexao = novaConexao();
            $resultEmail = $conexao->query($sql);

            if($resultEmail->num_rows > 0){
                $GLOBALS['feedback']['erroEmail'] = "Email ja existente. </br>";
            }

            if(empty($GLOBALS['feedback']['erroUserName']) && empty($GLOBALS['feedback']['erroEmail'])){
                $this->editUser();
            }
        }

        public function editUser(){
            require_once "../../DB/Conexao.php";

            $id = $_GET['editar'];

            $dataArray = [
                $_POST['userName'],
                $_POST['email'],
                $_POST['password'],
                $id
            ];
        
            $conexao = novaConexao();

            $sql = "UPDATE usuarios SET userName = ?, email = ?, senha = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssss", ...$dataArray);
            $stmt->execute(); 

            if($stmt->affected_rows > 0){
                $GLOBALS['feedback']['changed'] = "Editado com sucesso :)";
            }else{
                $GLOBALS['feedback']['noChange'] = "Você não fez alterações";
            }
        }


        public function getUser(){
            require_once "../../DB/Conexao.php";

            $conexao = novaConexao();
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("s", $_GET['editar']);

            if($stmt->execute()){
                $result = $stmt->get_result();
                $GLOBALS['dados'] = $result->fetch_array();
                if($GLOBALS['dados']['id'] == ""){
                    header("Location: ./admPage.php");
               
                }
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
    <link rel="stylesheet" href="styleAdmPage-Editar.css">
</head>
<body>

    <div class="feedback">
        <?php
            if(isset($feedback)){
                foreach($feedback as $msg){
                    echo $msg;
                }
            }
        ?>
    </div>

    <div class="conteiner">
    <h1>Editar usuário </h1>

        <form action="#" method="POST">
            <input type="hidden" name="id" value="<?php isset($_GET['editar']) ? $_GET['editar'] : null ?>">
            <input type="text" placeholder="Nome" name="userName" value="<?= isset($dados['userName']) ? $dados['userName'] : null ?>" >
            <input type="text" placeholder="email" name="email" value="<?= isset($dados['email']) ? $dados['email'] : null ?>" >
            <input type="password" placeholder="Senha" name="password" value="<?= isset($dados['senha']) ? $dados['senha'] : null ?>">

            <button> Atualizar dados</button>
        </form>
    </div>

    <a href="../../admPage/"> Adm Page - Inicial </a>
</body>
</html>