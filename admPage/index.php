<?php
session_start();
require_once "../DB/Conexao.php";

$adm = new AdmPage;
$adm->takeUsers();

class AdmPage{

    public function __construct(){

        if(!isset($_SESSION['user'])){
            header("Location: ../Logout/");
        }
        $_SESSION['user'] = $_SESSION['user'];
        $GLOBALS['users'] = $this->takeUsers();
        
        if(isset($_GET['excluir'])){
            $this->exitUser();
        };
    }

    public function takeUsers(){

        $conexao = novaConexao();
        $sql = "SELECT * FROM usuarios WHERE adm = 0";
        $result = $conexao->query($sql);
        
        if($result->num_rows > 0){
            while($rows = $result->fetch_all()){  
                return $users[] = $rows;
            }
        }
    }

    private function exitUser(){
        $conexao = novaConexao();
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $_GET['excluir']);
        $stmt->execute();
        header("Location: ./index.php");
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styleAdmPage.css">
    <title>Document</title>
</head>
<body>
    <header>
        <h1>ADM Page</h1>
        <p> Olá <?= $_SESSION['user']?>, seja bem-vindo :)</p>
        <a href="../Logout/">Logout</a>
    </header>

    <table>
        <thead>
            <th> ID </th>
            <th> Nome </th>
            <th> Email </th>
            <th> Senha </th>
            <th> Excluir </th>
            <th> Editar</th>
        </thead>

        <tbody>

            <?php  if($users){foreach($users as $user){ ?>
                <tr>
                    <td> <?php echo $user[0]; ?></td>
                    <td> <?php echo $user[1]; ?></td>
                    <td> <?php echo $user[2]; ?></td>
                    <td> <?php echo $user[3]; ?></td>
                    <td> <a href="./index.php?excluir=<?=$user[0]?>"> Excluir</a> </td>
                    <td> <a href="./AdmPage-Editar/index.php?editar=<?=$user[0]?>&userName=<?=$user[1]?>&email=<?=$user[2]?>"> Editar</a> </td>
                </tr>

            <?php } 
            }?>
        </tbody>
        </tbody>
    </table>
</body>
</html>
