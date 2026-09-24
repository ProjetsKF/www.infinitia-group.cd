# Module Formations — installation

## Migration

Exécuter, dans cet ordre, les migrations suivantes sur la base du site :

1. `migrations/20260924_training_module.sql` ;
2. `migrations/20260924_training_courses.sql`.

La première migration crée :

- `administrators` ;
- `training_sessions` ;
- `training_registrations`.

La seconde migration crée `training_courses` et y importe les dix formations du catalogue initial. Cette table devient la source officielle du catalogue public et de son administration. `config/formations.php` conserve seulement une copie de secours utilisée lorsque la table n’existe pas encore.

Les sessions restent reliées aux formations par le code technique stable `formation_code`. Le code d’une formation existante n’est donc pas modifiable dans l’administration, ce qui préserve les sessions et inscriptions historiques.

La migration inverse `migrations/20260924_training_courses_rollback.sql` renomme la table du catalogue au lieu de supprimer ses données. La migration inverse `migrations/20260924_training_module_rollback.sql` supprime les trois tables du module ; elle ne doit être exécutée qu’après sauvegarde, car elle supprime également les demandes enregistrées.

## Connexion à la base

La connexion existante peut être configurée avec les variables d’environnement suivantes :

- `INFINITIA_DB_HOST` ;
- `INFINITIA_DB_USER` ;
- `INFINITIA_DB_PASSWORD` ;
- `INFINITIA_DB_NAME`.

## Premier administrateur

Aucun compte ou mot de passe par défaut n’est créé. Après la migration, créer explicitement le premier compte depuis un terminal :

```text
php tools/create-admin.php admin@example.com "Nom complet"
```

Le script demande un mot de passe d’au moins 12 caractères et stocke uniquement son hash.

## Règle de capacité

Seules les demandes au statut `accepted` occupent une place. Les demandes `new` et `processing` restent des demandes en cours de traitement et ne sont pas comptées comme des admissions définitives.

Une session est proposée au public uniquement si elle est `open`, non terminée et si sa capacité n’est pas atteinte. Lorsque la dernière place est acceptée, la session passe automatiquement à `full`.
