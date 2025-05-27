-- favoriler tablosu oluşturma
CREATE TABLE IF NOT EXISTS `favoriler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `eklenme_tarihi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kullanici_film_unique` (`kullanici_id`, `film_id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `film_id` (`film_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Foreign key kısıtlamaları
ALTER TABLE `favoriler`
  ADD CONSTRAINT `favoriler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favoriler_ibfk_2` FOREIGN KEY (`film_id`) REFERENCES `filmler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Örnek veri (gerekirse kullanın, test için)
-- INSERT INTO `favoriler` (`kullanici_id`, `film_id`) VALUES
-- (1, 3),
-- (1, 5),
-- (2, 1),
-- (2, 4),
-- (3, 2);film_arsivifilm_arsivifilm_arsivi