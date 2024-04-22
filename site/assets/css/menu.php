<style>
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

li {
  float: left;
}

li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

li a:hover:not(.active) {
  background-color: #111;
}

.active {
  background-color: red;
}
</style>

<ul>
    <li><a href="index.php">Accueil</a></li>
    <li><a href="galerie.php">Ma galerie</a></li>
    <li><a href="allUser.php">Liste des utilisateurs</a></li>
    <li style="float:right"><a class="active" href="logout.php">Déconnexion</a></li>
</ul>