<?php
    // Conexão com banco de dados
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Declarando variaveis e tratando se o campo for vazio
    $nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
    $sexo = isset($_POST["sexo"]) ? $_POST["sexo"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $telefone = isset($_POST["fone"]) ? $_POST["fone"] : "";
    $cep = isset($_POST["cep"]) ? $_POST["cep"] : "";
    $log = isset($_POST["log"]) ? $_POST["log"] : "";
    $cidade = isset($_POST["cidade"]) ? $_POST["cidade"] : "";
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : "";
    $peso = isset($_POST["peso"]) ? $_POST["peso"] : "";
    $altura = isset($_POST["altura"]) ? $_POST["altura"] : "";
    $tipoSangue = isset($_POST["tiposangue"]) ? $_POST["tiposangue"] : "";

    // Código SQL para inserção
    $sqlTPessoa = <<< SQL
        INSERT INTO pessoa (nome, sexo, email, telefone, cep, logradouro, cidade, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    SQL; 

    $sqlTPaciente = <<< SQL
        INSERT INTO paciente (peso, altura, tipo_sanguineo, id_pessoa)
        VALUES (?, ?, ?, ?)
    SQL;

    // Inserindo nas tabelas por meio de transação
    try{
        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare($sqlTPessoa);
        if (!$stmt1->execute([
            $nome, $sexo, $email, $telefone, $cep, $log, $cidade, $estado
        ])) throw new Exception('Falha na primeira inserção');

        
        $id_pessoa = $pdo->lastInsertId();
        $stmt2 = $pdo->prepare($sqlTPaciente);
        if (!$stmt2->execute([
            $peso, $altura, $tipoSangue, $id_pessoa
        ])) throw new Exception('Falha na segunda inserção');
        
        $pdo->commit();
        
        // Sucesso na transação direciona seção inical
        header("location: ../area-funcionario/index.php");
        exit();

    } catch (Exception $e){
        //Caso ocorra erro desfaça as inserções
        $pdo->rollBack();
        if ($e->errorInfo[1] === 1062)
          exit('Dados duplicados: ' . $e->getMessage());
        else
          exit('Falha ao cadastrar os dados: ' . $e->getMessage());
    }
?>