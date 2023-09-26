

function buscaMedico(esp) {

    let xhr = new XMLHttpRequest();

    xhr.open("GET", "controller/busca-medicos.php?especialidade=" + esp, true);

    xhr.onload = function () {
        
        // verifica o código de status retornado pelo servidor
        if (xhr.status != 200) {
            console.error("Falha inesperada: " + xhr.responseText);
            return;
        }

        // converte a string JSON para objeto JavaScript
        try {
            var medicos = JSON.parse(xhr.responseText);
            
        }
        catch (e) {
            console.error("String JSON inválida: " + xhr.responseText);
            return;
        }

        // Incluindo medicos na seleção
        if(esp == "- Selecione -"){
            return;
        } else{

            // Percorrer vetor com nome dos medicos
            for(let i = 0; i < medicos.length; i++){
                // recupera elemento select
                var campoSelect = document.getElementById("iptMedico");
                // Cria elemento option
                var option = document.createElement("option");
                // Informa nome e valor do elemento option
                option.text = medicos[i].nome;
                option.value = medicos[i].nome;
                // Adiciona option no select
                campoSelect.add(option);
            }
        }
    }

    xhr.onerror = function () {
        console.error("Erro de rede - requisição não finalizada");
    };

    xhr.send();
}

function buscaHorarios(data, medico) {

    let xhr = new XMLHttpRequest();

    xhr.open("GET","controller/busca-horarios.php?data_consulta=" + data + "&nome_medico=" + medico, true);

    xhr.onload = function () {
    
        // verifica o código de status retornado pelo servidor
        if (xhr.status != 200) {
            console.error("Falha inesperada: " + xhr.responseText);
            return;
        }

        // converte a string JSON para objeto JavaScript
        try {
            var horarios = JSON.parse(xhr.responseText);
        }
        catch (e) {
            console.error("String JSON inválida: " + xhr.responseText);
            return;
        }

        // Percorrer vetor com horarios livres
        for(let i = 0; i < horarios.length; i++){
            // recupera elemento select
            var campoSelect = document.getElementById("iptHorarioDisp");
            // Cria elemento option
            var option = document.createElement("option");
            // Informa nome e valor do elemento option
            option.text = horarios[i].hora;
            option.value = horarios[i].hora;
            // Adiciona option no select
            campoSelect.add(option);
        }

    }

    xhr.onerror = function () {
    console.error("Erro de rede - requisição não finalizada");
    };

    xhr.send();
}

window.onload = function () {
    const selectElement = document.querySelector('#iptEspecialidade');
    selectElement.addEventListener('change', (event) => {

        // Remove opções existentes
        var elem = document.getElementById("iptMedico");    
        elem.options.length = 0;    

        // Cria as opções de acordo com a especialidade
        buscaMedico(event.target.value);
    });

    const inputData = document.querySelector("#iptData");
    const inputMedico = document.querySelector("#iptMedico");
    inputData.addEventListener('change', (event) =>{

        // Remove opções existentes
        var elem = document.getElementById("iptHorarioDisp");
        elem.options.length = 0;
        
        //Cria as opções de acordo com o horario disponivel
        buscaHorarios(event.target.value, inputMedico.value);
    });
}