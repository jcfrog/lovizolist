-- À exécuter une seule fois sur une base existante après le passage à la version sans login.
ALTER TABLE lists DROP COLUMN created_by;
ALTER TABLE items DROP COLUMN added_by;
ALTER TABLE items DROP COLUMN checked_by;
