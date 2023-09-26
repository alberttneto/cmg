<?php

    // solicitando conexão no bd através do arquivo conexaoMysql
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Tratando campos vazios
    $cep = isset($_POST["cep"]) ? $_POST["cep"] : "";
    $logradouro = isset($_POST["log"]) ? $_POST["log"] : "";
    $cidade = isset($_POST["cidade"]) ? $_POST["cidade"] : "";
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : "";

    // Código para inserção nas tabelas
    $sql = <<<SQL
        INSERT INTO base_endereços_ajax (cep, logradouro, cidade, estado)
        VALUES (?, ?, ?, ?)
    SQL; 


    try{
        
        // Realizando inserção no BD
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cep, $logradouro, $cidade, $estado]);

        // Direciona após sucesso
        header("location: ../index.html");
        exit();

    } catch (Exception $e){

        if ($e->errorInfo[1] === 1062)
          exit('Dados duplicados: ' . $e->getMessage());
        else
          exit('Falha ao cadastrar os dados: ' . $e->getMessage());
    }
?>