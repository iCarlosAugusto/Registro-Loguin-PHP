<?php

abstract class VerifyEmptyInput{


        public function verifyUserName($userName){
            
            //print_r($GLOBALS['erros']);
            
            if($userName == ""){
                $GLOBALS['erros']['userName'] = "É necessário um nome de usuário  </br>";
            }
        } 

        public function verifyEmail($email){
            if(trim($email) !== ""){
        
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $GLOBALS['erros']['email'] = "Email inválido. </br>";
               }
            }else{
                    $GLOBALS['erros']['email'] = "Email vazio, o campo é obrigatório. </br>";
            }
        }

        public function verifyPassword($password, $passwordConfirm){
    
        if(trim($password) !== ""){
            if(strlen($password) < 6)
                $GLOBALS['erros']['password'] = "Senha curta, pelo menos 6 caratheres. </br>";
    
             if(trim($passwordConfirm !== "")){
                 if($password !== $passwordConfirm){
                    $GLOBALS['erros']['password'] = "As senhas não batem, tente novamente  </br>.";
                }
            }else{
                    $GLOBALS['erros']['password'] = "Você precisa confirmar a sua senha  </br>";
            }
        }else{
                $GLOBALS['erros']['password'] = "Senha está vazia, o campo é obrigatório. </br>";
            }
        }
    }
