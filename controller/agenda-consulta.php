<?php

    // solicitando conexão no bd através do arquivo conexaoMysql
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Tratando campos vazios
    $medico = isset($_POST["nome_medico"]) ? $_POST["nome_medico"] : "";
    $data = isset($_POST["data_consulta"]) ? $_POST["data_consulta"] : "";
    $horario = isset($_POST["horario_disp"]) ? $_POST["horario_disp"] : "";
    $nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
    $sexo = isset($_POST["sexo"]) ? $_POST["sexo"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";

    //Mudando o formato da hora recebida
    $horario = $horario . ":00:00";

    // Código para consulta medico
    $sqlMedico = <<<SQL
        SELECT medico.id FROM medico 
        INNER JOIN funcionario ON funcionario.id = id_funcionario 
        INNER JOIN pessoa ON pessoa.id = id_pessoa 
        AND nome = ?
    SQL;

    // Código inserir consulta
    $sqlAgenda = <<<SQL
        INSERT INTO agenda (data_agendamento, horario, nome, sexo, email, id_medico)
        VALUES (?, ?, ?, ?, ?, ?)
    SQL;

    //Realizar consulta e inserção no BD
    try{
        $stmt = $pdo->prepare($sqlMedico);
        $stmt->execute([$medico]);

        $idMedico = $stmt->fetch();

        $stmt2 = $pdo->prepare($sqlAgenda);
        $stmt2->execute([$data, $horario, $nome, $sexo, $email, $idMedico['id']]);

        // Direciona para home após inserção
        header("location: ../index.html");
        exit();

    } catch (Exception $e){

        if ($e->errorInfo[1] === 1062)
          exit('Dados duplicados: ' . $e->getMessage());
        else
          exit('Falha ao cadastrar os dados: ' . $e->getMessage());
    }

?>