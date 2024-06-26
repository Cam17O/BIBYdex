<!----------------------------- page de création de compte ----------------------------->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte bibydex</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script defer src="assets/scripts/CreateAccount.js"></script>
</head>

<body>
    <div class="container">

        <!-- Zone pour le formulaire -->
        <div class="card" style="width: 80%; ">

            <!-- header de la zone -->
            <div class="card-header">
                <h1>Créer un compte</h1>
            </div>

            <!-- body de la zone -->
            <div class="card-body">
                <!-- formulaire pour obtenir les données pour créer un compte : méthode POST-->
                <form action="/assets/db/CreateAccount.php" method="post" id="form">

                    <div id="error"></div>

                    <!-- pseudo -->
                    <label for="pseudo" class="form-label">Pseudo</label>
                    <input type="text" class="form-control" name="pseudo" id="pseudo">

                    <!-- mail -->
                    <label for="mail" class="form-label">Mail</label>
                    <input type="text" class="form-control" name="mail" id="mail">

                    <!-- mot de passe -->

                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" name="password" id="password">

                    <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password">

                    </br>

                    <!-- Boutton de validation du formulaire : active le script assets/scripts/CreateAccount.php -->
                    <input type="submit" class="btn btn-primary" value="Créer le compte">

                </form>

                </br>

                <!-- Lien vers la page de connexion au cas ou l'utilisateur à déjà un compte -->
                <h6>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></h3>
            </div>
        </div>
    </div>
</body>

</html>