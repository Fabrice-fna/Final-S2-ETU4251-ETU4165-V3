<?php
session_start();
include("../inc/connexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mdp = $_POST['mdp'];

    $sql = "SELECT id_membre, mdp FROM membre WHERE email = '$email'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($mdp, $user['mdp'])) {
            $_SESSION['id_membre'] = $user['id_membre'];
            header("Location: liste_objet.php");
            exit;
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Email introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <h2 class="text-center mb-4">Connexion</h2>

                <?php if (isset($message)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" id="email" name="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="mdp" class="form-label">Mot de passe :</label>
                        <input type="password" id="mdp" name="mdp" class="form-control" required />
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Se connecter</button>
                    </div>
                </form>

                <p class="text-center mt-3">
                    Pas encore inscrit ? <a href="inscription.php">Créer un compte</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
