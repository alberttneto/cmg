<?php

    // Objeto endereço
    class Endereco
    {
        public $log;
        public $cidade;
        public $estado;

        function __construct($log, $cidade, $estado){
            $this->log = $log;
            $this->cidade = $cidade;
            $this->estado = $estado;
        }
    }

    // Conexão com o banco
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Armazena cep pelo method GET
    $cep = $_GET['cep'] ?? '';

    // Consulta SQL
    $sql = <<<SQL
        SELECT logradouro, cidade, estado 
        FROM base_endereços_ajax WHERE 
        ? = cep
    SQL;

    // Recuperando dados do banco de dados
    try{
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cep]);

    } catch (Exception $e) {  

        exit('Ocorreu uma falha: ' . $e->getMessage());
    }

    // Armazenas dados em uma array com indice por coluna
    $dados = $stmt->fetch(PDO::FETCH_NUM);

    // Cria objetos com dados retornados da consulta no banco
    $endereco = new Endereco($dados[0], $dados[1], $dados[2]);

    // Converte para json
    echo json_encode($endereco);

?>