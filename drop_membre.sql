-- Nettoyage des anciennes tables liées aux "membres".
-- Objectif : supprimer les tables `membre` et `membre_communaute` même si des contraintes FK existent encore.
-- Compatible MySQL / MariaDB (phpMyAdmin).
-- Note: les PREPARE ne peuvent pas exécuter plusieurs statements séparés par ';'.
-- On utilise donc une procédure temporaire + curseur.

SET FOREIGN_KEY_CHECKS = 0;

-- Vues éventuelles (anciennes)
DROP VIEW IF EXISTS membre_communaute_with_users;
DROP VIEW IF EXISTS interactions_with_users;

DELIMITER $$

CREATE PROCEDURE drop_fks_referencing(IN ref_table VARCHAR(64))
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE table_name VARCHAR(64);
  DECLARE constraint_name VARCHAR(64);

  DECLARE cur CURSOR FOR
    SELECT rc.TABLE_NAME, rc.CONSTRAINT_NAME
    FROM information_schema.REFERENTIAL_CONSTRAINTS rc
    WHERE rc.CONSTRAINT_SCHEMA = DATABASE()
      AND rc.REFERENCED_TABLE_NAME = ref_table;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO table_name, constraint_name;
    IF done = 1 THEN
      LEAVE read_loop;
    END IF;

    SET @sql_stmt = CONCAT(
      'ALTER TABLE `', table_name, '` DROP FOREIGN KEY `', constraint_name, '`'
    );
    PREPARE stmt FROM @sql_stmt;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END LOOP;
  CLOSE cur;
END$$

DELIMITER ;

-- Supprimer les contraintes FK qui pointent vers ces tables
CALL drop_fks_referencing('membre');
CALL drop_fks_referencing('membre_communaute');

DROP PROCEDURE IF EXISTS drop_fks_referencing;

-- Puis supprimer les tables
DROP TABLE IF EXISTS membre_communaute;
DROP TABLE IF EXISTS membre;

SET FOREIGN_KEY_CHECKS = 1;
