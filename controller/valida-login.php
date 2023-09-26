<?php

    // Conexão BD
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Inicia sessão
    session_start();

    // Objeto para armazenar pagina destino e situação da validação
    class RequestResponse{
            
        public $success;
        public $destination;

        function __construct($success, $destination){
            $this->success = $success;
            $this->destination = $destination;
        }
    }

    // armazena valores recebidos do form
    if (isset($_POST["email"])){
        $email = $_POST["email"];
    }
    if (isset($_POST["senha"])){
        $senha = $_POST["senha"];
    }

    // consulta SQL
    $sql = <<<SQL
        SELECT nome, hash_senha FROM pessoa
        INNER JOIN funcionario ON pessoa.id = id_pessoa
        AND email = ?
    SQL;

    // Cria objeto.
    $validaLogin = new RequestResponse(False, "");

    try{

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        // Verifica se a senha está correta
        if ($row && password_verify($senha, $row['hash_senha'])){

            // email de login
            $_SESSION['login'] = $email;
            // Criando token para senhahash
            $_SESSION['token'] = hash('sha512', $row['hash_senha'] . $_SERVER['HTTP_USER_AGENT']);
            // nome do funcionario
            $_SESSION['func'] = $row['nome'];

            // Seta valores do objeto
            $validaLogin->success = True;
            $validaLogin->destination =  "area-funcionario/index.php";

        }

    }catch(Exception $e){
        exit('Falha inesperada: ' . $e->getMessage());
    }

    // retorna em formado JSON
    echo json_encode($validaLogin);
?>