CREATE TABLE pessoa
(
    id int PRIMARY KEY auto_increment,
    nome varchar(50) not null,
    sexo varchar(9),
    email varchar(50) UNIQUE,
    telefone varchar(14),
    cep char(10),
    logradouro varchar(100),
    cidade varchar(50),
    estado char(30)
) ENGINE=InnoDB;

CREATE TABLE funcionario
(
    id int PRIMARY KEY auto_increment,
    data_contrato date not null,
    salario float not null,
    hash_senha varchar(255) not null,
    id_pessoa int not null,
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE paciente(
    id int PRIMARY KEY auto_increment,
    peso int,
    altura int,
    tipo_sanguineo varchar(3),
    id_pessoa int not null,
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id) ON DELETE CASCADE
);

CREATE TABLE medico(
    id int PRIMARY KEY auto_increment,
    especialidade varchar(50),
    crm char(6) UNIQUE not null,
    id_funcionario int not null,
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id) ON DELETE CASCADE
);

CREATE TABLE agenda(
    id int PRIMARY KEY auto_increment,
    data_agendamento date not null,
    horario time not null,
    nome varchar(50) not null,
    sexo varchar(9),
    email varchar(50) UNIQUE not null,
    id_medico int not null,
    FOREIGN KEY (id_medico) REFERENCES medico(id)
);

CREATE TABLE base_endereços_ajax(
    id int PRIMARY KEY auto_increment,
    cep char(10),
    logradouro varchar(100),
    cidade varchar(50),
    estado char(30)
);