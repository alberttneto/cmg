<?php

    // Objeto horario
    class Horario
    {
        public $hora;

        function __construct($hora){
            $this->hora = $hora;
        }
    }

    // Conexão com o banco
    require "conexaoMysql.php";
    $pdo = mysqlConnect();

    // Armazena cep pelo metodo GET
    $data = $_GET['data_consulta'] ?? '';
    $medico = $_GET['nome_medico'] ?? '';

    // Consulta SQL
    $sqlMedico = <<<SQL
        SELECT medico.id FROM medico 
        INNER JOIN funcionario ON funcionario.id = id_funcionario 
        INNER JOIN pessoa ON pessoa.id = id_pessoa 
        AND nome = ?
    SQL;

    $sqlHorarios = <<<SQL
        SELECT horario FROM agenda
        WHERE data_agendamento = ?
        AND id_medico = ?
    SQL;

    // Recuperando dados do banco de dados
    try{
        $stmt = $pdo->prepare($sqlMedico);
        $stmt->execute([$medico]);

        $idMedico = $stmt->fetch();

        $stmt2 = $pdo->prepare($sqlHorarios);
        $stmt2->execute([$data, $idMedico['id']]);

    } catch (Exception $e) {  

        exit('Ocorreu uma falha: ' . $e->getMessage());
    }

    // Variaveis
    $hora_ocupado = []; // Vetor horarios ocupados
    $hora_livre = []; // Vetor horarios disponiveis
    $cont = 0; // variavel para contar posição no vetor

    // Percorre tabela de horarios ja agendados do medico na data informada
    while($row = $stmt2->fetch()){
        // Armazena no vetor horarios que não estão disponiveis
        $hora_ocupado[$cont] = substr($row["horario"], 0,2); // Armazena apenas hora no vetor

        $cont += 1;
    }

    // Zera posicao vetor
    $cont = 0;

    // Contagem das horas disponiveis para agendamento
    for ($i=8; $i <= 17 ; $i++) { 
        
        // Se horario não estiver no vetor ocupado armazena como horario livre
        if (!in_array(strval($i), $hora_ocupado)){

            // Cria objeto hora
            $h = new Horario(strval($i)); 

            // Armazena horario livre no vetor
            $hora_livre[$cont] = $h;

            $cont += 1;
        }
    }

    // Retorna no formato JSON vetor de objetos horas livres
    echo json_encode($hora_livre);
?>