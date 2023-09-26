
// Chama as funções assim que carrega a pagina
window.onload = function (){

    /* EXIBIR OPÇÕES SE FOR MEDICO */
    // Armazena input checkbox
    check = document.querySelector("#iptehMedico");

    // Chama funação ao realizar ação no checkbox
    check.addEventListener("click", exibiOpMedico);


    /* TRANSIÇÃO DAS PAGINAS */
    // Armazena na variavel todos os botões que estão na tag nav
    buttons = document.querySelectorAll("nav button");

    // A cada botão na variavel buttons após evento click chama a função changeTab
    for (let button of buttons){
        button.addEventListener("click", identificaAba);
    }

    // Ao iniciar a pagina a primeira aba se inicia ativa
    acionaAba(0);

    /* PREENCHIMENTO AUTOMATICO DO ENDEREÇO */
    const inputCep = document.querySelector("#iptCep");
    inputCep.onkeyup = () => buscaEndereco(inputCep.value);


    /* DEIXAR DE EXIBIR LISTA DE CONSULTAS POR MEDICO */
    ehMedico = document.getElementById("ehmedico");
    if (!ehMedico){
        exibiConsultasMedico();
    }
    
}

// Função para requisação do endereço de acordo com o cep
function buscaEndereco(cep) {

  // Preenchimento automativo apenas para ceps
  // com quantidade de caracteres valido
  if (cep.length != 9) return;

  let xhr = new XMLHttpRequest();

  xhr.open("GET","../controller/busca-endereco.php?cep=" + cep, true);

  xhr.onload = function () {
  
      // verifica o código de status retornado pelo servidor
      if (xhr.status != 200) {
          console.error("Falha inesperada: " + xhr.responseText);
          return;
      }

      // converte a string JSON para objeto JavaScript
      try {
          var endereco = JSON.parse(xhr.responseText);
      }
      catch (e) {
          console.error("String JSON inválida: " + xhr.responseText);
          return;
      }

      // utiliza os dados retornados para preencher formulário
      let form = document.querySelector("form");
      form.log.value = endereco.log;
      form.cidade.value = endereco.cidade;
      form.estado.value = endereco.estado;
  }

  xhr.onerror = function () {
  console.error("Erro de rede - requisição não finalizada");
  };

  xhr.send();
}


// Função com o objetivo de achar o indice do botão clicado dentro da lista
function identificaAba(e){

    // Atraves do evento do click é armazenado o botão que foi clicado
    const botaoAcionado = e.target;

    // Armazena o nó especifico do botão que foi clicado
    const liDoBotao = botaoAcionado.parentNode;

    // Armazena todos os filhos da lista em um vetor 
    const nodes = Array.from(liDoBotao.parentNode.children);

    // Através do vetor é identificado o indice do botão que foi clicado
    const index = nodes.indexOf(liDoBotao);

    // Chama nova função passando o indice do botão como parametro
    acionaAba(index);
}

// Função com objetivo de ativar e desativar abas
function acionaAba(i) {
    
    // Armazena a aba que faz uso da classe .tabActive, ou seja
    // a aba que está ativa no momento 
    const tabActive = document.querySelector(".abaAtiva");

    // Verifica se foi armazenado a aba e faz a remoção da classe dela
    if (tabActive !== null){
        tabActive.className = "";
    }

    // Armazena o botão que faz uso da classe .buttonActive, ou seja
    // o botão que está ativa no momento 
    const buttonActive = document.querySelector(".botaoAtivo");

    // Verifica se tem algum botão na variavel para que possa remover a classe
    if (buttonActive !== null){
        buttonActive.className = "";
    }

    // Torna ativo a aba e o botão através do seu indice
    document.querySelectorAll(".abas section")[i].className = "abaAtiva"
    document.querySelectorAll("nav button")[i].className = "botaoAtivo"
}

// Função para exibir opção medico
function exibiOpMedico(){

    // contantes para saber qual div exibir e se a checkbox está marcada
    const marcado = document.querySelector("#iptehMedico");
    const opMedico = document.querySelector("#dadosMedico");

    // exibir ou deixar de exibir de acordo com o click 
    if (opMedico !== null && marcado.checked){
        opMedico.className = "row g-2 gy-3 gx-4 box-form";
    }else{
        document.querySelector("#dadosMedico").className = "opMedico"
    }
}

//Função para remover opção consultas do medico caso não seja medico ou não tenha
//consultas para serem exibidas
function exibiConsultasMedico(){

    // contantes com a linha que deve remover
    const suasConsultas = document.querySelector(".exibir-consultas");
    // Lista com todas as linhas
    const lista = document.querySelector("ul");

    // Remove linha
    lista.removeChild(suasConsultas);
}
