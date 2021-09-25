<?php

require_once "../../Registrar/VerifyEmptyInput.php";
session_start();


    class Editar extends VerifyEmptyInput{
    
        private $password;
        private $passwordConfirm;
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

        public function getPassword(){
            return $this->password;
        }

        public function setPassword($password){
            $this->password = $password;
        }


        public function getPasswordConfirm(){
            return $this->passwordConfirm;
        }

        public function setPasswordConfirm($passwordConfirm){
            $this->passwordConfirm = $passwordConfirm;
        }

        public function __construct(){
            $GLOBALS['feedback'] = array("noChange-Email" => "","noChange-UsernName" => "", "changed" => "", 
            "erroEmail" => "", "erroUserName" => "");

            $GLOBALS['erros'] = array("email" => "", "password" => "", "userName" => ""); 
            

            if(!isset($_SESSION['user'])){
                header("Location: ../Logout/");
            }
        }

        //verificar através da classe abstrata, se há input's vazios
        public function verifyInput($userName, $email, $password, $passwordConfirm){

            $this->verifyUserName($userName);
            $this->verifyEmail($email);
            $this->verifyPassword($password, $passwordConfirm);

            if(empty($GLOBALS['erros']['password']) && empty($GLOBALS['erros']['email']) && empty($GLOBALS['erros']['userName'])){
                $this->setUserName($_POST['userName']);
                $this->setEmail($_POST['email']);
                $this->setPassword($_POST['password']);
                $this->setPasswordConfirm($_POST['passwordConfirm']);

                $this->verifyUserIntoDB();
            }
        }


        public function verifyUserIntoDB(){
            require_once "../../DB/Conexao.php";

            $email = $_POST['email'];
            $userName = $_POST['userName'];

            //Verificar se o nome de usuário já existe ou se não mudou.
            $sql = "SELECT * FROM usuarios WHERE userName = '$userName' ";
            $conexao = novaConexao();
            $resultUserName = $conexao->query($sql);
            $resultArrayUserName = $resultUserName->fetch_array();
            //print_r($resultArrayUserName);
            
            if(isset($_GET['userName']) && isset($resultArrayUserName)){
                if($_GET['userName'] == $resultArrayUserName['userName']){
                    $GLOBALS['feedback']['noChange'] = "Obs: Usuário não foi editado. </br>";
                }else{
                    $resultUserName->num_rows > 0 ? $GLOBALS['feedback']['erroUserName'] = 'Nome de usuário já existe. </br>' : null;
                }
            }

        
            //Verificar se o emailjá existe ou se não mudou.
            $sql = "SELECT * FROM usuarios WHERE email = '$email' ";
            $conexao = novaConexao();
            $resultEmail = $conexao->query($sql);
            $resultArrayEmail = $resultEmail->fetch_array();
            
            if(isset($_GET['email']) && isset($resultArrayEmail)){
                if($_GET['email'] == $resultArrayEmail[2]){
                    $GLOBALS['feedback']['noChange-Email'] = "Obs: Email não foi editado. </br>";
                }else{
                    $resultEmail->num_rows > 0 ? $GLOBALS['feedback']['erroEmail'] = 'Email já está em uso por outro usuário </br>' : null;
                }
            }

            //Caso não haja erros, fazer o Update
            if(empty($GLOBALS['feedback']['erroUserName']) && empty($GLOBALS['feedback']['erroEmail'])){
                $this->editUser();
            }
        }

        public function editUser(){
            //Update no BD.
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
            }//else{
              // echo "ALTERÇÕES NAO FORAM FEITAS";
            //}
        }


        public function getUser(){
            //Pegar os dados do usuário e jogar no input.
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
    
    $edit = new Editar;

    if(count($_POST) > 0){
        $edit->verifyInput($_POST['userName'], $_POST['email'], $_POST['password'], $_POST['passwordConfirm']);
    }

    if(count($_GET) > 0){
        $edit->getUser();
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

    <div class="feedback">
        <?php
            if(isset($erros)){
                foreach($erros as $msg){
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
            <input type="password" placeholder="Repita Senha" name="passwordConfirm" value="<?= isset($dados['senha']) ? $dados['senha'] : null ?>">

            <button> Atualizar dados</button>
        </form>
    </div>

    <a href="../../admPage/"> Adm Page - Inicial </a>
</body>
</html>
