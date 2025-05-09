<?php
$root = __DIR__;
$excluded = ['index.php', 'favicon.ico', '.', '..'];

$items = array_filter(scandir($root), function($item) use ($excluded, $root) {
    return !in_array($item, $excluded);
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Projets - <?= gethostname() ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/716/716784.png" type="image/png">
    <style>
        .card:hover {
            transform: translateY(-5px);
            transition: 0.3s;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .icon {
            font-size: 40px;
        }
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #ccc;
        }
        .favorite-btn.active {
            color: gold;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="text-center mb-4">📂 Mes Projets - <small class="text-muted"><?= gethostname() ?></small></h1>

    <!-- 🔍 Champ de recherche -->
    <div class="row mb-3">
        <div class="col-md-8">
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un projet ou fichier...">
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <input type="checkbox" id="showFavorites" class="form-check-input me-2">
            <label for="showFavorites" class="form-check-label">Afficher uniquement les favoris</label>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($items)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">Aucun projet ou fichier trouvé dans le dossier racine.</div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <?php
                $path = $root . '/' . $item;
                $isDir = is_dir($path);
                $icon = $isDir ? '<i class="bi bi-folder-fill text-warning icon"></i>' : '<i class="bi bi-file-earmark-fill text-secondary icon"></i>';
                $lastMod = date("d/m/Y H:i", filemtime($path));
                $size = !$isDir ? round(filesize($path) / 1024, 1) . ' Ko' : '';
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3 project-card" data-name="<?= htmlspecialchars($item) ?>">
                    <div class="card shadow-sm h-100 position-relative">
                        <!-- ⭐️ Bouton favori -->
                        <i class="bi bi-star-fill favorite-btn" data-name="<?= htmlspecialchars($item) ?>"></i>

                        <div class="card-body d-flex flex-column text-center">
                            <?= $icon ?>
                            <h6 class="card-title text-truncate mt-2"><?= htmlspecialchars($item) ?></h6>
                            <p class="card-text text-muted small">Modifié : <?= $lastMod ?></p>
                            <?php if (!$isDir): ?>
                                <p class="card-text text-muted small">Taille : <?= $size ?></p>
                            <?php endif; ?>
                            <a href="<?= htmlspecialchars($item) ?>" class="btn btn-primary mt-auto" target="_blank">
                                <?= $isDir ? 'Ouvrir le dossier' : 'Télécharger / Ouvrir' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center mt-5 mb-3 text-muted small">
    Laragon Server &copy; <?= date('Y') ?> - <?= gethostname() ?>
</footer>

<script>
// 🔍 Recherche
document.getElementById('searchInput').addEventListener('input', filterProjects);
document.getElementById('showFavorites').addEventListener('change', filterProjects);

const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');

// Init favoris affichage étoiles
document.querySelectorAll('.favorite-btn').forEach(btn => {
    const name = btn.dataset.name;
    if (favorites.includes(name)) btn.classList.add('active');

    btn.addEventListener('click', () => {
        if (favorites.includes(name)) {
            favorites.splice(favorites.indexOf(name), 1);
            btn.classList.remove('active');
        } else {
            favorites.push(name);
            btn.classList.add('active');
        }
        localStorage.setItem('favorites', JSON.stringify(favorites));
        filterProjects(); // Met à jour la vue si filtrage activé
    });
});

// Fonction de filtrage
function filterProjects() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const showFavs = document.getElementById('showFavorites').checked;

    document.querySelectorAll('.project-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const isFavorite = favorites.includes(card.dataset.name);
        let visible = name.includes(filter);

        if (showFavs && !isFavorite) visible = false;
        card.style.display = visible ? '' : 'none';
    });
}
</script>

</body>
</html>
