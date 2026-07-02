SET NAMES utf8mb4;
SET time_zone = '+03:00';
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE restaurant_reviews;
TRUNCATE TABLE reservations;
TRUNCATE TABLE restaurant_partner_applications;
TRUNCATE TABLE menu_items;
TRUNCATE TABLE menu_categories;
TRUNCATE TABLE restaurant_open_hours;
TRUNCATE TABLE tables;
TRUNCATE TABLE restaurants;
TRUNCATE TABLE addresses;
TRUNCATE TABLE neighborhoods;
TRUNCATE TABLE districts;
TRUNCATE TABLE cities;
TRUNCATE TABLE countries;
TRUNCATE TABLE system_logs;
TRUNCATE TABLE users;
TRUNCATE TABLE cuisine_types;
TRUNCATE TABLE day_of_weeks;
TRUNCATE TABLE partner_application_statuses;
TRUNCATE TABLE reservation_statuses;
TRUNCATE TABLE restaurant_statuses;
TRUNCATE TABLE role_types;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO countries (country_id, iso_code, name) VALUES
(1, 'TR', 'Türkiye');

INSERT INTO cities (city_id, country_id, name, plate_code) VALUES
(1, 1, 'İstanbul', 34),
(2, 1, 'Ankara', 6),
(3, 1, 'İzmir', 35),
(4, 1, 'Bursa', 16),
(5, 1, 'Antalya', 7);

INSERT INTO districts (district_id, city_id, name) VALUES
(1, 1, 'Kadıköy'),
(2, 1, 'Beşiktaş'),
(3, 1, 'Beyoğlu'),
(4, 1, 'Şişli'),
(5, 2, 'Çankaya'),
(6, 2, 'Yenimahalle'),
(7, 3, 'Konak'),
(8, 3, 'Karşıyaka'),
(9, 4, 'Osmangazi'),
(10, 4, 'Nilüfer'),
(11, 5, 'Muratpaşa'),
(12, 5, 'Konyaaltı');

INSERT INTO neighborhoods (neighborhood_id, district_id, name) VALUES
(1, 1, 'Moda'),
(2, 1, 'Caferağa'),
(3, 2, 'Ortaköy'),
(4, 2, 'Levent'),
(5, 3, 'Karaköy'),
(6, 3, 'Cihangir'),
(7, 4, 'Nişantaşı'),
(8, 4, 'Mecidiyeköy'),
(9, 5, 'Kavaklıdere'),
(10, 5, 'Bahçelievler'),
(11, 6, 'Batıkent'),
(12, 6, 'Demetevler'),
(13, 7, 'Alsancak'),
(14, 7, 'Güzelyalı'),
(15, 8, 'Bostanlı'),
(16, 8, 'Mavişehir'),
(17, 9, 'Heykel'),
(18, 9, 'Çekirge'),
(19, 10, 'Balat'),
(20, 10, 'FSM'),
(21, 11, 'Lara'),
(22, 11, 'Kaleiçi'),
(23, 12, 'Liman'),
(24, 12, 'Hurma');

INSERT INTO role_types (role_type_id, code, label) VALUES
(1, 'admin', 'Yönetici'),
(2, 'host', 'İşletmeci'),
(3, 'user', 'Kullanıcı');

INSERT INTO restaurant_statuses (restaurant_status_id, code, label) VALUES
(1, 'pending', 'Onay Bekliyor'),
(2, 'approved', 'Onaylandı'),
(3, 'rejected', 'Reddedildi'),
(4, 'suspended', 'Askıya Alındı');

INSERT INTO reservation_statuses (reservation_status_id, code, label) VALUES
(1, 'pending', 'Beklemede'),
(2, 'confirmed', 'Onaylandı'),
(3, 'checked_in', 'Giriş Yapıldı'),
(4, 'completed', 'Tamamlandı'),
(5, 'cancelled', 'İptal'),
(6, 'no_show', 'Gelmedi');

INSERT INTO partner_application_statuses (partner_application_status_id, code, label) VALUES
(1, 'pending', 'Beklemede'),
(2, 'approved', 'Onaylandı'),
(3, 'rejected', 'Reddedildi');

INSERT INTO cuisine_types (cuisine_type_id, name) VALUES
(1, 'Türk Mutfağı'),
(2, 'Akdeniz'),
(3, 'İtalyan'),
(4, 'Japon'),
(5, 'Deniz Ürünleri'),
(6, 'Anadolu'),
(7, 'Brunch'),
(8, 'Vegan'),
(9, 'Asya'),
(10, 'Steakhouse'),
(11, 'Dünya Mutfağı'),
(12, 'Tatlı & Kahve');

INSERT INTO day_of_weeks (day_of_week_id, code, label) VALUES
(1, 'MON', 'Pazartesi'),
(2, 'TUE', 'Salı'),
(3, 'WED', 'Çarşamba'),
(4, 'THU', 'Perşembe'),
(5, 'FRI', 'Cuma'),
(6, 'SAT', 'Cumartesi'),
(7, 'SUN', 'Pazar');

INSERT INTO users (id, email, full_name, password_hash, role, created_date) VALUES
(1, 'admin@reserve.local', 'Reserve Admin', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'admin', NOW()),
(2, 'user@reserve.local', 'Demo Kullanıcı', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'user', NOW()),
(3, 'host01@reserve.local', 'Host 01', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(4, 'host02@reserve.local', 'Host 02', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(5, 'host03@reserve.local', 'Host 03', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(6, 'host04@reserve.local', 'Host 04', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(7, 'host05@reserve.local', 'Host 05', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(8, 'host06@reserve.local', 'Host 06', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(9, 'host07@reserve.local', 'Host 07', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(10, 'host08@reserve.local', 'Host 08', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(11, 'host09@reserve.local', 'Host 09', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(12, 'host10@reserve.local', 'Host 10', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(13, 'host11@reserve.local', 'Host 11', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(14, 'host12@reserve.local', 'Host 12', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(15, 'host13@reserve.local', 'Host 13', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(16, 'host14@reserve.local', 'Host 14', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(17, 'host15@reserve.local', 'Host 15', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(18, 'host16@reserve.local', 'Host 16', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(19, 'host17@reserve.local', 'Host 17', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(20, 'host18@reserve.local', 'Host 18', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(21, 'host19@reserve.local', 'Host 19', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(22, 'host20@reserve.local', 'Host 20', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(23, 'host21@reserve.local', 'Host 21', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(24, 'host22@reserve.local', 'Host 22', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(25, 'host23@reserve.local', 'Host 23', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(26, 'host24@reserve.local', 'Host 24', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(27, 'host25@reserve.local', 'Host 25', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(28, 'host26@reserve.local', 'Host 26', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(29, 'host27@reserve.local', 'Host 27', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(30, 'host28@reserve.local', 'Host 28', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(31, 'host29@reserve.local', 'Host 29', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW()),
(32, 'host30@reserve.local', 'Host 30', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'host', NOW());

INSERT INTO addresses (address_id, city_id, district_id, neighborhood_id, street_name, building_number, floor_number, unit_number, direction_note, postal_code, latitude, longitude) VALUES
(1,1,1,1,'Bahariye Caddesi','12','2','5','Moda iskelesine 200 metre', '34710',40.9870,29.0289),
(2,1,1,2,'Moda Caddesi','24','1','2','Sahil yürüyüş yoluna yakın', '34714',40.9842,29.0268),
(3,1,2,3,'Muallim Naci Caddesi','8','3','7','Ortaköy meydanı yanı', '34347',41.0470,29.0263),
(4,1,2,4,'Nispetiye Caddesi','51','4','9','Metro çıkışına 3 dk', '34337',41.0788,29.0148),
(5,1,3,5,'Kemankeş Caddesi','16','2','4','Karaköy tramvayına yakın', '34425',41.0236,28.9755),
(6,1,3,6,'Sıraselviler Caddesi','73','5','11','Cihangir parkı karşısı', '34433',41.0331,28.9862),
(7,1,4,7,'Teşvikiye Caddesi','41','3','6','Vali Konağı kesişimi', '34365',41.0482,28.9900),
(8,1,4,8,'Büyükdere Caddesi','102','6','15','Mecidiyeköy metrobüs üstü', '34394',41.0677,28.9945),
(9,2,5,9,'Tunalı Hilmi Caddesi','58','2','3','Kuğulu Park 5 dk', '06680',39.9109,32.8603),
(10,2,5,10,'Aşkabat Caddesi','19','1','1','7. Cadde köşesi', '06490',39.9224,32.8238),
(11,2,6,11,'İstanbul Yolu','88','4','12','Batıkent metroya yakın', '06370',39.9789,32.7151),
(12,2,6,12,'Vatan Caddesi','27','2','8','Demetevler pazarı yanı', '06200',39.9593,32.7855),
(13,3,7,13,'Kıbrıs Şehitleri Caddesi','35','1','4','Alsancak vapur iskelesi', '35220',38.4358,27.1428),
(14,3,7,14,'Mithatpaşa Caddesi','214','3','10','Sahil şeridine yakın', '35280',38.4013,27.0842),
(15,3,8,15,'Cemal Gürsel Caddesi','66','2','7','Bostanlı pazar yerine 4 dk', '35550',38.4566,27.1090),
(16,3,8,16,'Cahar Dudayev Bulvarı','44','5','14','Mavişehir sahile yakın', '35590',38.4675,27.0933),
(17,4,9,17,'Atatürk Caddesi','23','2','6','Tarihi çarşı girişinde', '16010',40.1836,29.0610),
(18,4,9,18,'Çekirge Caddesi','72','3','9','Kaplıcalar bölgesi', '16070',40.1975,29.0347),
(19,4,10,19,'Balat Caddesi','14','1','2','Ana bulvar cephe', '16140',40.2251,28.9674),
(20,4,10,20,'FSM Bulvarı','95','4','13','AVM karşısı', '16120',40.2124,28.9920),
(21,5,11,21,'Lara Caddesi','53','2','5','Sahil parkına 300 metre', '07160',36.8514,30.7718),
(22,5,11,22,'Hesapçı Sokak','11','1','1','Kaleiçi saat kulesi yanı', '07100',36.8842,30.7057),
(23,5,12,23,'Akdeniz Bulvarı','120','3','8','Konyaaltı sahil bandı', '07070',36.8670,30.6377),
(24,5,12,24,'Boğaçayı Caddesi','9','2','3','Hurma pazarına yakın', '07130',36.8520,30.6273),
(25,1,1,1,'Kadife Sokak','17','1','6','Barlar sokağı girişi', '34710',40.9898,29.0302),
(26,2,5,9,'Bestekar Sokak','28','2','4','Konsolosluklar bölgesi', '06680',39.9152,32.8582),
(27,3,7,13,'Şair Eşref Bulvarı','102','5','16','Kordon yürüyüş yoluna yakın', '35220',38.4296,27.1379),
(28,4,10,20,'İzmir Yolu Caddesi','67','3','10','FSM kavşağı', '16120',40.2144,28.9872),
(29,5,12,23,'Arapsuyu Caddesi','31','1','2','Sahilden bir paralel içerde', '07070',36.8591,30.6395),
(30,1,2,4,'Ebulula Mardin Caddesi','6','4','12','Levent iş merkezleri', '34337',41.0798,29.0114);

UPDATE addresses
SET direction_city_id = city_id,
    direction_district_id = district_id,
    direction_neighborhood_id = neighborhood_id
WHERE direction_note IS NOT NULL AND direction_note <> '';

INSERT INTO restaurants (id, name, description, cuisine_type, city, district, neighborhood, street, avenue, building_number, floor_number, apartment_number, door_number, postal_code, address_notes, address, phone, price_range, cover_image, opening_time, closing_time, reservation_duration_minutes, owner_email, status, is_featured, rating, total_reservations, address_id) VALUES
(1,'Mavi Teras','Deniz esintili modern Akdeniz mutfağı ve sakin teras deneyimi.','Akdeniz','İstanbul','Kadıköy','Moda','Bahariye Caddesi','Moda Yolu','12','2','5','5','34710','İskeleye yakın','Bahariye Caddesi No:12 Kadıköy/İstanbul','+90 216 100 00 01','$$$','https://picsum.photos/seed/reserve-r01/1200/800','10:00:00','23:30:00',90,'host01@reserve.local','approved',1,4.80,230,1),
(2,'Han Kahvaltı Evi','Gün boyu serpme kahvaltı, fırın ürünleri ve özel demleme kahveler.','Brunch','İstanbul','Kadıköy','Caferağa','Moda Caddesi','Rıhtım Sokak','24','1','2','2','34714','Sahil hattı','Moda Caddesi No:24 Kadıköy/İstanbul','+90 216 100 00 02','$$','https://picsum.photos/seed/reserve-r02/1200/800','08:30:00','19:00:00',75,'host02@reserve.local','approved',1,4.55,180,2),
(3,'Ortaköy Izgara','Et ve ızgara odaklı menü, boğaz manzaralı akşam servisi.','Steakhouse','İstanbul','Beşiktaş','Ortaköy','Muallim Naci Caddesi','Sahil Yolu','8','3','7','7','34347','Meydan yanı','Muallim Naci Caddesi No:8 Beşiktaş/İstanbul','+90 212 100 00 03','$$$$','https://picsum.photos/seed/reserve-r03/1200/800','12:00:00','23:45:00',90,'host03@reserve.local','approved',0,4.72,142,3),
(4,'Levent Urban Kitchen','Dünya mutfağı tabakları ve iş çıkışı için hızlı rezervasyon deneyimi.','Dünya Mutfağı','İstanbul','Beşiktaş','Levent','Nispetiye Caddesi','Büyükdere Yolu','51','4','9','9','34337','Metroya yakın','Nispetiye Caddesi No:51 Beşiktaş/İstanbul','+90 212 100 00 04','$$$','https://picsum.photos/seed/reserve-r04/1200/800','11:00:00','23:00:00',90,'host04@reserve.local','approved',0,4.42,97,4),
(5,'Karaköy Meze','Paylaşımlık meze, deniz ürünleri ve uzun akşam sofraları.','Deniz Ürünleri','İstanbul','Beyoğlu','Karaköy','Kemankeş Caddesi','Bankalar Sokak','16','2','4','4','34425','Galata köprüsüne yakın','Kemankeş Caddesi No:16 Beyoğlu/İstanbul','+90 212 100 00 05','$$$','https://picsum.photos/seed/reserve-r05/1200/800','12:00:00','00:00:00',90,'host05@reserve.local','approved',1,4.67,201,5),
(6,'Cihangir Bowl','Vegan ve sağlıklı kaseler, bitkisel mutfak odaklı modern sunumlar.','Vegan','İstanbul','Beyoğlu','Cihangir','Sıraselviler Caddesi','Cihangir Yokuşu','73','5','11','11','34433','Park karşısı','Sıraselviler Caddesi No:73 Beyoğlu/İstanbul','+90 212 100 00 06','$$','https://picsum.photos/seed/reserve-r06/1200/800','10:00:00','22:00:00',75,'host06@reserve.local','approved',0,4.35,88,6),
(7,'Nişantaşı Trattoria','Taze makarna, risotto ve klasik İtalyan reçeteleri.','İtalyan','İstanbul','Şişli','Nişantaşı','Teşvikiye Caddesi','Vali Konağı Caddesi','41','3','6','6','34365','Cadde üzerinde','Teşvikiye Caddesi No:41 Şişli/İstanbul','+90 212 100 00 07','$$$','https://picsum.photos/seed/reserve-r07/1200/800','11:30:00','23:30:00',90,'host07@reserve.local','approved',1,4.74,210,7),
(8,'Mecidiyeköy Express','Öğle yoğunluğuna uygun hızlı menü ve pratik masa yönetimi.','Türk Mutfağı','İstanbul','Şişli','Mecidiyeköy','Büyükdere Caddesi','Mecidiyeköy Yolu','102','6','15','15','34394','İş merkezleri bölgesi','Büyükdere Caddesi No:102 Şişli/İstanbul','+90 212 100 00 08','$$','https://picsum.photos/seed/reserve-r08/1200/800','09:00:00','21:30:00',60,'host08@reserve.local','approved',0,4.11,64,8),
(9,'Kavaklıdere Sofra','Anadolu reçeteleri modern sunumla, sakin ve kaliteli servis.','Anadolu','Ankara','Çankaya','Kavaklıdere','Tunalı Hilmi Caddesi','Kavaklıdere Sokak','58','2','3','3','06680','Kuğulu Park çevresi','Tunalı Hilmi Caddesi No:58 Çankaya/Ankara','+90 312 100 00 09','$$$','https://picsum.photos/seed/reserve-r09/1200/800','11:00:00','23:00:00',90,'host09@reserve.local','approved',1,4.62,172,9),
(10,'Bahçelievler Brunch','Hafta sonu yoğunluğuna uygun geniş kahvaltı ve tatlı menüsü.','Brunch','Ankara','Çankaya','Bahçelievler','Aşkabat Caddesi','7. Cadde','19','1','1','1','06490','Cadde üstü','Aşkabat Caddesi No:19 Çankaya/Ankara','+90 312 100 00 10','$$','https://picsum.photos/seed/reserve-r10/1200/800','08:00:00','18:30:00',75,'host10@reserve.local','approved',0,4.28,95,10),
(11,'Batıkent Asya','Wok, noodle ve paylaşımlık Uzak Doğu tabakları.','Asya','Ankara','Yenimahalle','Batıkent','İstanbul Yolu','Batıkent Bulvarı','88','4','12','12','06370','Metroya yakın','İstanbul Yolu No:88 Yenimahalle/Ankara','+90 312 100 00 11','$$','https://picsum.photos/seed/reserve-r11/1200/800','10:30:00','22:30:00',90,'host11@reserve.local','approved',0,4.39,106,11),
(12,'Demetevler Ocakbaşı','Kebap, ızgara ve geleneksel lezzetler için aile dostu salon.','Türk Mutfağı','Ankara','Yenimahalle','Demetevler','Vatan Caddesi','Demetevler Sokak','27','2','8','8','06200','Pazar alanı yakını','Vatan Caddesi No:27 Yenimahalle/Ankara','+90 312 100 00 12','$$$','https://picsum.photos/seed/reserve-r12/1200/800','11:30:00','23:30:00',90,'host12@reserve.local','approved',0,4.46,121,12),
(13,'Alsancak Marina','Deniz ürünleri ve Ege mezeleri, akşam servisinde canlı atmosfer.','Deniz Ürünleri','İzmir','Konak','Alsancak','Kıbrıs Şehitleri Caddesi','Liman Yolu','35','1','4','4','35220','Kordon hattı','Kıbrıs Şehitleri Caddesi No:35 Konak/İzmir','+90 232 100 00 13','$$$','https://picsum.photos/seed/reserve-r13/1200/800','12:00:00','23:45:00',90,'host13@reserve.local','approved',1,4.76,244,13),
(14,'Güzelyalı Fırın','Taş fırın pizzalar ve ev yapımı tatlılarla sıcak mahalle restoranı.','İtalyan','İzmir','Konak','Güzelyalı','Mithatpaşa Caddesi','Sahil Yolu','214','3','10','10','35280','Sahil manzarası','Mithatpaşa Caddesi No:214 Konak/İzmir','+90 232 100 00 14','$$','https://picsum.photos/seed/reserve-r14/1200/800','10:00:00','22:30:00',90,'host14@reserve.local','approved',0,4.40,99,14),
(15,'Bostanlı Kitchen','Akdeniz ve vegan seçenekleri bir arada sunan modern mutfak.','Akdeniz','İzmir','Karşıyaka','Bostanlı','Cemal Gürsel Caddesi','Bostanlı İç Yol','66','2','7','7','35550','Pazar yeri civarı','Cemal Gürsel Caddesi No:66 Karşıyaka/İzmir','+90 232 100 00 15','$$','https://picsum.photos/seed/reserve-r15/1200/800','10:00:00','22:00:00',75,'host15@reserve.local','approved',0,4.33,87,15),
(16,'Mavişehir Lounge','Geniş salon ve ferah menü ile aile rezervasyonlarına uygun.','Dünya Mutfağı','İzmir','Karşıyaka','Mavişehir','Cahar Dudayev Bulvarı','Mavişehir Yolu','44','5','14','14','35590','Site girişine yakın','Cahar Dudayev Bulvarı No:44 Karşıyaka/İzmir','+90 232 100 00 16','$$$','https://picsum.photos/seed/reserve-r16/1200/800','11:00:00','23:00:00',90,'host16@reserve.local','approved',0,4.51,133,16),
(17,'Heykel Konağı','Bursa klasiklerini modern sunumla birleştiren şehir restoranı.','Anadolu','Bursa','Osmangazi','Heykel','Atatürk Caddesi','Merkez Yolu','23','2','6','6','16010','Tarihi merkez','Atatürk Caddesi No:23 Osmangazi/Bursa','+90 224 100 00 17','$$$','https://picsum.photos/seed/reserve-r17/1200/800','11:00:00','23:30:00',90,'host17@reserve.local','approved',1,4.69,190,17),
(18,'Çekirge Bahçe','Bahçe oturumu ve geniş masa planı ile kalabalık gruplara uygun.','Türk Mutfağı','Bursa','Osmangazi','Çekirge','Çekirge Caddesi','Kaplıca Sokak','72','3','9','9','16070','Kaplıca bölgesi','Çekirge Caddesi No:72 Osmangazi/Bursa','+90 224 100 00 18','$$','https://picsum.photos/seed/reserve-r18/1200/800','10:00:00','22:30:00',90,'host18@reserve.local','approved',0,4.22,76,18),
(19,'Balat Gourmet','Şef dokunuşlu menüler ve seçili butik ürünlerle akşam servisi.','Dünya Mutfağı','Bursa','Nilüfer','Balat','Balat Caddesi','Balat Sokak','14','1','2','2','16140','Ana cadde önü','Balat Caddesi No:14 Nilüfer/Bursa','+90 224 100 00 19','$$$','https://picsum.photos/seed/reserve-r19/1200/800','12:00:00','23:00:00',90,'host19@reserve.local','approved',0,4.37,92,19),
(20,'FSM Buluşma','İş ve arkadaş buluşmaları için hızlı servisli şehir restoranı.','Türk Mutfağı','Bursa','Nilüfer','FSM','FSM Bulvarı','FSM İç Yol','95','4','13','13','16120','AVM karşısı','FSM Bulvarı No:95 Nilüfer/Bursa','+90 224 100 00 20','$$','https://picsum.photos/seed/reserve-r20/1200/800','10:30:00','22:30:00',75,'host20@reserve.local','approved',0,4.18,69,20),
(21,'Lara Seaside','Akdeniz mutfağı, açık hava masaları ve gün batımı servisi.','Akdeniz','Antalya','Muratpaşa','Lara','Lara Caddesi','Sahil Yolu','53','2','5','5','07160','Sahile 300 metre','Lara Caddesi No:53 Muratpaşa/Antalya','+90 242 100 00 21','$$$','https://picsum.photos/seed/reserve-r21/1200/800','11:00:00','23:30:00',90,'host21@reserve.local','approved',1,4.79,256,21),
(22,'Kaleiçi Ocak','Tarihi doku içinde kebap ve meze odaklı sıcak akşam servisi.','Anadolu','Antalya','Muratpaşa','Kaleiçi','Hesapçı Sokak','Kaleiçi Yolu','11','1','1','1','07100','Saat kulesi çevresi','Hesapçı Sokak No:11 Muratpaşa/Antalya','+90 242 100 00 22','$$','https://picsum.photos/seed/reserve-r22/1200/800','12:00:00','23:45:00',90,'host22@reserve.local','approved',0,4.44,111,22),
(23,'Konyaaltı Blue','Balık, salata ve ızgara seçenekleriyle sahil konsepti.','Deniz Ürünleri','Antalya','Konyaaltı','Liman','Akdeniz Bulvarı','Sahil Park','120','3','8','8','07070','Sahil bandı üstü','Akdeniz Bulvarı No:120 Konyaaltı/Antalya','+90 242 100 00 23','$$$','https://picsum.photos/seed/reserve-r23/1200/800','11:30:00','23:30:00',90,'host23@reserve.local','approved',1,4.70,215,23),
(24,'Hurma Brasserie','Kahvaltıdan akşam servisine uzanan modern mahalle mutfağı.','Brunch','Antalya','Konyaaltı','Hurma','Boğaçayı Caddesi','Hurma İç Yol','9','2','3','3','07130','Pazar yeri yanı','Boğaçayı Caddesi No:9 Konyaaltı/Antalya','+90 242 100 00 24','$$','https://picsum.photos/seed/reserve-r24/1200/800','08:30:00','21:30:00',75,'host24@reserve.local','approved',0,4.26,83,24),
(25,'Kadife Sokak Tatlı','Tatlı, kahve ve hafif atıştırmalık odaklı butik mekan.','Tatlı & Kahve','İstanbul','Kadıköy','Moda','Kadife Sokak','Moda Yan Yol','17','1','6','6','34710','Yaya trafiği yoğun','Kadife Sokak No:17 Kadıköy/İstanbul','+90 216 100 00 25','$$','https://picsum.photos/seed/reserve-r25/1200/800','09:00:00','22:30:00',60,'host25@reserve.local','approved',0,4.53,149,25),
(26,'Bestekar Sushi','Minimal dekor ve taze sushi seçenekleri ile premium deneyim.','Japon','Ankara','Çankaya','Kavaklıdere','Bestekar Sokak','Bestekar Yolu','28','2','4','4','06680','Konsolosluklara yakın','Bestekar Sokak No:28 Çankaya/Ankara','+90 312 100 00 26','$$$$','https://picsum.photos/seed/reserve-r26/1200/800','12:00:00','23:00:00',90,'host26@reserve.local','approved',0,4.65,134,26),
(27,'Kordon 35','Kordon atmosferinde modern Ege mutfağı ve uzun akşam servisleri.','Akdeniz','İzmir','Konak','Alsancak','Şair Eşref Bulvarı','Kordon Yolu','102','5','16','16','35220','Kordon hattı','Şair Eşref Bulvarı No:102 Konak/İzmir','+90 232 100 00 27','$$$','https://picsum.photos/seed/reserve-r27/1200/800','11:00:00','23:30:00',90,'host27@reserve.local','approved',1,4.73,227,27),
(28,'FSM Et Atölyesi','Kuru dinlendirilmiş et seçenekleri ve özel tadım menüsü.','Steakhouse','Bursa','Nilüfer','FSM','İzmir Yolu Caddesi','FSM Bağlantı','67','3','10','10','16120','Kavşak üstü','İzmir Yolu Caddesi No:67 Nilüfer/Bursa','+90 224 100 00 28','$$$$','https://picsum.photos/seed/reserve-r28/1200/800','12:00:00','23:45:00',90,'host28@reserve.local','approved',0,4.60,158,28),
(29,'Arapsuyu Vegan','Bitki bazlı menü, glutensiz seçenekler ve hafif akşam tabakları.','Vegan','Antalya','Konyaaltı','Liman','Arapsuyu Caddesi','Sahil Altı','31','1','2','2','07070','Sahile yakın ara sokak','Arapsuyu Caddesi No:31 Konyaaltı/Antalya','+90 242 100 00 29','$$','https://picsum.photos/seed/reserve-r29/1200/800','10:00:00','22:00:00',75,'host29@reserve.local','approved',0,4.31,74,29),
(30,'Levent Prestige','Kurumsal buluşmalar için şık salon ve rafine menü seçenekleri.','Dünya Mutfağı','İstanbul','Beşiktaş','Levent','Ebulula Mardin Caddesi','Levent Merkez','6','4','12','12','34337','Ofis kulelerine yakın','Ebulula Mardin Caddesi No:6 Beşiktaş/İstanbul','+90 212 100 00 30','$$$$','https://picsum.photos/seed/reserve-r30/1200/800','11:30:00','23:30:00',90,'host30@reserve.local','approved',1,4.77,240,30);

INSERT INTO tables (restaurant_id, table_number, capacity, location, is_active, description)
SELECT r.id,
       CONCAT('T', n.n),
       CASE n.n WHEN 1 THEN 2 WHEN 2 THEN 2 WHEN 3 THEN 4 WHEN 4 THEN 4 WHEN 5 THEN 6 ELSE 8 END,
       CASE WHEN n.n <= 2 THEN 'İç Salon' WHEN n.n <= 4 THEN 'Cam Kenarı' ELSE 'Teras' END,
       1,
       CASE WHEN n.n = 6 THEN 'Grup rezervasyonu için idealdir' ELSE 'Standart masa' END
FROM restaurants r
JOIN (
    SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) n;

INSERT INTO restaurant_open_hours (restaurant_id, day_of_week, opening_time, closing_time, reservation_duration_minutes)
SELECT r.id,
       d.day_of_week_id,
       CASE WHEN d.day_of_week_id IN (6,7) THEN '09:00:00' ELSE r.opening_time END,
       CASE WHEN d.day_of_week_id IN (5,6) THEN '23:59:00' ELSE r.closing_time END,
       r.reservation_duration_minutes
FROM restaurants r
JOIN day_of_weeks d;

INSERT INTO menu_categories (restaurant_id, name, display_order)
SELECT r.id, c.name, c.display_order
FROM restaurants r
JOIN (
    SELECT 'Başlangıçlar' AS name, 1 AS display_order
    UNION ALL SELECT 'Ana Yemekler', 2
    UNION ALL SELECT 'Tatlılar', 3
    UNION ALL SELECT 'İçecekler', 4
) c;

INSERT INTO menu_items (restaurant_id, category_id, name, description, price, image_url, is_active)
SELECT c.restaurant_id,
       c.id,
       t.item_name,
       t.item_description,
       ROUND(t.base_price + ((c.restaurant_id % 5) * t.price_step), 2),
       CONCAT('https://picsum.photos/seed/menu-', c.restaurant_id, '-', t.seed_key, '/900/600'),
       1
FROM menu_categories c
JOIN (
    SELECT 'Başlangıçlar' AS category_name, 'Günün Çorbası' AS item_name, 'Mevsim sebzeleriyle hazırlanır.' AS item_description, 85.00 AS base_price, 1.50 AS price_step, 'soup' AS seed_key
    UNION ALL SELECT 'Başlangıçlar', 'Mini Meze Tabağı', 'Paylaşımlık seçili meze çeşitleri.', 120.00, 2.00, 'meze'
    UNION ALL SELECT 'Başlangıçlar', 'Çıtır Patates', 'Özel baharat karışımı ile servis edilir.', 95.00, 1.25, 'fries'
    UNION ALL SELECT 'Ana Yemekler', 'Izgara Köfte', 'Odun kömürü ateşinde pişirilir.', 240.00, 3.00, 'kofte'
    UNION ALL SELECT 'Ana Yemekler', 'Tavuk Izgara', 'Marine edilmiş tavuk fileto.', 220.00, 2.80, 'chicken'
    UNION ALL SELECT 'Ana Yemekler', 'Şefin Makarna Tabağı', 'Günün sosu ile taze makarna.', 255.00, 3.20, 'pasta'
    UNION ALL SELECT 'Tatlılar', 'San Sebastian Cheesecake', 'Yoğun kıvam, hafif yanık doku.', 150.00, 2.20, 'cheesecake'
    UNION ALL SELECT 'Tatlılar', 'Fırın Sütlaç', 'Geleneksel usulde hazırlanır.', 110.00, 1.50, 'sutlac'
    UNION ALL SELECT 'Tatlılar', 'Çikolatalı Mousse', 'Bitter çikolata ve krema dengesi.', 145.00, 1.80, 'mousse'
    UNION ALL SELECT 'İçecekler', 'Türk Kahvesi', 'Közde, sade veya orta.', 55.00, 1.00, 'coffee'
    UNION ALL SELECT 'İçecekler', 'Limonata', 'Taze limon ve nane ile.', 65.00, 1.10, 'lemonade'
    UNION ALL SELECT 'İçecekler', 'Soğuk Çay', 'Mevsim meyveli özel karışım.', 70.00, 1.20, 'ice-tea'
) t ON t.category_name = c.name;

INSERT INTO reservations (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
SELECT 'RSV-0001', 1, r.name, t.id, t.table_number, 'Diyar Sarı', 'user@reserve.local', '+905551112233', 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:30:00', 'confirmed', 'QR-RSV-0001-A1', 'Pencere kenarı tercih edilir.', r.owner_email
FROM restaurants r
JOIN tables t ON t.restaurant_id = r.id AND t.table_number = 'T3'
WHERE r.id = 1;

INSERT INTO reservations (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
SELECT 'RSV-0002', 7, r.name, t.id, t.table_number, 'Ayşe Yılmaz', 'ayse@example.com', '+905443334455', 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '20:00:00', 'confirmed', 'QR-RSV-0002-B2', 'Doğum günü için sessiz masa.', r.owner_email
FROM restaurants r
JOIN tables t ON t.restaurant_id = r.id AND t.table_number = 'T5'
WHERE r.id = 7;

INSERT INTO reservations (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
SELECT 'RSV-0003', 13, r.name, t.id, t.table_number, 'Mehmet Kaya', 'mehmet@example.com', '+905326667788', 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '21:00:00', 'pending', 'QR-RSV-0003-C3', 'Alerji: yer fıstığı.', r.owner_email
FROM restaurants r
JOIN tables t ON t.restaurant_id = r.id AND t.table_number = 'T4'
WHERE r.id = 13;

INSERT INTO reservations (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
SELECT 'RSV-0004', 21, r.name, t.id, t.table_number, 'Elif Demir', 'elif@example.com', '+905337771122', 2, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '19:00:00', 'checked_in', 'QR-RSV-0004-D4', 'Dış alan tercih edilir.', r.owner_email
FROM restaurants r
JOIN tables t ON t.restaurant_id = r.id AND t.table_number = 'T2'
WHERE r.id = 21;

INSERT INTO reservations (reservation_code, restaurant_id, restaurant_name, table_id, table_number, customer_name, customer_email, customer_phone, guest_count, reservation_date, reservation_time, status, qr_token, notes, owner_email)
SELECT 'RSV-0005', 26, r.name, t.id, t.table_number, 'Can Öztürk', 'can@example.com', '+905399991100', 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '20:30:00', 'completed', 'QR-RSV-0005-E5', 'Sushi tadım menüsü istiyor.', r.owner_email
FROM restaurants r
JOIN tables t ON t.restaurant_id = r.id AND t.table_number = 'T1'
WHERE r.id = 26;

INSERT INTO restaurant_reviews (reservation_id, restaurant_id, user_email, rating, created_at)
SELECT r.id, r.restaurant_id, r.customer_email, 5, NOW()
FROM reservations r
WHERE r.reservation_code = 'RSV-0005';

INSERT INTO restaurant_reviews (reservation_id, restaurant_id, user_email, rating, created_at)
SELECT r.id, r.restaurant_id, r.customer_email, 4, NOW()
FROM reservations r
WHERE r.reservation_code = 'RSV-0001';

INSERT INTO restaurant_reviews (reservation_id, restaurant_id, user_email, rating, created_at)
SELECT r.id, r.restaurant_id, r.customer_email, 5, NOW()
FROM reservations r
WHERE r.reservation_code = 'RSV-0002';

INSERT INTO restaurant_reviews (reservation_id, restaurant_id, user_email, rating, created_at)
SELECT r.id, r.restaurant_id, r.customer_email, 3, NOW()
FROM reservations r
WHERE r.reservation_code = 'RSV-0003';

INSERT INTO restaurant_reviews (reservation_id, restaurant_id, user_email, rating, created_at)
SELECT r.id, r.restaurant_id, r.customer_email, 5, NOW()
FROM reservations r
WHERE r.reservation_code = 'RSV-0004';

INSERT INTO restaurant_partner_applications (restaurant_name, contact_name, restaurant_email, phone, city, district, neighborhood, street, avenue, building_number, floor_number, apartment_number, door_number, postal_code, address_notes, address, cuisine_type, description, opening_time, closing_time, image_url, password_hash, status, linked_restaurant_id, review_notes, reviewed_by_email, reviewed_at, created_at, address_id) VALUES
('Vadi Gastro', 'Selim Arslan', 'vadi@partner.local', '+905324445566', 'İstanbul', 'Şişli', 'Mecidiyeköy', 'Büyükdere Caddesi', 'Profilo Yolu', '118', '2', '1', '3', '34394', 'AVM karşı çaprazı', 'Büyükdere Caddesi No:118 Şişli/İstanbul', 'Dünya Mutfağı', 'Geniş menü ve kurumsal etkinlik odaklı restoran başvurusu.', '10:00:00', '23:00:00', 'https://picsum.photos/seed/partner-01/1200/800', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'pending', NULL, NULL, NULL, NULL, NOW(), NULL),
('Sahil Tat', 'Pelin Yıldız', 'sahil@partner.local', '+905303334455', 'İzmir', 'Konak', 'Güzelyalı', 'Mithatpaşa Caddesi', 'Sahil Yolu', '221', '1', '2', '5', '35280', 'Vapur iskelesine yakın', 'Mithatpaşa Caddesi No:221 Konak/İzmir', 'Akdeniz', 'Deniz ürünleri odaklı butik restoran partner başvurusu.', '11:00:00', '23:30:00', 'https://picsum.photos/seed/partner-02/1200/800', '$2y$10$h2UoZeErFKDlwK9eua8c2eUjEHtGZICpl6LHcJ6ZI898Ye3P/0xDq', 'approved', 13, 'Belgeler eksiksiz, süreç tamamlandı.', 'admin@reserve.local', NOW(), NOW(), 14);

INSERT INTO system_logs (level, message, context, created_at) VALUES
('info', 'Seed verisi yüklendi', JSON_OBJECT('source', 'database/seed.sql'), NOW()),
('info', 'Örnek restoranlar oluşturuldu', JSON_OBJECT('count', 30), NOW()),
('info', 'Örnek menüler oluşturuldu', JSON_OBJECT('categories_per_restaurant', 4, 'items_per_category', 3), NOW()),
('info', 'Türkçe karakter testi: Çeşme, Şişli, İğneada, Öğün', JSON_OBJECT('test', 'çğıöşü ÇĞİÖŞÜ'), NOW());
