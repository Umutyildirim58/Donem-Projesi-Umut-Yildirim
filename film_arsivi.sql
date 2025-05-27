-- --------------------------------------------------------
-- Sunucu:                       127.0.0.1
-- Sunucu sürümü:                8.0.30 - MySQL Community Server - GPL
-- Sunucu İşletim Sistemi:       Win64
-- HeidiSQL Sürüm:               12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- film_arsivi için veritabanı yapısı dökülüyor
CREATE DATABASE IF NOT EXISTS `film_arsivi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `film_arsivi`;

-- tablo yapısı dökülüyor film_arsivi.filmler
CREATE TABLE IF NOT EXISTS `filmler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tur` varchar(255) DEFAULT NULL,
  `yil` int DEFAULT NULL,
  `yonetmen` varchar(64) DEFAULT NULL,
  `aciklama` text,
  `resim` varchar(255) DEFAULT 'default.jpg',
  `imdb` decimal(3,1) DEFAULT '0.0',
  `eklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- film_arsivi.filmler: ~10 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `filmler` (`id`, `ad`, `tur`, `yil`, `yonetmen`, `aciklama`, `resim`, `imdb`, `eklenme_tarihi`) VALUES
	(101, 'The Shawshank Redemption (Esaretin Bedeli)', 'Drama', 1994, 'Frank Darabont', 'Uçsuz yere hapse giren Andy Dufresne\'in Shawshank Cezaevi\'nde arkadaşlık ve özgürlük arayışını konu alır. Umut ve dostluk bu film, sinema dünyasında bir yapım olarak kabul edilir.', 'shawshank.jpg', 9.3, '2024-12-05 21:16:18'),
	(102, 'The Godfather', 'Crime,Drama', 1972, 'Francis Ford Coppola', 'Corleone bölgesinin lideri Vito Corleone\'in, ailesini korumak ve mafya imparatorluğunu sürdürmek için mücadeleyi anlatan bir başyapıttır.', 'godfather.jpg', 9.2, '2024-12-05 21:22:54'),
	(107, 'Ezel', 'İntikam', 2019, 'Uluç Bayraktar', 'İntikam ve ihanet temalarını işleyen, başrolde Kenan İmirzalioğlu\'nun yer aldığı 2009 yapımı bir Türk dizisidir. Ezel, geçmişteki ihanetlerin intikamını almak için geri döner.', 'ezel.jpg', 8.5, '2024-12-07 23:10:54'),
	(109, 'Kara Şövalye', 'Aksiyon,suç', 2008, 'Christopher Nolan', 'Gotham City\'yi çalışan Batman, Joker\'in kaotik planlarıyla mücadele ediyor. Heath Ledger\'ın Joker rolündeki muhteşem performansı filmi zirveye taşıyor.', 'dark_knight.jpg', 9.0, '2024-12-08 22:47:46'),
	(110, 'Forrest Gump', 'Dram, Komedi', 1994, 'Robert Zemeckis', 'Forrest Gump, zekâsı düşük ama büyük kalbi olan bir adamın, Amerika tarihindeki önemli olaylarla iç içe geçen yolculuğunu anlatıyor. Tom Hanks\'in muazzam performansıyla dikkat çekiyor.', 'forrest_gump.jpg', 8.8, '2024-12-08 22:49:35'),
	(111, 'Başlangıç', 'Aksiyon, Bilim Kurgu', 2010, 'Christopher Nolan', 'Bir grup hırsız, insanların rüyalarına girip fikir çalmaktadır. Nolan\'ın zekice yazdığı senaryo ve görsel efektlerle öne çıkan bu film, zihinsel oyunlar yapıyor.', 'inception.jpg', 8.8, '2024-12-08 22:50:25'),
	(112, 'Kurtlar Vadisi Pusu', 'Aksiyon, Dram, Suç', 2007, 'Sinan Çetin', 'Savaş dünyasındaki güç savaşlarını ve siyasetle iç içe geçmiş suç dünyasını anlatır. Baş karakter Polat Alemdar\'ın mücadelesi üzerinden, ihanet, dostluk ve vatan sevgisi gibi temalar işlenir.', 'kurtlar_vadisi.jpg', 7.8, '2024-12-08 22:52:30'),
	(113, 'Schindler\'in Listesi', 'Drama, Tarih', 1993, 'Steven Spielberg', 'Nazi Almanyası\'nda, Oskar Schindler, 1.200\'den fazla Yahudi\'yi ölümden kurtararak tarihe geçer. Spielberg\'in yönettiği bu film, insanlığın dramını etkileyici bir şekilde anlatır.', 'schindlers_list.jpg', 9.0, '2024-12-08 22:53:18'),
	(114, 'Yıldızlararası', 'Bilim Kurgu, Drama', 2014, 'Christopher Nolan', 'İnsanlığın hayatta kalabilmesi için uzaya seyahat eden bir grup astronotun, zaman ve uzayla ilgili zorlayan bir yolculuk yer alır. Görselliği ve bilimsel temalarıyla dikkat çeker.', 'interstellar.jpg', 8.7, '2024-12-08 22:54:10'),
	(115, 'Dövüş Kulübü', 'Drama, Psikolojik', 1999, 'David Fincher', 'Bir adamın gündelik hayatı, bir dövüş kulübü tarafından alt üst olur. Film, modern toplum ve kimlik sorgulamaları üzerine bir inceleme sunar.', 'fight_club.jpg', 8.8, '2024-12-08 22:54:57');

-- tablo yapısı dökülüyor film_arsivi.yonetmenler
CREATE TABLE IF NOT EXISTS `yonetmenler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad` varchar(100) NOT NULL,
  `dogum_yili` int DEFAULT NULL,
  `unlu_film` varchar(255) DEFAULT NULL,
  `eklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- film_arsivi.yonetmenler: ~3 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `yonetmenler` (`id`, `ad`, `dogum_yili`, `unlu_film`, `eklenme_tarihi`) VALUES
	(1, 'Christopher Nolan', 1970, 'Inception', '2024-12-10 15:30:22'),
	(2, 'Quentin Tarantino', 1963, 'Pulp Fiction', '2024-12-10 15:30:22'),
	(3, 'Hayao Miyazaki', 1941, 'Spirited Away', '2024-12-10 15:30:22');

-- tablo yapısı dökülüyor film_arsivi.kullanicilar
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `adi_soyadi` varchar(100) DEFAULT NULL,
  `rol` enum('kullanici','admin') DEFAULT 'kullanici',
  `durum` tinyint(1) DEFAULT '1',
  `kayit_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  `son_giris` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- film_arsivi.kullanicilar: ~2 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `email`, `sifre`, `adi_soyadi`, `rol`, `durum`, `kayit_tarihi`, `son_giris`) VALUES
	(1, 'admin', 'admin@filmarsivi.com', '$2y$10$uXikGKOClzIElTpjXcwiXe8QUwJRFRzVpERKKCNTFsOZsOQfXziDe', 'Site Yöneticisi', 'admin', 1, '2024-12-10 15:45:02', '2024-12-10 15:45:02'),
	(2, 'kullanici', 'kullanici@filmarsivi.com', '$2y$10$8W7cPnxUb3VQJ7JnSQIRouk2L9S/ioNUlyJ1MW2YZ9euPSV7y0Sme', 'Örnek Kullanıcı', 'kullanici', 1, '2024-12-10 15:45:02', '2024-12-10 15:45:02');

-- tablo yapısı dökülüyor film_arsivi.favoriler
CREATE TABLE IF NOT EXISTS `favoriler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `film_id` int NOT NULL,
  `eklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_favoriler_kullanici` (`kullanici_id`),
  KEY `fk_favoriler_film` (`film_id`),
  CONSTRAINT `fk_favoriler_film` FOREIGN KEY (`film_id`) REFERENCES `filmler` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favoriler_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- film_arsivi.favoriler: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor film_arsivi.yorumlar
CREATE TABLE IF NOT EXISTS `yorumlar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `film_id` int NOT NULL,
  `yorum` text NOT NULL,
  `puan` int DEFAULT NULL,
  `eklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_yorumlar_kullanici` (`kullanici_id`),
  KEY `fk_yorumlar_film` (`film_id`),
  CONSTRAINT `fk_yorumlar_film` FOREIGN KEY (`film_id`) REFERENCES `filmler` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_yorumlar_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- film_arsivi.yorumlar: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;