<?php

    // solicitando conexão no bd através do arquivo conexaoMysql
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Tratando campos vazios
    $nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
    $sexo = isset($_POST["sexo"]) ? $_POST["sexo"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $telefone = isset($_POST["fone"]) ? $_POST["fone"] : "";
    $cep = isset($_POST["cep"]) ? $_POST["cep"] : "";
    $logradouro = isset($_POST["log"]) ? $_POST["log"] : "";
    $cidade = isset($_POST["cidade"]) ? $_POST["cidade"] : "";
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : "";
    $dataContrato = isset($_POST["dataContrato"]) ? $_POST["dataContrato"] : "";
    $salario = isset($_POST["salario"]) ? $_POST["salario"] : "";
    $senha = isset($_POST["senha"]) ? $_POST["senha"] : "";
    $crm = isset($_POST["crm"]) ? $_POST["crm"] : "";
    $especialidade = isset($_POST["especialidade"]) ? $_POST["especialidade"] : "";
    $ehMedico = isset($_POST['ehMedico']) ? TRUE : FALSE;

    // Criando senha HASH
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Código para inserção nas tabelas
    $sqlTPessoa = <<<SQL
        INSERT INTO pessoa (nome, sexo, email, telefone, cep, logradouro, cidade, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    SQL; 

    $sqlTFuncionario = <<<SQL
        INSERT INTO funcionario (data_contrato, salario, hash_senha, id_pessoa)
        VALUES (?, ?, ?, ?)
    SQL; 

    $sqlTMedico = <<<SQL
        INSERT INTO medico (especialidade, crm, id_funcionario)
        VALUES (?, ?, ?)
    SQL; 


    try{
        $pdo->beginTransaction();

        // Executar inserção nas tabelas com tratamento de dados
        $stmt1 = $pdo->prepare($sqlTPessoa);
        if (!$stmt1->execute([
            $nome, $sexo, $email, $telefone, $cep, $logradouro, $cidade, $estado
          ])) throw new Exception('Falha na primeira inserção');

        $idPessoa = $pdo->lastInsertId();
        $stmt2 = $pdo->prepare($sqlTFuncionario);
        if (!$stmt2->execute([
            $dataContrato, $salario, $senhaHash, $idPessoa
        ])) throw new Exception('Falha na segunda inserção');

        // Verifica se o funcionario é medico, se sim faz inserção na tabela
        if($ehMedico){

            $idFuncionario = $pdo->lastInsertId();
            $stmt3 = $pdo->prepare($sqlTMedico);
            if (!$stmt3->execute([
                $especialidade, $crm, $idFuncionario
            ])) throw new Exception('Falha na terceira inserção');
        }

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