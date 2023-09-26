<?php

    // Objeto medico armazena nome
    class medico{

        public $nome;

        function __construct($nome){
            $this->nome = $nome;
        }
    }

    // Conexão BD
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    //Trata dados em branco e armazena
    if (isset($_GET["especialidade"])){
        $especialidade = $_GET["especialidade"];
    }

    //Código SQL de consulta nome medico
    $sql = <<<SQL
        SELECT nome FROM pessoa
        INNER JOIN funcionario ON pessoa.id = id_pessoa
       	INNER JOIN medico ON funcionario.id = id_funcionario
        AND especialidade = ?
    SQL;

    // Realizando consulta no BD
    try{

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$especialidade]);

    } catch(Exception $e){
        exit('Falha inesperada: ' . $e->getMessage());
    }

    
    $cont = 0; //Variavel posição no vetor medico
    $medicos = []; //Vetor para armazenar medicos

    //Percorrer tabela medicos, cria objeto com nome do medico e adiciona ao vetor de objetos
    while($row = $stmt->fetch()){  
        $m = new medico($row["nome"]); 
        $medicos[$cont] = $m;
    
        $cont += 1;
    }

    // Retorna em formato JSON vetor de objetos medicos
    echo json_encode($medicos);
?>