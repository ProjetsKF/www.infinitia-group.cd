-- Rollback conservateur : les données sont renommées, pas supprimées.
-- Cette commande échoue volontairement si la table de sauvegarde existe déjà.
RENAME TABLE training_courses TO training_courses_rollback_backup_20260924;
