<?php
session_start();
include("../inc/connexion.php");

$id_membre = $_SESSION['id_membre'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_objet'], $_POST['date_retour'])) {
    $id_objet_post = (int)$_POST['id_objet'];
    $date_retour_input = $_POST['date_retour'];

    $date_auj = new DateTime();
    $date_retour = DateTime::createFromFormat('Y-m-d', $date_retour_input);

    if ($date_retour && $date_retour > $date_auj) {
        $interval = $date_auj->diff($date_retour)->days;
        if ($interval <= 365) {
            $verif = mysqli_query($conn, "SELECT * FROM emprunt WHERE id_objet = $id_objet_post AND (date_retour >= CURDATE())");
            if (mysqli_num_rows($verif) === 0) {
                $insert_sql = "INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) 
                               VALUES ($id_objet_post, $id_membre, CURDATE(), '$date_retour_input')";
                mysqli_query($conn, $insert_sql);
                $_SESSION['message'] = "Objet emprunté jusqu'au $date_retour_input.";
            } else {
                $_SESSION['message'] = "Cet objet n'est plus disponible.";
            }
        } else {
            $_SESSION['message'] = "Durée invalide (max 365 jours).";
        }
    } else {
        $_SESSION['message'] = "Date invalide ou antérieure à aujourd'hui.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$categorie = $_GET['categorie'] ?? "";
$nom_objet = $_GET['nom_objet'] ?? "";
$disponible = isset($_GET['disponible']);

$sql = "SELECT o.id_objet, o.nom_objet, c.nom_categorie, e.date_retour, i.nom_image
        FROM objet o
        JOIN categorie_objet c ON o.id_categorie = c.id_categorie
        LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND (e.date_retour IS NULL OR e.date_retour >= CURDATE())
        LEFT JOIN images_objet i ON o.id_objet = i.id_objet";

$conditions = [];
if ($categorie) $conditions[] = "c.nom_categorie = '" . mysqli_real_escape_string($conn, $categorie) . "'";
if ($nom_objet) $conditions[] = "o.nom_objet LIKE '%" . mysqli_real_escape_string($conn, $nom_objet) . "%'";
if ($disponible) $conditions[] = "e.id_emprunt IS NULL";
if ($conditions) $sql .= " WHERE " . implode(" AND ", $conditions);
$res = mysqli_query($conn, $sql);

$res_dispos = mysqli_query($conn, "SELECT o.id_objet, o.nom_objet FROM objet o
    LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND (e.date_retour IS NULL OR e.date_retour >= CURDATE())
    WHERE e.id_emprunt IS NULL ORDER BY o.nom_objet ASC");
$disponibles = mysqli_fetch_all($res_dispos, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Liste des objets</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        .card-img-top { max-height: 180px; object-fit: contain; }
        .emprunt-section {
            background: #fff7f2;
            border: 2px solid #b16d54;
            border-radius: 12px;
            padding: 20px;
            margin: 30px auto;
            max-width: 650px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Liste des objets</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="GET" class="mb-4">
        <div class="row g-2 justify-content-center">
            <div class="col-auto">
                <select name="categorie" class="form-select">
                    <option value="">-- Toutes les catégories --</option>
                    <option value="Esthétique" <?= $categorie === 'Esthétique' ? 'selected' : '' ?>>Esthétique</option>
                    <option value="Bricolage" <?= $categorie === 'Bricolage' ? 'selected' : '' ?>>Bricolage</option>
                    <option value="Mécanique" <?= $categorie === 'Mécanique' ? 'selected' : '' ?>>Mécanique</option>
                    <option value="Cuisine" <?= $categorie === 'Cuisine' ? 'selected' : '' ?>>Cuisine</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="nom_objet" class="form-control" placeholder="Recherche..." value="<?= htmlspecialchars($nom_objet) ?>" />
            </div>
            <div class="col-auto form-check">
                <input type="checkbox" class="form-check-input" name="disponible" id="disponible" <?= $disponible ? 'checked' : '' ?> />
                <label for="disponible" class="form-check-label">Disponibles</label>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>

    <div class="emprunt-section">
        <h4>Emprunter un objet</h4>
        <?php if (count($disponibles) > 0): ?>
            <form method="POST" class="row g-3 align-items-end justify-content-center">
                <div class="col-md-5">
                    <label for="id_objet" class="form-label">Objet :</label>
                    <select name="id_objet" id="id_objet" class="form-select" required>
                        <option value="" disabled selected>-- Choisir un objet --</option>
                        <?php foreach ($disponibles as $obj): ?>
                            <option value="<?= $obj['id_objet'] ?>"><?= htmlspecialchars($obj['nom_objet']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date_retour" class="form-label">Date de retour :</label>
                    <input type="date" name="date_retour" id="date_retour" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="<?= date('Y-m-d', strtotime('+1 year')) ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" type="submit">Emprunter</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-center text-muted">Aucun objet disponible actuellement.</p>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php $today = date('Y-m-d');
        while ($row = mysqli_fetch_assoc($res)) : ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 text-center">
                    <?php 
                    $img = $row['nom_image'] ? 'img/' . $row['nom_image'] : 'img/default.jpg';
                    ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="Image">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['nom_objet']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($row['nom_categorie']) ?></p>
                    </div>
                    <div class="card-footer">
                        <?php if (!empty($row['date_retour']) && $row['date_retour'] > $today): ?>
                            <small class="text-danger">Disponible le <?= htmlspecialchars($row['date_retour']) ?></small>
                        <?php else: ?>
                            <small class="text-success">Disponible</small>
                        <?php endif; ?>
                        <div class="mt-2">
                            <a href="images_objet.php?id_objet=<?= $row['id_objet'] ?>" class="btn btn-outline-primary btn-sm">Voir images</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>