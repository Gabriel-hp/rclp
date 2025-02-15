-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14-Fev-2025 às 15:47
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `chamados`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `protocolo` varchar(255) NOT NULL,
  `assunto` varchar(255) NOT NULL,
  `abertoEm` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `criadoPor` varchar(255) NOT NULL,
  `resolvidoEm` timestamp NULL DEFAULT NULL,
  `fechadoEm` timestamp NULL DEFAULT NULL,
  `status` enum('Em Aberto','Aguardando','Fechado') NOT NULL,
  `origem` varchar(255) NOT NULL,
  `nivel` enum('Junior','Pleno','Senior') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tickets`
--

INSERT INTO `tickets` (`id`, `protocolo`, `assunto`, `abertoEm`, `criadoPor`, `resolvidoEm`, `fechadoEm`, `status`, `origem`, `nivel`, `created_at`, `updated_at`) VALUES
(2, 'CSLP-202501001409', 'Verificação de ponto de Internet - Relógio de Ponto', '2025-02-14 13:48:20', 'Eduarda Lopes Soares', NULL, NULL, 'Em Aberto', 'Agente', 'Pleno', NULL, NULL),
(3, 'CSLP-202502001948', 'Embrasil - MPTZ - CDPM1 - QUADRANTE 5 - câmera inoperante', '2025-02-14 13:48:25', 'Jonis Souza de Lima.', NULL, NULL, 'Aguardando', 'Agente', 'Pleno', NULL, NULL),
(4, 'CSLP-202502001949', 'SSP-PCL-194 - Alarme de Energia', '2025-02-14 13:48:31', 'Jonis Souza de Lima.', NULL, NULL, 'Aguardando', 'Agente', 'Pleno', NULL, NULL),
(5, 'CSLP-202502001955', 'SSP-PVG-0129 - Alarme de sinal', '2025-02-14 13:46:55', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Junior', NULL, NULL),
(6, 'CSLP-202502001961', 'MASSIVA OLT PONTA NEGRA GPON 1/1/1 - Alarme de Sinal', '2025-02-14 13:47:07', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Junior', NULL, NULL),
(7, 'CSLP-202502001983', 'SSP-PVG-039P - Alarme de Energia', '2025-02-14 13:48:49', 'Jonis Souza de Lima.', NULL, NULL, 'Aguardando', 'Agente', 'Senior', NULL, NULL),
(8, 'CSLP-202502001993', 'Embrasil - MPTZ - CDPM1 - QUADRA PV 1 - câmera inoperante', '2025-02-14 13:48:44', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Senior', NULL, NULL),
(9, 'CSLP-202502001862', 'SSP-PCL-112 - alarme de energia', '2025-02-14 13:47:39', 'Jonis Souza de Lima.', NULL, NULL, 'Aguardando', 'Agente', 'Junior', NULL, NULL),
(10, 'CSLP-202502002129', 'SSP-PVG-088 - Alarme de energia', '2025-02-14 13:47:49', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Junior', NULL, NULL),
(24, 'CSLP-202502001589', 'SSP-PVG-024 - Alarme de energia', '2025-02-14 13:48:38', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Senior', NULL, NULL),
(25, 'CSLP-202502001590', 'SSP-PVG-003 - Alarme de energia', '2025-02-14 13:48:08', 'Jonis Souza de Lima.', NULL, NULL, 'Em Aberto', 'Agente', 'Junior', NULL, NULL),
(55, 'CSLP-202502002109', 'Verificação de Link', '2025-02-12 15:56:42', 'Jéssica Araújo', NULL, NULL, 'Aguardando', 'Agente', 'Junior', NULL, NULL),
(59, 'CSLP-202502001817', 'Verificação de Link', '2025-02-14 13:53:20', 'Jéssica Araújo', NULL, NULL, 'Em Aberto', 'Agente', 'Junior', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_protocolo_unique` (`protocolo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
