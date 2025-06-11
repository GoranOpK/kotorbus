USE web_base;

-- 1. Procedura za dodavanje nove rezervacije
-- Poziva se CALL AddReservation(5, 15, '2025-05-29', 'Turist test', 'Crna gora', 'KOAB123', 3, 'pera@example.com');
DELIMITER $$

CREATE PROCEDURE AddReservation (
    IN dropOffTimeSlotId INT,
    IN pickUpTimeSlotId INT,
    IN reservation_date DATE,
    IN userName VARCHAR(255),
    IN countryName VARCHAR(100),
    IN licensePlate VARCHAR(50),
    IN vehicleTypeId INT,
    IN e_mail VARCHAR(255)
)
BEGIN
    DECLARE preostalo_dorp_off INT;
    DECLARE preostalo_pick_up INT;
    DECLARE reservation_table VARCHAR(20);
    DECLARE is_drop_off_available BOOLEAN DEFAULT FALSE;
    DECLARE is_pick_up_available BOOLEAN DEFAULT FALSE;

    SET reservation_table = DATE_FORMAT(reservation_date, '%Y%m%d');

    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'web_base' 
        AND table_name = reservation_table)
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabela nije kreirana.';
    END IF;

    -- Provera validnosti tipa vozila
    IF NOT (vehicleTypeId = 1 OR vehicleTypeId = 2  OR vehicleTypeId = 3 OR vehicleTypeId = 4) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid vehicle type.';
    END IF;

    -- Provera dostupnosti drop-off slota
    SET @sql = CONCAT('SELECT available INTO @available_drop_off FROM `', reservation_table, '` WHERE time_slot_id = ?');
    PREPARE stmt FROM @sql;
    SET @slot_id = dropOffTimeSlotId;
    EXECUTE stmt USING @slot_id;
    DEALLOCATE PREPARE stmt;
    SET is_drop_off_available = @available_drop_off;

    -- Provera dostupnosti pick-up slota
    SET @sql = CONCAT('SELECT available INTO @available_pick_up FROM `', reservation_table, '` WHERE time_slot_id = ?');
    PREPARE stmt FROM @sql;
    SET @slot_id = pickUpTimeSlotId;
    EXECUTE stmt USING @slot_id;
    DEALLOCATE PREPARE stmt;
    SET is_pick_up_available = @available_pick_up;

    IF is_drop_off_available AND is_pick_up_available THEN
        -- Unos rezervacije
        INSERT INTO reservations (drop_off_time_slot_id,  pick_up_time_slot_id, reservation_date, user_name, country, license_plate, vehicle_type_id, email, status)
        VALUES (dropOffTimeSlotId, pickUpTimeSlotId, reservation_date, userName, countryName, licensePlate, vehicleTypeId, e_mail, 'pending');

        -- remaining za drop-off
        SET @sql = CONCAT('SELECT remaining INTO @preostalo_dorp_off FROM `', reservation_table, '` WHERE time_slot_id = ?');
        PREPARE stmt FROM @sql;
        SET @slot_id = dropOffTimeSlotId;
        EXECUTE stmt USING @slot_id;
        DEALLOCATE PREPARE stmt;
        SET preostalo_dorp_off = @preostalo_dorp_off - 1;

        -- remaining za pick-up
        SET @sql = CONCAT('SELECT remaining INTO @preostalo_pick_up FROM `', reservation_table, '` WHERE time_slot_id = ?');
        PREPARE stmt FROM @sql;
        SET @slot_id = pickUpTimeSlotId;
        EXECUTE stmt USING @slot_id;
        DEALLOCATE PREPARE stmt;
        SET preostalo_pick_up = @preostalo_pick_up - 1;

        -- UPDATE drop-off
        SET @sql = CONCAT('UPDATE `', reservation_table, '` SET remaining = ?, available = ? WHERE time_slot_id = ?');
        PREPARE stmt FROM @sql;
        SET @new_remaining1 = preostalo_dorp_off;
        SET @new_available1 = IF(preostalo_dorp_off > 0, TRUE, FALSE);
        SET @slot_id1 = dropOffTimeSlotId;
        EXECUTE stmt USING @new_remaining1, @new_available1, @slot_id1;
        DEALLOCATE PREPARE stmt;

        -- UPDATE pick-up
        SET @sql = CONCAT('UPDATE `', reservation_table, '` SET remaining = ?, available = ? WHERE time_slot_id = ?');
        PREPARE stmt FROM @sql;
        SET @new_remaining2 = preostalo_pick_up;
        SET @new_available2 = IF(preostalo_pick_up > 0, TRUE, FALSE);
        SET @slot_id2 = pickUpTimeSlotId;
        EXECUTE stmt USING @new_remaining2, @new_available2, @slot_id2;
        DEALLOCATE PREPARE stmt;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Vremenski slot nije dostupan za rezervaciju.';
    END IF;
END$$

DELIMITER ;

-- 2. Procedura za provjeru statusa vremenskog slota za zadati datum
-- Poziva se sa:
-- SET @da_li_je_dostupan = NULL;
-- CALL CheckTimeSlotAvailability(5, '2025-05-29', @da_li_je_dostupan);
DELIMITER $$
CREATE PROCEDURE CheckTimeSlotAvailability (
    IN timeSlotId INT,
    IN reservation_date DATE,
    OUT isAvailable BOOLEAN
)
BEGIN
    DECLARE reservation_table VARCHAR(20);
    SET reservation_table = DATE_FORMAT(reservation_date, '%Y%m%d');
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'web_base' 
        AND table_name = reservation_table)
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabela nije kreirana.';
    END IF;
    SET @sql = CONCAT('SELECT available INTO @is_available FROM `', reservation_table, '` WHERE time_slot_id = ?');
    PREPARE stmt FROM @sql;
    SET @slot_id = timeSlotId;
    EXECUTE stmt USING @slot_id;
    DEALLOCATE PREPARE stmt;
    SET isAvailable = @is_available;
END$$
DELIMITER ;

-- 3. Procedura za dinamičko pravljenje tabela
-- Poziva se sa CALL CreateTableForDateWithData('2025-05-28');
DELIMITER $$
CREATE PROCEDURE CreateTableForDateWithData(IN p_date DATE)
BEGIN
    DECLARE table_name VARCHAR(20);
    DECLARE sql_query TEXT;
    DECLARE insert_query TEXT;
    DECLARE i INT;
    DECLARE default_remaining INT DEFAULT 7;
-- Dohvati broj mesta iz system_config
    SELECT CAST(value AS UNSIGNED) INTO default_remaining FROM system_config WHERE name = 'available_parking_slots';
    SET table_name = DATE_FORMAT(p_date, '%Y%m%d');
    SET @sql_query = CONCAT('CREATE TABLE IF NOT EXISTS `', table_name, '` (id INT AUTO_INCREMENT PRIMARY KEY, time_slot_id INT NOT NULL, remaining INT NOT NULL, available BOOLEAN DEFAULT TRUE, FOREIGN KEY (time_slot_id) REFERENCES list_of_time_slots(id))');
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
-- Prva vrsta
    SET @insert_query = CONCAT('INSERT INTO `', table_name, '` (time_slot_id, remaining, available) VALUES (1, 999, TRUE)');
    PREPARE stmt FROM @insert_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
-- Srednje vrste
    SET i = 2;
    WHILE i <= 40 DO
        SET @insert_query = CONCAT('INSERT INTO `', table_name, '` (time_slot_id, remaining, available) VALUES (', i, ', ', default_remaining, ', TRUE)');
        PREPARE stmt FROM @insert_query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SET i = i + 1;
    END WHILE;
-- Poslednja vrsta
    SET @insert_query = CONCAT('INSERT INTO `', table_name, '` (time_slot_id, remaining, available) VALUES (41, 999, TRUE)');
    PREPARE stmt FROM @insert_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- 4. Procedura za brisanje tabele po datumu
-- Poziva se sa CALL DropTableForDate('2025-05-28');
DELIMITER $$
CREATE PROCEDURE DropTableForDate(IN p_date DATE)
BEGIN
    DECLARE reservation_table VARCHAR(20);
    DECLARE sql_query TEXT;
-- Formatiraj datum u 'YYYYMMDD'
    SET reservation_table = DATE_FORMAT(p_date, '%Y%m%d');
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'web_base' 
        AND table_name = CAST(reservation_table AS CHAR))
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabela nije kreirana.';
    END IF;
-- Sastavi SQL upit za brisanje tabele
    SET @sql_query = CONCAT('DROP TABLE IF EXISTS `', reservation_table, '`');
-- Pripremi i izvrši dinamički SQL
    PREPARE stmt FROM @sql_query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

-- 5. Procedura koja blokira prodaju svih time slotova za zadati dan
-- Poziva se sa CALL BlockTableForDate('2025-05-29');
DELIMITER $$
CREATE PROCEDURE BlockTableForDate(IN p_date DATE)
BEGIN
    DECLARE reservation_table VARCHAR(20);
    DECLARE insert_query TEXT;
    DECLARE i INT;
    SET reservation_table = DATE_FORMAT(p_date, '%Y%m%d');
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'web_base' 
        AND table_name = CAST(reservation_table AS CHAR))
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabela nije kreirana.';
    END IF;
    SET i = 1;
    WHILE i <= 41 DO
        SET @insert_query = CONCAT('UPDATE `', reservation_table, '` SET remaining = 0, available = FALSE WHERE time_slot_id = ',i);
        PREPARE stmt FROM @insert_query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SET i = i + 1;
    END WHILE;
END$$
DELIMITER ;

-- 6. Procedura koja blokira prodaju time slotova za zadati dan u zadatom intervalu
-- Poziva se sa CALL BlockSlotsForDate('2025-05-29', 10, 20);
DELIMITER $$
CREATE PROCEDURE BlockSlotsForDate(
    IN p_date DATE,
    IN firstTimeSlot INT,
    IN lastTimeSlot INT
)
BEGIN
    DECLARE reservation_table VARCHAR(20);
    DECLARE insert_query TEXT;
    DECLARE i INT;
-- Formatiraj datum u 'YYYYMMDD'
    SET reservation_table = DATE_FORMAT(p_date, '%Y%m%d');

    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.tables 
        WHERE table_schema = 'web_base' 
        AND table_name = CAST(reservation_table AS CHAR))
    THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabela nije kreirana.';
    END IF;

    IF (firstTimeSlot <= lastTimeSlot AND 
        firstTimeSlot >= 1 AND
        firstTimeSlot <= 41 AND
        lastTimeSlot >= 1 AND
        lastTimeSlot <= 41) THEN
        SET i = firstTimeSlot;
        WHILE i <= lastTimeSlot DO
            SET @insert_query = CONCAT('UPDATE `', reservation_table, '` SET remaining = 0, available = FALSE WHERE time_slot_id = ',i);
            PREPARE stmt FROM @insert_query;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            SET i = i + 1;
        END WHILE;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Prvi ili poslednji time slot nisu korektno uneti.';
    END IF;
END$$
DELIMITER ;