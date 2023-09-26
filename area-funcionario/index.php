<?php
    // Código PHP para consultar tabelas

    // Conexão com o banco de dados
    require_once "../controller/conexaoMysql.php";
    require_once "../controller/verifica-sessao.php";

    session_start();
    $pdo = mysqlConnect();
    checkLogin($pdo);

    // Armazena nome do funcionario da sessão
    $nome_funcionario = $_SESSION['func'];


    // Consultar no banco de dados todos os funcionarios cadastrados
    $sqlFuncionario = <<<SQL
        SELECT * FROM pessoa
        INNER JOIN funcionario ON pessoa.id = id_pessoa
        LEFT JOIN medico ON funcionario.id = id_funcionario
    SQL;

    // Consultar todos pacientes
    $sqlPaciente = <<<SQL
        SELECT * FROM pessoa
        INNER JOIN paciente ON pessoa.id = id_pessoa
    SQL;

    // Consultar endereços cadastrados
    $sqlEnderecos = <<<SQL
        SELECT * FROM base_endereços_ajax
    SQL;

    // Consultar consultas agendadas
    $sqlConsultas = <<<SQL
        SELECT agenda.nome as paciente, agenda.email, agenda.sexo, data_agendamento, horario, pessoa.nome
        FROM agenda
        INNER JOIN medico ON medico.id = id_medico
        INNER JOIN funcionario ON funcionario.id = id_funcionario
        INNER JOIN pessoa ON pessoa.id = id_pessoa
    SQL;

    // Consultar consultar agendadas por medico

    $sqlConsultasMedico = <<<SQL
        SELECT pessoa.nome as medico, agenda.email, agenda.sexo, data_agendamento, horario, agenda.nome as paciente 
        FROM agenda 
        INNER JOIN medico ON medico.id = id_medico 
        INNER JOIN funcionario ON funcionario.id = id_funcionario 
        INNER JOIN pessoa ON pessoa.id = id_pessoa 
        AND pessoa.nome = ?
    SQL;

    // Envia a consulta ao banco de dados
    try{

        $dados1 = $pdo->query($sqlFuncionario);
        $dados2 = $pdo->query($sqlPaciente);
        $dados3 = $pdo->query($sqlEnderecos);
        $dados4 = $pdo->query($sqlConsultas);

        $dados5 = $pdo->prepare($sqlConsultasMedico);
        $dados5->execute([$nome_funcionario]);

    } catch (Exception $e) {
        exit('Ocorreu uma falha: ' . $e->getMessage());
    }

    
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Area Funcionário</title>
	    <meta charset="utf-8">
        <!-- Tag de responsividade -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css">
        <!-- Bootstrap bundle com JavaScript e bib. Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
        <!-- Folha de estilos CSS -->
        <link rel="stylesheet" href="../style/style-area-funcionario.css">
        <!-- JavaScript-->
        <script src="../scripts/home-funcionario.js"></script>
    </head>

    <body>

        <!-- CABEÇALHO-->
        <header>
            <div class="row ">
                <div class="col-sm-8 logo">
                    <img  src="../images/logo.jpg" alt="logo" width="201" height="46">
                </div>
                <div class="col-sm-4 nomeFun">
                    
                    <!-- Código PHP para exibir nome do funcionario que fez login-->
                    <?php
                        echo <<<HTML
                            <p><span style="color: #6DDAD4; margin-right: 8px;"> Funcionário:</span> $nome_funcionario</p>
                        HTML;
                    ?>
                </div>
            </div>
        </header>

        <!-- BARRA DE NAVEGAÇÃO-->
        <nav class="nav" id="ini">
            <ul>
                <li>
                    <button class="botaoAtivo">
                        Cadastrar Funcionário
                    </button> 
                </li>
                <li>
                    <button>
                        Cadastro Paciente
                    </button>
                </li>
                <li>
                    <button>
                        Listar Funcionários
                    </button>
                </li>
                <li>
                    <button>
                        Listar Pacientes
                    </button>
                </li>
                <li>
                    <button>
                        Listar Endereços
                    </button>
                </li>
                <li>
                    <button>
                        Listar Consultas Agendadas
                    </button>
                </li>
                <li class="exibir-consultas">
                    <button>
                        Suas Consultas Agendadas
                    </button>
                </li>
                <li>
                    <a class="nav-link" href="../controller/logout.php">Sair</a>
                </li>
            </ul>
        </nav>

        <!-- O main esta dividido por seções onde o click do botão controla qual seção 
        deve ser exibida-->
        <main class="container abas">

            <!-- FORMULARIO PARA CADASTRO DOS FUNCIONARIOS-->
            <section class="abaAtiva">

                <form action="../controller/cadastro-funcionario.php" method="POST">

                    <h3>Dados do Funcionário</h3>
                    <hr>

                    <div class="row g-2 gy-3 gx-4 box-form">
    
                        <div class="col-sm-6">
                            <label for="nome" class="form-label">Nome completo</label>
                            <input type="text" name="nome" class="form-control" id="nome">
                        </div>
        
                        <div class="col-sm-6">
                            <label for="iptsexo" class="form-label">Sexo</label>
                            <select id="iptsexo" name="sexo" class="form-select">
                                <option selected>Sel.</option>
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                            </select>
    
                        </div>
        
                        <div class="col-sm-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" id="email">
                        </div>
    
                        <div class="col-sm-6">
                            <label for="iptfone" class="form-label">Telefone</label>
                            <input type="tel" id="iptfone" name="fone" class="form-control">
                        </div>
                    </div>
    
                    <div class="row g-2 gy-3 gx-4 box-form">
                        <div class="col-sm-6">
                            <label for="iptCep" class="form-label">CEP (formato xxxxx-xxx):</label>
                            <input type="text" id="iptCep" name="cep" class="form-control">
                        </div>
                        
                        <div class="col-sm-6">
                            <label for="log" class="form-label">Logradouro</label>
                            <input type="text" id="log" name="log" class="form-control">
                        </div>
                            
                        <div class="col-sm-6">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" id="cidade" name="cidade" class="form-control">
                        </div>
    
                        <div class="col-sm-6">
                            <label for="estado" class="form-label">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option selected>Sel.</option>
                                <option value="Acre" >AC</option>
                                <option value="Alagoas">AL</option>
                                <option value="Amapa">AP</option>
                                <option value="Aamazonas">AM</option>
                                <option value="Bahia">BA</option>
                                <option value="Ceara">CE</option>
                                <option value="Distrito Federal">DF</option>
                                <option value="Espirito Santo">ES</option>
                                <option value="Goias">GO</option>
                                <option value="Maranhão">MA</option>
                                <option value="Mato Grosso">MT</option>
                                <option value="Mato Grosso do Sul">MS</option>
                                <option value="Minas Gerais">MG</option>
                                <option value="Para">PA</option>
                                <option value="Paraiba">PB</option>
                                <option value="Parana">PR</option>
                                <option value="Pernanbuco">PE</option>
                                <option value="Piaui">PI</option>
                                <option value="Rio de Janeiro">RJ</option>
                                <option value="Rio Grande do Norte">RN</option>
                                <option value="Rio Grande do Sul">RS</option>
                                <option value="Rondonia">RO</option>
                                <option value="Roraima">RR</option>
                                <option value="Santa Catarina">SC</option>
                                <option value="São Paulo">SP</option>
                                <option value="Sergipe">SE</option>
                                <option value="Tocantins">TO</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="iptDataContrato" class="form-label">Data início do contrato</label>
                            <input type="date" id="iptDataContrato" name="dataContrato" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptSalario" class="form-label">Salário</label>
                            <input type="number" step="0.01" min="0.01" id="iptSalario" name="salario" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptSenha" class="form-label">Senha</label>
                            <input type="password" id="iptSenha" name="senha" class="form-control">
                        </div>

                        <div class="col-sm-12">
                            <input class="form-check-input" type="checkbox" name="ehMedico" id="iptehMedico">
                            <label class="form-check-label" for="iptehMedico">
                                funcionário médico
                            </label>
                        </div>
                    </div>

                    <div class="row g-2 gy-3 gx-4 box-form opMedico" id="dadosMedico">

                        <div class="col-sm-6">
                            <label for="especialidade" class="form-label">Escpecialidade médico</label>
                            <input type="text" name="especialidade" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="crm" class="form-label">CRM</label>
                            <input type="text" name="crm" class="form-control">
                        </div>

                    </div>
                    <div class="row g-2 gy-3 gx-4 box-form">

                        <div class="col-2">
                            <button type="submit" class="btn btn-success">Cadastrar</button>
                        </div>
                    </div> 
                </form>
            </section>

            <!-- FORMULARIO PARA CADASTRO DE PACIENTE-->
            <section>
                <form action="../controller/cadastro-paciente.php" method="POST">

                    <h3>Dados do Paciente</h3>
                    <hr>

                    <div class="row g-2 gy-3 gx-4 box-form">

                        <div class="col-sm-6">
                            <label for="iptnome" class="form-label">Nome completo</label>
                            <input type="text" id="iptnome" name="nome" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptsexo2" class="form-label">Sexo</label>
                            <select id="iptsexo2" name="sexo" class="form-select">
                                <option selected>Sel.</option>
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label for="iptEmail" class="form-label">E-mail</label>
                            <input type="email" id="iptEmail" name="email" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptfone2" class="form-label">Telefone</label>
                            <input type="tel" id="iptfone2" name="fone" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptcep2" class="form-label">CEP formato xxxxx-xxx:</label>
                            <input type="text" id="iptcep2" name="cep" class="form-control">
                        </div>

                        <div class="col-sm-6">
                            <label for="iptlog" class="form-label">Logradouro</label>
                            <input type="text" id="iptlog" name="log" class="form-control">
                        </div>
                        
                        <div class="col-sm-8">
                            <label for="iptcidade" class="form-label">Cidade</label>
                            <input type="text" id="iptcidade" name="cidade" class="form-control">
                        </div>

                        <div class="col-sm-4">
                            <label for="iptestado2" class="form-label">Estado</label>
                            <select id="iptestado2" name="estado" class="form-select">
                                <option selected>Sel.</option>
                                <option value="Acre" >AC</option>
                                <option value="Alagoas">AL</option>
                                <option value="Amapa">AP</option>
                                <option value="Aamazonas">AM</option>
                                <option value="Bahia">BA</option>
                                <option value="Ceara">CE</option>
                                <option value="Distrito Federal">DF</option>
                                <option value="Espirito Santo">ES</option>
                                <option value="Goias">GO</option>
                                <option value="Maranhão">MA</option>
                                <option value="Mato Grosso">MT</option>
                                <option value="Mato Grosso do Sul">MS</option>
                                <option value="Minas Gerais">MG</option>
                                <option value="Para">PA</option>
                                <option value="Paraiba">PB</option>
                                <option value="Parana">PR</option>
                                <option value="Pernanbuco">PE</option>
                                <option value="Piaui">PI</option>
                                <option value="Rio de Janeiro">RJ</option>
                                <option value="Rio Grande do Norte">RN</option>
                                <option value="Rio Grande do Sul">RS</option>
                                <option value="Rondonia">RO</option>
                                <option value="Roraima">RR</option>
                                <option value="Santa Catarina">SC</option>
                                <option value="São Paulo">SP</option>
                                <option value="Sergipe">SE</option>
                                <option value="Tocantins">TO</option>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label for="iptpeso" class="form-label">Peso</label>
                            <input type="number" id="iptpeso" name="peso" class="form-control">
                        </div>

                        <div class="col-sm-4">
                            <label for="iptaltura" class="form-label">Altura</label>
                            <input type="number" id="iptaltura" name="altura" class="form-control">
                        </div>

                        <div class="col-sm-4">
                            <label for="iptsangue" class="form-label">Tipo Sanguineo</label>
                            <input type="text" id="iptsangue" name="tiposangue" class="form-control" >
                        </div>

                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-success">
                                Cadastrar
                            </button>
                        </div>
                    </div>
                </form>
            </section>
            
            <!-- TABELA HTML PARA EXIBIÇÃO DOS FUNCIONARIOS CADASTRADOS -->
            <section>
                <h3>Funcionários Cadastrados</h3>
                <div class="tabelas container">

                    <table>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Sexo</th>
                            <th>CEP</th>
                            <th>Logradouro</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th>Data da Contratação</th>
                            <th>Salário</th>
                            <th>CRM</th>
                            <th>Especialidade</th>
                        </tr>

                        <!-- Código PHP para exibir funcionarios retornados do BD-->
                        <?php
                            while ($row = $dados1->fetch()) {

                                // Tratamento de dados para evitar ataque XSS
                                $nome = htmlspecialchars($row['nome']);
                                $sexo = htmlspecialchars($row['sexo']);
                                $email = htmlspecialchars($row['email']);
                                $fone = htmlspecialchars($row['telefone']);
                                $cep = htmlspecialchars($row['cep']);
                                $log = htmlspecialchars($row['logradouro']);
                                $cidade = htmlspecialchars($row['cidade']);
                                $estado = htmlspecialchars($row['estado']);
                                $data_contrato = htmlspecialchars($row['data_contrato']);
                                $salario = htmlspecialchars($row['salario']);
                                $crm = htmlspecialchars($row['crm']);
                                $especialidade = htmlspecialchars($row['especialidade']);
                                
                                echo <<<HTML
                                    <tr>
                                        <td>$nome</td>
                                        <td>$email</td>
                                        <td>$fone</td>
                                        <td>$sexo</td>
                                        <td>$cep</td>
                                        <td>$log</td>
                                        <td>$cidade</td>
                                        <td>$estado</td>
                                        <td>$data_contrato</td>
                                        <td>$salario</td>
                                        <td>$crm</td>
                                        <td>$especialidade</td>
                                    </tr>
                                HTML;
                            }                           

                        ?>
                    </table>
                </div>
            </section>

            <!-- TABELA HTML PARA EXIBIÇÃO DOS PACIENTES CADASTRADOS -->
            <section>
                <h3>Pacientes Cadastrados</h3>
                <div class="tabelas container">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Sexo</th>
                        <th>CEP</th>
                        <th>Logradouro</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>Peso</th>
                        <th>Altura</th>
                        <th>Tipo Sanguineo</th>
                    </tr>

                    <!-- Código PHP para exibir pacientes retornados do BD-->
                    <?php
                            while ($row = $dados2->fetch()) {

                                // Tratamento de dados para evitar ataque XSS
                                $nome = htmlspecialchars($row['nome']);
                                $sexo = htmlspecialchars($row['sexo']);
                                $email = htmlspecialchars($row['email']);
                                $fone = htmlspecialchars($row['telefone']);
                                $cep = htmlspecialchars($row['cep']);
                                $log = htmlspecialchars($row['logradouro']);
                                $cidade = htmlspecialchars($row['cidade']);
                                $estado = htmlspecialchars($row['estado']);
                                $peso = htmlspecialchars($row['peso']);
                                $altura = htmlspecialchars($row['altura']);
                                $tipoS = htmlspecialchars($row['tipo_sanguineo']);

                            
                                echo <<<HTML
                                    <tr>
                                        <td>$nome</td>
                                        <td>$email</td>
                                        <td>$fone</td>
                                        <td>$sexo</td>
                                        <td>$cep</td>
                                        <td>$log</td>
                                        <td>$cidade</td>
                                        <td>$estado</td>
                                        <td>$peso</td>
                                        <td>$altura</td>
                                        <td>$tipoS</td>
                                    </tr>
                                HTML;
                            }                           
                    ?>
                </table>
            </div>
            </section>

            <!-- TABELA HTML PARA EXIBIÇÃO DOS ENDEREÇOS CADASTRADOS -->
            <section>
                <h3>Endereços Cadastrados</h3>
                <div class="tabelas container">
                <table>
                    <tr>
                        <th>CEP</th>
                        <th>Logradouro</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                    </tr>

                    <!-- Código PHP para exibir endereços retornados do BD-->
                    <?php
                        while ($row = $dados3->fetch()) {

                            // Tratamento de dados para evitar ataque XSS
                            $cep = htmlspecialchars($row['cep']);
                            $log = htmlspecialchars($row['logradouro']);
                            $cidade = htmlspecialchars($row['cidade']);
                            $estado = htmlspecialchars($row['estado']);

                            echo <<<HTML
                                <tr>
                                    <td>$cep</td>
                                    <td>$log</td>
                                    <td>$cidade</td>
                                    <td>$estado</td>
                                </tr>
                            HTML;
                        }                           
                    ?>
                </table>
            </div>
            </section>

            <section>
                <h3>Consultas Agendadas</h3>
                <div class="tabelas container">
                <table>
                    <tr>
                        <th>Paciente</th>
                        <th>E-mail</th>
                        <th>Sexo</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Medico</th>
                    </tr>

                    <!-- Código PHP para exibir endereços retornados do BD-->
                    <?php
                        while ($row = $dados4->fetch()) {

                            // Tratamento de dados para evitar ataque XSS
                            $paciente = htmlspecialchars($row['paciente']);
                            $email = htmlspecialchars($row['email']);
                            $sexo = htmlspecialchars($row['sexo']);
                            $data = htmlspecialchars($row['data_agendamento']);
                            $hora = htmlspecialchars($row['horario']);
                            $medico = htmlspecialchars($row['nome']);

                            echo <<<HTML
                                <tr>
                                    <td>$paciente</td>
                                    <td>$email</td>
                                    <td>$sexo</td>
                                    <td>$data</td>
                                    <td>$hora</td>
                                    <td>$medico</td>
                                </tr>
                            HTML;
                        }                           
                    ?>
                </table>
            </div>
            </section>

            <section>
                <h3>Suas Consultas Agendadas</h3>
                <div class="tabelas container">
                <table id="consultas-medico">
                        <tr>
                            <th>Medico</th>
                            <th>Paciente</th>
                            <th>E-mail</th>
                            <th>Sexo</th>
                            <th>Data</th>
                            <th>Hora</th>
                        </tr>

                    <!-- Código PHP para exibir endereços retornados do BD-->
                    <?php
                        while ($row = $dados5->fetch()) {

                            // Tratamento de dados para evitar ataque XSS
                            $paciente = htmlspecialchars($row['paciente']);
                            $email = htmlspecialchars($row['email']);
                            $sexo = htmlspecialchars($row['sexo']);
                            $data = htmlspecialchars($row['data_agendamento']);
                            $hora = htmlspecialchars($row['horario']);
                            $medico = htmlspecialchars($row['medico']);
                            

                            echo <<<HTML
                                <tr>
                                        <td id="ehmedico">$medico</td>
                                        <td>$paciente</td>
                                        <td>$email</td>
                                        <td>$sexo</td>
                                        <td>$data</td>
                                        <td>$hora</td>
                                </tr>
                            HTML;

                        }                           
                    ?>
                </table>
            </div>
            </section>
            
        </main>

        <footer>
            <hr>
            <p>
                <a href="#ini">Voltar ao topo</a>
            </p>
        </footer>
        
    </body>
</html>