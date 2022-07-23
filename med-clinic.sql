-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2022 at 09:35 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proiectweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `aprecieripostari`
--

CREATE TABLE `aprecieripostari` (
  `idApreciere` int(11) NOT NULL,
  `idPostare` int(11) NOT NULL,
  `idUtilizator` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categoriiconsultatii`
--

CREATE TABLE `categoriiconsultatii` (
  `id` int(11) NOT NULL,
  `denumirec` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categoriiconsultatii`
--

INSERT INTO `categoriiconsultatii` (`id`, `denumirec`) VALUES
(1, 'Stomatologie'),
(2, 'Cardiologie'),
(3, 'Oftalmologie'),
(11, 'Neurologie');

-- --------------------------------------------------------

--
-- Table structure for table `categoriipostari`
--

CREATE TABLE `categoriipostari` (
  `id` int(11) NOT NULL,
  `denumireCategorie` varchar(50) NOT NULL,
  `path_poza` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categoriipostari`
--

INSERT INTO `categoriipostari` (`id`, `denumireCategorie`, `path_poza`) VALUES
(4, 'Noutăți', ''),
(5, 'Promotii', ''),
(7, 'Categorie de test', ''),
(8, 'categorieDeTest', '');

-- --------------------------------------------------------

--
-- Table structure for table `cazuricovid`
--

CREATE TABLE `cazuricovid` (
  `id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `nr_cazuri` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `comentariipostari`
--

CREATE TABLE `comentariipostari` (
  `idComentariu` int(11) NOT NULL,
  `id_postare` int(11) NOT NULL,
  `id_autor_comentariu` int(11) NOT NULL,
  `comentariu` varchar(1000) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stareComentariu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `comentariipostari`
--

INSERT INTO `comentariipostari` (`idComentariu`, `id_postare`, `id_autor_comentariu`, `comentariu`, `data`, `stareComentariu`) VALUES
(1, 13, 1, 'Acesta este un comentariu de test.', '2021-12-13 13:38:49', 0),
(2, 5, 1, 'Foarte tare masina!', '2021-12-13 13:40:40', 1),
(3, 5, 1, 'pe patru roți, motociclete, ATV (ATV-uri), motor outboard, scaune cu rotile și o varietate de alte motoare mici cu ardere internă. În 2016, Suzuki a fost al unsprezecelea cel mai mare producător auto din toată lumea. În 2016, Suzuki a fost al unsprezecelea cel mai mare producător auto din toată lumea.Suzuki are peste 45.000 de angajați și are 35 de instalații de producție în 23 de țări și 133 distribuitori în 192 de țări. Volumul vânzărilor la nivel mondial de automobile este al zecelea cel mai mare din lume, în timp ce volumul vânzărilor interne este al treilea ca mărime din țară. Sistemul Hybrid de 48V, disponibil pe Vitara, SX4 S-Cross și Swift Sport este cel mai avansat sistem Mild Hybrid din gama Suzuki. Acesta are principii de funcționare ', '2021-12-13 13:38:37', 1),
(4, 5, 1, 'Acesta este un comentariu', '2021-12-13 13:41:34', 1),
(5, 5, 1, 'Acesta este un alt comentariu', '2021-12-13 13:41:47', 1),
(6, 5, 13, 'Imi place aceasta postare! :)', '2021-12-13 15:34:59', 0),
(7, 13, 13, 'Nice', '2021-12-16 17:25:02', 1),
(8, 13, 17, 'Nu-i rau.', '2021-12-13 16:46:24', 0),
(9, 14, 1, 'Asteptam clienti.', '2021-12-14 10:07:24', 1),
(10, 14, 1, 'Comentariu de test!', '2021-12-14 10:07:34', 0),
(11, 14, 19, 'Salut', '2021-12-24 15:39:02', 1),
(12, 14, 19, 'asdasda', '2021-12-24 15:54:21', 1),
(13, 12, 22, 'test comentariu', '2022-01-12 09:25:38', 0),
(14, 18, 23, 'Frumoasa postare', '2022-01-12 16:41:46', 1),
(15, 18, 23, 'frumoasa postare', '2022-01-12 16:41:54', 1),
(16, 12, 23, 'tare', '2022-01-13 09:58:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gradmedici`
--

CREATE TABLE `gradmedici` (
  `id` int(11) NOT NULL,
  `grad` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gradmedici`
--

INSERT INTO `gradmedici` (`id`, `grad`) VALUES
(1, 'Medic generalist'),
(2, 'Medic rezident'),
(3, 'Medic specialist'),
(4, 'Medic primar');

-- --------------------------------------------------------

--
-- Table structure for table `judete`
--

CREATE TABLE `judete` (
  `id` int(11) NOT NULL,
  `denumire` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `loguri`
--

CREATE TABLE `loguri` (
  `id` int(11) NOT NULL,
  `idAdministrator` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `actiune` varchar(200) NOT NULL,
  `data_ora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `medici`
--

CREATE TABLE `medici` (
  `id` int(11) NOT NULL,
  `idCategorie` int(11) NOT NULL,
  `numeMedic` varchar(50) NOT NULL,
  `prenumeMedic` varchar(100) NOT NULL,
  `CNP` varchar(13) DEFAULT NULL,
  `idGrad` int(11) NOT NULL,
  `idStatusAngajat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mesaje`
--

CREATE TABLE `mesaje` (
  `id` int(11) NOT NULL,
  `prenume` varchar(60) NOT NULL,
  `nume` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tara` varchar(20) NOT NULL,
  `mesaj` varchar(1000) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mesaje`
--

INSERT INTO `mesaje` (`id`, `prenume`, `nume`, `email`, `tara`, `mesaj`, `data`) VALUES
(30, 'Test', 'test', 'test@test.com', 'Romania', 'mesaj de test', '2022-01-12 17:24:10'),
(31, 'PrezentareProiect', 'Proiect', 'proiect@test.com', 'Romania', 'Prezentare proiect', '2022-01-13 09:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`) VALUES
(4, 'test@test.com'),
(5, 't2estt@gmail.com'),
(6, 'dasdasd@deasd.com'),
(7, 'sadasda@dasda.com'),
(8, 'test@testtt.com'),
(9, 'test2@testtt.com'),
(10, 'test3@test.com'),
(11, 'test4@test.com'),
(12, 'testttttt@gmail.com'),
(13, 'test12312@test.com'),
(14, 'asdasdasd@gmail.com'),
(15, 'test@test'),
(16, 'test981@gmail.com'),
(17, 'etetst@gmail.com'),
(18, 'testproiect@test.com');

-- --------------------------------------------------------

--
-- Table structure for table `postari`
--

CREATE TABLE `postari` (
  `id` int(11) NOT NULL,
  `idAutor` int(11) NOT NULL,
  `titlu` varchar(50) NOT NULL,
  `imagine` varchar(255) NOT NULL,
  `continut` mediumtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `idCateg` int(11) NOT NULL,
  `preview` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `programari`
--

CREATE TABLE `programari` (
  `id` int(11) NOT NULL,
  `idServiciu` int(11) NOT NULL,
  `idMedic` int(11) NOT NULL,
  `data` date NOT NULL,
  `idUtilizator` int(11) NOT NULL,
  `detaliiSuplimentare` varchar(1000) NOT NULL,
  `idStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `servicii`
--

CREATE TABLE `servicii` (
  `id` int(11) NOT NULL,
  `idCategorie` int(11) NOT NULL,
  `denumire` varchar(50) NOT NULL,
  `pret` decimal(8,2) NOT NULL,
  `detalii` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `servicii`
--

INSERT INTO `servicii` (`id`, `idCategorie`, `denumire`, `pret`, `detalii`) VALUES
(1, 1, 'Implant dentar', '300.00', 'Acesta este un implant dentar'),
(2, 2, 'ADN proviral HTLV I + HTLV II', '1067.00', 'HTLV (Human T-cell Lymphotropic Viruses) este un oncovirus ARN care face parte din familia retrovirusurilor. Se utilizează pentru a detecta infecţiile cu HTLV, pentru a ajuta la diagnosticarea leucemiilor cu celule T- adulte, a limfoamelor sau a HTLV – asociată mielopatiilor. Două tipuri de HTLV sunt cel mai frecvent testate: HTLV 1 şi HTLV 2, care au tropism PENTRU limfocitele CD4. Se estimează că aproximativ 15 -20 milioane de oameni sunt infectaţi cu HTLV. '),
(3, 1, 'Plomba', '100.00', 'Aceasta este o plomba. Inainte de a realiza plomba, maseaua trebuie tratata.'),
(4, 2, 'RMN', '150.00', 'Acesta este un RMN.'),
(5, 11, 'Consultatie Epileptologie', '300.00', 'Epilepsia este o boala neurologica, care consta in perturbari periodice ale activitatii electrice cerebrale. Astfel, activitatea neuronala normala este tulburata, rezultand diferite experiente senzoriale sau comportamente ciudate, ce se pot asocia uneor convulsii, spasme musculare si pierderea starii de constienta.\r\n\r\nEpilepsia este o boala ce poate fi cauzata de mai multi factori, totusi aparitia unei singure crize nu presupune neaparat diagnostic de epilepsie. Este esential sa consultati un medic specialist, ce poate determina sursa crizei si poate pune un diagnostic corect.\r\nIn cadrul clinicii Neuroaxis, echipa coordonata de doamna Dr. Ioana Mindruta, este pregatita sa va ajute in identificarea si tratarea afectiunilor epileptice.'),
(8, 3, 'Consult rutina', '100.00', 'Acesta este un consult de rutina.'),
(9, 3, 'Control de rutina', '150.00', 'Acesta esta un control de rutina');

-- --------------------------------------------------------

--
-- Table structure for table `setarisite`
--

CREATE TABLE `setarisite` (
  `id` int(11) NOT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `telefon` varchar(13) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `descriere` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `setarisite`
--

INSERT INTO `setarisite` (`id`, `linkedin`, `facebook`, `youtube`, `instagram`, `telefon`, `email`, `descriere`) VALUES
(1, 'https://www.linkedin.com/in/alexandru-r%C4%83ducu-361a09161/', 'https://www.facebook.com/', 'https://www.google.com/', 'https://www.instagram.com/', '+40726509341', 'raducu.alexandru.florian@gmail.com', 'Site realizat de Răducu Alexandru-Florian în cadrul proiectului pentru disciplina Programare WEB Avansată.');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `denumire_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `denumire_status`) VALUES
(1, 'activ'),
(2, 'anulat'),
(3, 'efectuat');

-- --------------------------------------------------------

--
-- Table structure for table `statusangajat`
--

CREATE TABLE `statusangajat` (
  `id` int(11) NOT NULL,
  `status_angajat` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `statusangajat`
--

INSERT INTO `statusangajat` (`id`, `status_angajat`) VALUES
(1, 'Activ'),
(2, 'Concediu'),
(3, 'Demisie'),
(4, 'Concediat'),
(5, 'Pensionar');

-- --------------------------------------------------------

--
-- Table structure for table `utlizatori`
--

CREATE TABLE `utlizatori` (
  `id` int(11) NOT NULL,
  `nume_utilizator` varchar(20) NOT NULL,
  `parola` varchar(255) NOT NULL,
  `nume` varchar(50) NOT NULL,
  `prenume` varchar(100) NOT NULL,
  `data_nasterii` date NOT NULL,
  `tara` varchar(50) NOT NULL,
  `nivel_administrare` int(1) NOT NULL,
  `poza_profil` mediumblob NOT NULL,
  `tip_poza` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aprecieripostari`
--
ALTER TABLE `aprecieripostari`
  ADD PRIMARY KEY (`idApreciere`),
  ADD KEY `idPostare` (`idPostare`),
  ADD KEY `idUtilizator` (`idUtilizator`) USING BTREE;

--
-- Indexes for table `categoriiconsultatii`
--
ALTER TABLE `categoriiconsultatii`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categoriipostari`
--
ALTER TABLE `categoriipostari`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cazuricovid`
--
ALTER TABLE `cazuricovid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comentariipostari`
--
ALTER TABLE `comentariipostari`
  ADD PRIMARY KEY (`idComentariu`);

--
-- Indexes for table `gradmedici`
--
ALTER TABLE `gradmedici`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `judete`
--
ALTER TABLE `judete`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loguri`
--
ALTER TABLE `loguri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idAdministrator` (`idAdministrator`);

--
-- Indexes for table `medici`
--
ALTER TABLE `medici`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCategorie` (`idCategorie`),
  ADD KEY `idGrad` (`idGrad`),
  ADD KEY `idStatusAngajat` (`idStatusAngajat`);

--
-- Indexes for table `mesaje`
--
ALTER TABLE `mesaje`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postari`
--
ALTER TABLE `postari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idAutor` (`idAutor`) USING BTREE,
  ADD KEY `idCateg` (`idCateg`) USING BTREE;

--
-- Indexes for table `programari`
--
ALTER TABLE `programari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idServiciu` (`idServiciu`),
  ADD KEY `idMedic` (`idMedic`),
  ADD KEY `idUtilizator` (`idUtilizator`),
  ADD KEY `idStatus` (`idStatus`);

--
-- Indexes for table `servicii`
--
ALTER TABLE `servicii`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCategorie` (`idCategorie`);

--
-- Indexes for table `setarisite`
--
ALTER TABLE `setarisite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statusangajat`
--
ALTER TABLE `statusangajat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utlizatori`
--
ALTER TABLE `utlizatori`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aprecieripostari`
--
ALTER TABLE `aprecieripostari`
  MODIFY `idApreciere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categoriiconsultatii`
--
ALTER TABLE `categoriiconsultatii`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categoriipostari`
--
ALTER TABLE `categoriipostari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cazuricovid`
--
ALTER TABLE `cazuricovid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comentariipostari`
--
ALTER TABLE `comentariipostari`
  MODIFY `idComentariu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gradmedici`
--
ALTER TABLE `gradmedici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `judete`
--
ALTER TABLE `judete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loguri`
--
ALTER TABLE `loguri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `medici`
--
ALTER TABLE `medici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mesaje`
--
ALTER TABLE `mesaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `postari`
--
ALTER TABLE `postari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `programari`
--
ALTER TABLE `programari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `servicii`
--
ALTER TABLE `servicii`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `setarisite`
--
ALTER TABLE `setarisite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `statusangajat`
--
ALTER TABLE `statusangajat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `utlizatori`
--
ALTER TABLE `utlizatori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aprecieripostari`
--
ALTER TABLE `aprecieripostari`
  ADD CONSTRAINT `aprecieripostari_ibfk_1` FOREIGN KEY (`idPostare`) REFERENCES `postari` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aprecieripostari_ibfk_2` FOREIGN KEY (`idUtilizator`) REFERENCES `utlizatori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `loguri`
--
ALTER TABLE `loguri`
  ADD CONSTRAINT `loguri_ibfk_1` FOREIGN KEY (`idAdministrator`) REFERENCES `utlizatori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medici`
--
ALTER TABLE `medici`
  ADD CONSTRAINT `medici_ibfk_1` FOREIGN KEY (`idCategorie`) REFERENCES `categoriiconsultatii` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medici_ibfk_2` FOREIGN KEY (`idGrad`) REFERENCES `gradmedici` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `postari`
--
ALTER TABLE `postari`
  ADD CONSTRAINT `postari_ibfk_1` FOREIGN KEY (`idAutor`) REFERENCES `utlizatori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postari_ibfk_2` FOREIGN KEY (`idCateg`) REFERENCES `categoriipostari` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `programari`
--
ALTER TABLE `programari`
  ADD CONSTRAINT `programari_ibfk_1` FOREIGN KEY (`idServiciu`) REFERENCES `servicii` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `programari_ibfk_2` FOREIGN KEY (`idUtilizator`) REFERENCES `utlizatori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `programari_ibfk_3` FOREIGN KEY (`idMedic`) REFERENCES `medici` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `programari_ibfk_4` FOREIGN KEY (`idStatus`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `servicii`
--
ALTER TABLE `servicii`
  ADD CONSTRAINT `servicii_ibfk_1` FOREIGN KEY (`idCategorie`) REFERENCES `categoriiconsultatii` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
