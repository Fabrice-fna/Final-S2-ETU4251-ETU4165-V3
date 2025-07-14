<?php
session_start();
include("../inc/connexion.php");

if (!isset($_GET['id_objet'])) {
    header("Location: liste_objet.php");
    exit;
}

$id_objet = (int)$_GET['id_objet'];
$voir_toutes = isset($_GET['voir_toutes']) && $_GET['voir_toutes'] == 1;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $newName = uniqid() . '.' . $ext;
        $uploadDir = 'img/'; 
        $uploadPath = $uploadDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $sql = "INSERT INTO images_objet (id_objet, nom_image) VALUES ($id_objet, '" . mysqli_real_escape_string($conn, $newName) . "')";
            mysqli_query($conn, $sql);
            $message = "Image ajoutée avec succès.";
        } else {
            $message = "Erreur lors de l’upload.";
        }
    } else {
        $message = "Format de fichier non autorisé.";
    }
}


if (isset($_GET['delete_image'])) {
    $id_image = (int)$_GET['delete_image'];
    $res = mysqli_query($conn, "SELECT nom_image FROM images_objet WHERE id_image = $id_image AND id_objet = $id_objet");

    if ($res && mysqli_num_rows($res) > 0) {
        $img = mysqli_fetch_assoc($res);
        $filePath = 'img/' . $img['nom_image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        mysqli_query($conn, "DELETE FROM images_objet WHERE id_image = $id_image");
        $message = "Image supprimée.";
    }
}


$resImages = mysqli_query($conn, "SELECT * FROM images_objet WHERE id_objet = $id_objet ORDER BY id_image ASC");
$images = [];
while ($row = mysqli_fetch_assoc($resImages)) {
    $images[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Images de l'objet #<?= $id_objet ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <style>
        .img-thumb {
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            margin: 10px;
            border: 1px solid #ccc;
            padding: 5px;
        }
        .img-container {
            display: inline-block;
            position: relative;
            margin: 5px;
        }
    </style>
</head>
<body class="container mt-4">

    <h2>Images de l'objet #<?= $id_objet ?></h2>

    <?php if (isset($message)) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <a href="liste_objet.php" class="btn btn-secondary mb-3">Retour à la liste des objets</a>

    <h4>Ajouter une nouvelle image</h4>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="file" name="image" required accept=".jpg,.jpeg,.png,.gif" />
        <button type="submit" class="btn btn-primary">Uploader</button>
    </form>

    <?php if (count($images) > 0): ?>
        <?php if (!$voir_toutes): ?>
            <h4>Première image</h4>
            <div class="img-container text-center">
                <img src="img/<?= htmlspecialchars($images[0]['nom_image']) ?>" alt="Image" class="img-thumb" />
                <div class="mt-2">
                    <a href="?id_objet=<?= $id_objet ?>&delete_image=<?= $images[0]['id_image'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette image ?');">Supprimer l'image</a>
                </div>
            </div>
            <p>
                <a href="?id_objet=<?= $id_objet ?>&voir_toutes=1" class="btn btn-outline-primary mt-3">Voir toutes les images</a>
            </p>
        <?php else: ?>
            <h4>Toutes les images</h4>
            <?php foreach ($images as $img): ?>
                <div class="img-container text-center">
                    <img src="img/<?= htmlspecialchars($img['nom_image']) ?>" alt="Image" class="img-thumb" />
                    <div class="mt-2">
                        <a href="?id_objet=<?= $id_objet ?>&delete_image=<?= $img['id_image'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette image ?');">Supprimer l'image</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <p>
                <a href="?id_objet=<?= $id_objet ?>" class="btn btn-outline-secondary mt-3">Voir seulement la première image</a>
            </p>
        <?php endif; ?>
    <?php else: ?>
        <p>Aucune image pour cet objet.</p>
    <?php endif; ?>

</body>
</html>
