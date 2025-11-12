<h2>Liste des Jeux</h2>
<a href="index.php?action=add">➕ Ajouter un jeu</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Nom</th><th>Prix</th><th>Stock</th><th>Catégorie</th><th>Action</th></tr>
<?php foreach ($stmt as $row): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= htmlspecialchars($row['nom']) ?></td>
  <td><?= $row['prix'] ?> €</td>
  <td><?= $row['stock'] ?></td>
  <td><?= htmlspecialchars($row['categorie']) ?></td>
  <td>
    <a href="index.php?action=edit&id=<?= $row['id'] ?>">Modifier</a> |
    <a href="index.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
  </td>
</tr>
<?php endforeach; ?>
</table>