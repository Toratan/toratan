DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER T_FOLDERS_SHARE AFTER UPDATE ON folders FOR EACH ROW
BEGIN
    -- check if any publicity flag has been changed
    IF OLD.is_public <> NEW.is_public THEN 
        -- if so ?
        -- update the publicity of the sub-notes of current folder
        UPDATE `notes` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
        -- update the publicity of the sub-links of current folder
        UPDATE `links` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
    END IF;
END */;;
DELIMITER ;
