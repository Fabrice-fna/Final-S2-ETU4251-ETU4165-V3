<?php
session_start();
include("../inc/connexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    $verif = mysqli_query($conn, "SELECT * FROM membre WHERE email = '$email'");
    if (mysqli_num_rows($verif) > 0) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $sql = "INSERT INTO membre (nom, email, mdp) VALUES ('$nom', '$email', '$mdp')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['id_membre'] = mysqli_insert_id($conn);
            header("Location: liste_objet.php");
            exit;
        } else {
            $message = "Erreur : " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inscription</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h2 class="text-center mb-4">Inscription</h2>

                <?php if (isset($message)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom :</label>
                        <input type="text" id="nom" name="nom" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" id="email" name="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="mdp" class="form-label">Mot de passe :</label>
                        <input type="password" id="mdp" name="mdp" class="form-control" required />
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>
                </form>

                <p class="text-center mt-3">
                    Déjà inscrit ? <a href="login.php">Connexion</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
