<h2>Modifier un jeu</h2>
<form method="POST">
  Nom: <input type="text" name="nom" value="<?= htmlspecialchars($jeu['nom']) ?>"><br>
  Description: <textarea name="description"><?= htmlspecialchars($jeu['description']) ?></textarea><br>
  Prix: <input type="number" step="0.01" name="prix" value="<?= $jeu['prix'] ?>"><br>
  Stock: <input type="number" name="stock" value="<?= $jeu['stock'] ?>"><br>
  Catégorie: <input type="text" name="categorie" value="<?= htmlspecialchars($jeu['categorie']) ?>"><br>
  <button type="submit">Modifier</button>
</form>
<a href="index.php">⬅ Retour</a>