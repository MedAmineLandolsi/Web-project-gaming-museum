<h2>Ajouter un jeu</h2>
<p style="color:red"><?= $error ?? "" ?></p>
<form method="POST">
  Nom: <input type="text" name="nom"><br>
  Description: <textarea name="description"></textarea><br>
  Prix: <input type="number" step="0.01" name="prix"><br>
  Stock: <input type="number" name="stock"><br>
  Catégorie: <input type="text" name="categorie"><br>
  <button type="submit">Ajouter</button>
</form>
<a href="index.php">⬅ Retour</a>