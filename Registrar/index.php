<?php
use function PHPSTORM_META\type;
require_once "../DB/Conexao.php";

if(count($_POST) > 0){
        $log = new Registrar($_POST['email'], $_POST['password'], $_POST['passwordConfirm'], $_POST['userName']);
    }
    
    class Registrar{

        private $email;
        private $password;
        private $passwordConfirm;
        public $UserName;

        public function setUserName($userName){
            $this->userName = $userName;
        }
        public function getUserName(){
            return $this->userName;
        }

        private function setPasswordConfirm($passwordConfirm){
            $this->passwordConfirm = $passwordConfirm;
        }
        private function getPasswordConfirm(){
            return $this->passwordConfirm;
        }

        private function getPassword(){
            return $this->password;
        }

        private function setPassword($password){
            $this->password = $password;
        }

        public function getEmail(){
            return $this->email;
        }

        public function setEmail($email){
            $this->email = $email;
        }

        public function __construct($email, $password, $passwordConfirm, $userName){
            $GLOBALS['erros'] = array("email" => "", "password" => "", "userName" => ""); 
            $this->setEmail($email);
            $this->setPassword($password);
            $this->setPasswordConfirm($passwordConfirm);
            $this->setUserName($userName);
            $this->verifyEmptyFields($this->getEmail(), $this->getPassword(), $this->getPasswordConfirm(), $this->getUserName());
        }

        private function verifyEmptyFields($email, $senha, $passwordConfirm, $userName){
            $userName == "" ? $GLOBALS['erros']['userName'] = "É necessário um nome de usuário  </br>" : null;

            if(trim($email) !== ""){
               
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $GLOBALS['erros']['email'] = "Email inválido. </br>";
               }
            }else{
                $GLOBALS['erros']['email'] = "Email vazio, o campo é obrigatório. </br>";
            }
            
            if(trim($senha) !== ""){
                if(strlen($senha) < 6)
                 $GLOBALS['erros']['password'] = "Senha curta, pelo menos 6 caratheres. </br>";

                 if(trim($passwordConfirm !== "")){
                     if($senha !== $passwordConfirm){
                    $GLOBALS['erros']['password'] = "As senhas não batem, tente novamente  </br>.";
                    }
                }else{
                    $GLOBALS['erros']['password'] = "Você precisa confirmar a sua senha  </br>";
                }
            }else{
                $GLOBALS['erros']['password'] = "Senha está vazia, o campo é obrigatório. </br>";
            }

                if(empty($GLOBALS['erros']['password']) && empty($GLOBALS['erros']['email']) && empty($GLOBALS['erros']['userName'])){
                    $this->setEmail($email);
                    $this->setPassword($senha);
                    $this->setUserName($userName);
                    $this->verifyIfUserExists();
                }
        }

        private function verifyIfUserExists(){
            $email = $this->getEmail();
            $userName = $this->getUserName();

            echo "$email";
            echo "$userName";

            $conexao = novaConexao();

            $sqlEmail = "SELECT * FROM usuarios WHERE email = '$email' ";
            $resultEmail = $conexao->query($sqlEmail);
            if($resultEmail->num_rows > 0){
                $GLOBALS['erros']['email'] = "O email já está sendo usado por outro usuário. </br>";
            }

            $sqlUser = "SELECT * FROM usuarios WHERE userName = '$userName' ";
            $resultUser = $conexao->query($sqlUser);
            if($resultUser->num_rows > 0){
                $GLOBALS['erros']['userName'] = "Nome de usuário já está sendo usado por outro usuário. </br>";
            }
            
                if(empty($GLOBALS['erros']['userName']) && empty($GLOBALS['erros']['email'])){
                    $this->addUserIntoDB();
                }  
            }

        private function AddUserIntoDB(){
            $conexao = novaConexao();
            $admPermission = 0;

            $parameters = [
                $this->getUserName(),
                $this->getEmail(),
                $this->getPassword(),
                $admPermission
            ];

            $sql = "INSERT INTO usuarios (userName, email, senha, adm) VALUES(?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("sssi", ...$parameters);
            if($stmt->execute()){
                $GLOBALS['$msg'] = "Registrado com sucesso! :)";
                unset($_POST);
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
    <link rel="stylesheet" href="./styleReg.css">
</head>
<body>

    <div class="sucesso">
        <?php 
            if(isset($GLOBALS['$msg'])){
                echo $GLOBALS['$msg'];
            }
        ?>
    </div>

   <div class="erros">
        <?php if(isset($erros)){
            foreach($erros as $erro){
                echo $erro;
            }
        }?>
    </div>

    <div class="conteiner">
        <header> Registro PHP </header>

        <form action="#" method="POST">
            <input type="text" name="userName" id="userName" placeholder="Nome de usuário" value="<?= isset($_POST['userName'])?$_POST['userName'] : null ?>">
            <input type="email" name="email" id="email" placeholder="e-mail" value="<?= isset($_POST['email']) ? $_POST['email'] : null ?>">
            <input type="password" name="password" id="password" placeholder="Senha">
            <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirme sua senha">
            <button>Registrar</button>
        </form>
        <a href="../index.php">Já tem uma conta? Faça o Loguin!</a>
    </div>
</body>
</html>