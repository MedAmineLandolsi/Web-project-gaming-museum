<?php
require_once "../config/Database.php";
require_once "../models/Jeux.php";
$db = (new Database())->getConnection();
$model = new Jeux($db);
$stmt = $model->readAll();
?>
<h1>🎮 Catalogue de Jeux</h1>
<ul>
<?php foreach ($stmt as $row): ?>
  <li><b><?= htmlspecialchars($row['nom']) ?></b> - <?= $row['prix'] ?> € (<?= $row['categorie'] ?>)</li>
<?php endforeach; ?>
</ul>