<?php
// create_views.php
// Usage: run via browser or CLI to create helpful views for direct joins

require_once __DIR__ . '/config.php';

try {
    $db = getDB(); // assumes config.php exposes getDB() or $db_with_dbname; adapt if needed
} catch (Exception $e) {
    // Try the global $db
    if (isset($db)) {
        // ok
    } else {
        echo "Impossible d'obtenir la connexion DB : " . $e->getMessage();
        exit(1);
    }
}

try {
    // View: interactions joined to users via membre.user_id
    $sql1 = "CREATE OR REPLACE VIEW interactions_with_users AS
             SELECT i.*, m.id AS membre_id, u.id AS user_id, u.username, u.profile_picture_url
             FROM interactions i
             LEFT JOIN membre m ON i.user_id = m.id
             LEFT JOIN users u ON m.user_id = u.id";

    $db->exec($sql1);

    // View: membre_communaute joined to users via membre.user_id
    $sql2 = "CREATE OR REPLACE VIEW membre_communaute_with_users AS
             SELECT mc.*, m.id AS membre_id, u.id AS user_id, u.username, u.profile_picture_url
             FROM membre_communaute mc
             LEFT JOIN membre m ON mc.membre_id = m.id
             LEFT JOIN users u ON m.user_id = u.id";

    $db->exec($sql2);

    echo "✅ Vues créées/mises à jour : interactions_with_users, membre_communaute_with_users\n";
} catch (PDOException $e) {
    echo "Erreur lors de la création des vues : " . $e->getMessage() . "\n";
    exit(1);
}

?>