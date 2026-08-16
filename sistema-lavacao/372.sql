DROP DATABASE IF EXISTS db_lavacao3_70;
CREATE DATABASE IF NOT EXISTS db_lavacao3_70;
USE db_lavacao3_70;

-- Create table for vehicle cor (color)
CREATE TABLE cor (
   id INT NOT NULL AUTO_INCREMENT,
   nome VARCHAR(50) NOT NULL,
   CONSTRAINT pk_cor
   PRIMARY KEY (id)
);
INSERT INTO cor (nome) VALUES
('Verde'),
('Amarelo'),
('Roxo'),
('Laranja'),
('Marrom'),
('Rosa'),
('Prata'),
('Dourado'),
('Bordo'),
('Bege');



-- Create table for vehicle marca (brand)
CREATE TABLE marca (
   id INT NOT NULL AUTO_INCREMENT,
   nome VARCHAR(50) NOT NULL,
   CONSTRAINT pk_marca
   PRIMARY KEY (id)
);

INSERT INTO marca (nome) VALUES
('Chevrolet'),
('Fiat'),
('Toyota'),
('Ford'),
('Volkswagen'),
('Honda'),
('Hyundai'),
('Nissan'),
('BMW'),
('Audi'),
('Mercedes-Benz'),
('Kia'),
('Subaru'),
('Mazda');

-- Create table for vehicle modelo (model)
CREATE TABLE modelo (
   id INT NOT NULL AUTO_INCREMENT,
   descricao VARCHAR(50) NOT NULL,
   categoria ENUM('PEQUENO', 'MEDIO', 'GRANDE', 'MOTO', 'PADRAO') NOT NULL,
   id_marca INT NOT NULL,
   CONSTRAINT pk_modelo
      PRIMARY KEY(id),
   CONSTRAINT fk_modelo_marca
      FOREIGN KEY(id_marca)
      REFERENCES marca(id)
);



-- Add vehicle models for each brand
INSERT INTO modelo (descricao, categoria, id_marca) VALUES
('Sonic', 'PEQUENO', 1),          -- Chevrolet
('Camaro', 'GRANDE', 1),
('Cruze', 'MEDIO', 1),
('Onix', 'PEQUENO', 1),
('Tracker', 'PADRAO', 1),

('Mobi', 'PEQUENO', 2),           -- Fiat
('Argo', 'MEDIO', 2),
('Cronos', 'MEDIO', 2),
('Toro', 'PADRAO', 2),
('Uno', 'PEQUENO', 2),

('Yaris', 'MEDIO', 3),            -- Toyota
('Hilux', 'PADRAO', 3),
('Etios', 'PEQUENO', 3),
('RAV4', 'PADRAO', 3),
('Prius', 'PADRAO', 3),

('Focus', 'MEDIO', 4),            -- Ford
('Ranger', 'PADRAO', 4),
('Ka', 'PEQUENO', 4),
('EcoSport', 'PADRAO', 4),
('Fiesta', 'PEQUENO', 4),

('Golf', 'MEDIO', 5),             -- Volkswagen
('Passat', 'GRANDE', 5),
('T-Cross', 'PADRAO', 5),
('Jetta', 'MEDIO', 5),
('Up!', 'PEQUENO', 5),

('Civic', 'MEDIO', 6),            -- Honda
('City', 'PEQUENO', 6),
('HR-V', 'PADRAO', 6),
('Fit', 'PEQUENO', 6),
('Accord', 'GRANDE', 6),

('HB20', 'PEQUENO', 7),           -- Hyundai
('Creta', 'PADRAO', 7),
('i30', 'MEDIO', 7),
('Tucson', 'PADRAO', 7),
('Santa Fe', 'GRANDE', 7),

('Versa', 'MEDIO', 8),            -- Nissan
('March', 'PEQUENO', 8),
('Kicks', 'PADRAO', 8),
('Sentra', 'MEDIO', 8),
('Frontier', 'PADRAO', 8),

('Série 3', 'MEDIO', 9),          -- BMW
('X1', 'PADRAO', 9),
('Série 1', 'PEQUENO', 9),
('X3', 'PADRAO', 9),
('X5', 'GRANDE', 9),

('A3', 'MEDIO', 10),              -- Audi
('A4', 'MEDIO', 10),
('Q3', 'PADRAO', 10),
('A1', 'PEQUENO', 10),
('Q7', 'GRANDE', 10),

('Classe A', 'PEQUENO', 11),      -- Mercedes-Benz
('Classe C', 'MEDIO', 11),
('GLA', 'PADRAO', 11),
('Classe E', 'GRANDE', 11),
('Classe G', 'GRANDE', 11),

('Cerato', 'MEDIO', 12),          -- Kia
('Picanto', 'PEQUENO', 12),
('Sportage', 'PADRAO', 12),
('Sorento', 'GRANDE', 12),
('Rio', 'PEQUENO', 12),

('Impreza', 'MEDIO', 13),         -- Subaru
('Outback', 'PADRAO', 13),
('Forester', 'PADRAO', 13),
('Legacy', 'MEDIO', 13),
('XV', 'PADRAO', 13),

('MX-5', 'PEQUENO', 14),          -- Mazda
('CX-5', 'PADRAO', 14),
('Mazda2', 'PEQUENO', 14),
('Mazda3', 'MEDIO', 14),
('Mazda6', 'MEDIO', 14);


/*TABELA MOTOR COM RELACIONAMENTO 1:1 PARA PRODUTO*/
CREATE TABLE motor(
   id_modelo int NOT NULL,
   potencia int NOT NULL DEFAULT 100,
   tipoCombustivel enum('GASOLINA','ETANOL','FLEX','GNV','OUTRO') NOT NULL DEFAULT 'GASOLINA',
   CONSTRAINT pk_motor PRIMARY KEY (id_modelo),
    CONSTRAINT fk_motor_modelo FOREIGN KEY (id_modelo) REFERENCES modelo(id) ON DELETE CASCADE
);

INSERT INTO motor (id_modelo, potencia, tipoCombustivel) VALUES
(1, 172, 'GASOLINA'),
(2, 130, 'ETANOL'),
(3, 177, 'ETANOL'),
(4, 143, 'FLEX'),
(5, 112, 'GNV'),
(6, 165, 'GASOLINA'),
(7, 125, 'ETANOL'),
(8, 150, 'FLEX'),
(9, 155, 'GASOLINA'),
(10, 120, 'ETANOL'),
(11, 130, 'GASOLINA'),
(12, 140, 'ETANOL'),
(13, 145, 'FLEX'),
(14, 135, 'GASOLINA'),
(15, 110, 'ETANOL'),
(16, 155, 'GASOLINA'),
(17, 130, 'ETANOL'),
(18, 145, 'FLEX'),
(19, 160, 'GASOLINA'),
(20, 120, 'ETANOL'),
(21, 140, 'GASOLINA'),
(22, 150, 'ETANOL'),
(23, 115, 'FLEX'),
(24, 135, 'GASOLINA'),
(25, 110, 'ETANOL'),
(26, 165, 'GASOLINA'),
(27, 125, 'ETANOL'),
(28, 140, 'FLEX'),
(29, 150, 'GASOLINA'),
(30, 130, 'ETANOL'),
(31, 145, 'GASOLINA'),
(32, 120, 'ETANOL'),
(33, 140, 'GASOLINA'),
(34, 150, 'ETANOL'),
(35, 115, 'FLEX'),
(36, 135, 'GASOLINA'),
(37, 110, 'ETANOL'),
(38, 155, 'GASOLINA'),
(39, 130, 'ETANOL'),
(40, 145, 'FLEX'),
(41, 160, 'GASOLINA'),
(42, 120, 'ETANOL'),
(43, 140, 'GASOLINA'),
(44, 150, 'ETANOL'),
(45, 115, 'FLEX'),
(46, 135, 'GASOLINA'),
(47, 110, 'ETANOL'),
(48, 155, 'GASOLINA'),
(49, 130, 'ETANOL'),
(50, 145, 'FLEX'),
(51, 160, 'GASOLINA'),
(52, 120, 'ETANOL'),
(53, 140, 'GASOLINA'),
(54, 150, 'ETANOL'),
(55, 115, 'FLEX'),
(56, 135, 'GASOLINA'),
(57, 110, 'ETANOL'),
(58, 155, 'GASOLINA'),
(59, 130, 'ETANOL'),
(60, 145, 'FLEX'),
(61, 160, 'GASOLINA'),
(62, 120, 'ETANOL'),
(63, 140, 'GASOLINA'),
(64, 150, 'ETANOL'),
(65, 115, 'FLEX'),
(66, 135, 'GASOLINA'),
(67, 110, 'ETANOL'),
(68, 155, 'GASOLINA'),
(69, 130, 'ETANOL'),
(70, 145, 'FLEX');

CREATE TABLE cliente(
   id int NOT NULL auto_increment,
   nome varchar(50) NOT NULL,
   telefone varchar(50) NOT NULL,
   email varchar(100),
   data_cadastro DATE,
   CONSTRAINT pk_cliente
      PRIMARY KEY(id)
);

INSERT INTO cliente (nome, telefone, email, data_cadastro) VALUES
('João Silva', '(48) 9999-9991', 'joao@email.com', '1990-05-15'),
('Maria Souza', '(48) 9999-9891', 'maria@email.com', '1985-08-22'),
('Pedro Almeida', '(48) 9999-9391', 'pedro@email.com', '1978-11-30'),
('Ana Pereira', '(48) 9599-9991', 'ana@email.com', '1992-04-17'),
('Lucas Oliveira', '(48) 9999-4991', 'lucas@email.com', '1989-10-05'),
('Carla Santos', '(48) 9921-9991', 'carla@email.com', '1983-06-25'),
('Felipe Costa', '(48) 9992-9991', 'felipe@email.com', '1975-09-12'),
('Julia Ferreira', '(48) 9549-9991', 'julia@email.com', '1995-07-08'),
('Rafaela Barbosa', '(48) 9399-9991', 'rafaela@email.com', '1980-12-18'),
('Gustavo Mendes', '(48) 9999-3291', 'gustavo@email.com', '1998-02-28');

CREATE TABLE pessoafisica(
	id_cliente INT NOT NULL REFERENCES cliente(id),
    cpf VARCHAR(20) NOT NULL,
    data_nascimento date,
    CONSTRAINT pk_pessoafisica PRIMARY KEY (id_cliente),
    CONSTRAINT fk_pessoafisica_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id) 
		ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO pessoafisica (id_cliente, cpf, data_nascimento) VALUES
(1, '123.456.789-00', '1980-05-25'), -- Substitua pelos valores reais
(2, '987.654.321-01', '1975-10-15'),
(3, '567.890.123-02', '1988-02-28'),
(4, '890.123.456-03', '1993-09-03'),
(5, '345.678.901-04', '1987-12-12');


CREATE TABLE pessoajuridica(
	id_cliente INT NOT NULL REFERENCES cliente(id),
    cnpj VARCHAR(20) NOT NULL,
    inscricaoestadual varchar(100),
    CONSTRAINT pk_pessoajuridica PRIMARY KEY (id_cliente),
    CONSTRAINT fk_pessoajuridica_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id) 
		ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO pessoajuridica (id_cliente, cnpj, inscricaoestadual) VALUES
(10, '12345678901234', 'ISENTO'), -- Substitua pelos valores reais
(9, '98765432109876', '1234567890'),
(8, '56789012345678', 'ISENTO'),
(7, '89012345678901', '2345678901'),
(6, '34567890123456', '5678901234');

-- Create table for vehicle veiculo (vehicle)
CREATE TABLE veiculo (
   id INT NOT NULL AUTO_INCREMENT,
   placa VARCHAR(50) NOT NULL,
   observacoes VARCHAR(255) NOT NULL, -- Observacoes com 'o' minúsculo
   id_modelo INT NOT NULL,
   id_cor INT NOT NULL,
   id_cliente INT NOT NULL,
   CONSTRAINT pk_veiculo PRIMARY KEY (id),
   CONSTRAINT fk_veiculo_modelo FOREIGN KEY (id_modelo) REFERENCES modelo(id),
   CONSTRAINT fk_veiculo_cor FOREIGN KEY (id_cor) REFERENCES cor(id),
   CONSTRAINT fk_veiculo_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id)
);
-- Add 10 vehicles to the 'veiculo' table
INSERT INTO veiculo (placa, observacoes, id_modelo, id_cor, id_cliente) VALUES
('ABC1234', 'Veículo em ótimo estado', 1, 1, 1), -- Substitua pelos detalhes reais e IDs de cliente
('DEF5678', 'Precisa de manutenção', 2, 2, 2),
('GHI9012', 'Pouco uso', 3, 3, 3),
('JKL3456', 'Veículo esportivo', 4, 4, 4),
('MNO7890', 'Necessita troca de óleo', 5, 5, 5),
('PQR123', 'Ótima dirigibilidade', 6, 6, 6),
('STU456', 'Veículo familiar', 7, 7, 7),
('VWX789', 'Econômico', 8, 8, 8),
('YZA012', 'Para passeios longos', 9, 9, 9),
('BCD345', 'Confortável', 10, 10, 10);

CREATE TABLE servico (
   id INT NOT NULL AUTO_INCREMENT,
   descricao VARCHAR(50) NOT NULL,
   valor DECIMAL(10,2) NOT NULL,
   pontos int not null,
   categoria ENUM('PEQUENO', 'MEDIO', 'GRANDE', 'MOTO', 'PADRAO','TODOS') NOT NULL DEFAULT 'TODOS',
   CONSTRAINT pk_servico PRIMARY KEY(id)
);

INSERT INTO servico (descricao, valor, pontos, categoria) VALUES
('Troca de óleo', 50.00, 20, 'PEQUENO'),
('Revisão completa', 150.00, 50, 'GRANDE'),
('Troca de pneus', 200.00, 30, 'MEDIO'),
('Limpeza do sistema de ar condicionado', 80.00, 25, 'PADRAO'),
('Troca de correia dentada', 120.00, 40, 'MEDIO');


CREATE TABLE itemos (
   id INT NOT NULL AUTO_INCREMENT,
   valor DECIMAL(10,2) NOT NULL,
   observacao VARCHAR(255) NOT NULL,
   id_servico INT NOT NULL,
   id_ordemservico INT NOT NULL,
   primary key(id),
   key fk_item_de_os_servico(id_servico),
   key fk_item_de_os_ordemservico(id_ordemservico)

);

-- Assumindo que os IDs de serviços e ordens de serviço existem
INSERT INTO itemos (valor, observacao, id_servico, id_ordemservico) VALUES
(50.00, 'Troca de óleo realizada com sucesso.', 1, 1),
(150.00, 'Revisão completa feita conforme o recomendado.', 2, 2),
(200.00, 'Troca de pneus efetuada com alinhamento.', 3, 3),
(80.00, 'Limpeza do sistema de ar condicionado realizada.', 4, 4),
(120.00, 'Troca de correia dentada finalizada.', 5, 5);

 

CREATE TABLE ordemservico (
   id INT NOT NULL AUTO_INCREMENT,
   agenda DATE NOT NULL,
   total DECIMAL(10,2) NOT NULL,
   taxa_desconto DOUBLE,
   id_veiculo INT NOT NULL,
   estatus ENUM('FINALIZADA', 'CANCELADA', 'ABERTA') NOT NULL DEFAULT 'ABERTA',
   primary key(id),
   KEY fk_ordemservico_veiculo(id_veiculo)

);


INSERT INTO ordemservico (agenda, total, taxa_desconto, id_veiculo, estatus) VALUES
('2023-12-01', 250.00, 10.00, 1, 'FINALIZADA'),
('2023-12-02', 180.00, NULL, 2, 'ABERTA'),
('2023-12-03', 300.00, 15.00, 3, 'CANCELADA'),
('2023-12-04', 120.00, NULL, 1, 'ABERTA'),
('2023-12-05', 400.00, 20.00, 2, 'FINALIZADA');

