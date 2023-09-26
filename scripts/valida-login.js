
// Chama função ao careegar página
window.onload = function () { 

    // Armazena elemento button
    const btLogin = document.getElementById("btLogin");

    // Chama função de validação ao clicar no botão
    btLogin.addEventListener("click", validaLogin);
    
}

// Função para validar login usando AJAX
function validaLogin() {

    // Armazena formulario
    let meuForm = document.querySelector("form");

    // Cria objeto com formulario
    let formData = new FormData(meuForm);

    let xhr = new XMLHttpRequest();

    // Fazendo requisição
    xhr.open("POST", "controller/valida-login.php");
    

    xhr.onload = function () {
        
        // verifica o código de status retornado pelo servidor
        if (xhr.status != 200) {
            console.error("Falha inesperada: " + xhr.responseText);
            return;
        }

        // converte a string JSON para objeto JavaScript
        try {
            var response = JSON.parse(xhr.responseText);
        }
        catch (e) {
            console.error("String JSON inválida: " + xhr.responseText);
        return;
        }

        // Se login for valido direciona para página do funcionario
        // caso contrario informa login invalido
        if (response.success){
            window.location = response.destination;
        } else{
            document.querySelector("#loginFailMsg").style.display = 'block';
        }

    }

    xhr.onerror = function () {
        console.error("Erro de rede - requisição não finalizada");
    };
    
    xhr.send(formData);
}
