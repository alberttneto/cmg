<?php

    // Função para verificar se sessão esta ativa
    function validaLogin($pdo){

        // Se variaveis de sessão não tiver sido inicializadas ja retorna false
        if(!isset($_SESSION['login'], $_SESSION['token'])){
            return false;
        }

        // Armazena valor de login
        $email = $_SESSION['login'];

        // Consulta SQL
        $sql = <<<SQL
            SELECT hash_senha FROM pessoa
            INNER JOIN funcionario ON pessoa.id = id_pessoa
            AND email = ?
        SQL;

        try{

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $senhaHash = $stmt->fetchColumn();

            // Se não existir senha para usuario retorna false
            if(!$senhaHash){
                return false;
            }

            // Gera token com a senha recuperada do BD
            $token = hash('sha512', $senhaHash . $_SERVER['HTTP_USER_AGENT']);

            // tokens diferentes retorna false
            if(!hash_equals($token, $_SESSION['token'])){
                return false;
            }

            // Sem erros retorna true
            return true;

        }catch(Exception $e){
            exit('Falha inesperada: ' . $e->getMessage());
        }
    }

    // Função para verificar se usuario esta logado
    function checkLogin($pdo){
        
        // não estando logado retorna para home, caso contratio mantem na area restrita
        if(!validaLogin($pdo)){
            header("location: ../index.html");
            exit();
        }
    }

?>