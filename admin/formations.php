<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
infinitia_admin_require_login();

if (!infinitia_table_exists($conn, 'training_courses')) {
    infinitia_admin_header('Formations', 'courses');
    echo '<div class="alert alert-danger">La migration du catalogue des formations n’est pas encore appliquée.</div>';
    infinitia_admin_footer();
    exit;
}

$errors = array();
$show_form = isset($_GET['action']) && $_GET['action'] === 'create';
$editing = array('id' => 0, 'code' => '', 'name' => '', 'category' => '', 'short_description' => '', 'detailed_content' => '', 'duration' => '', 'price' => '', 'price_unit' => '', 'display_order' => 0, 'is_active' => 1);

if (isset($_GET['edit']) && infinitia_valid_id($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $statement = mysqli_prepare($conn, 'SELECT id, code, name, category, short_description, detailed_content, duration, price, price_unit, display_order, is_active FROM training_courses WHERE id = ? LIMIT 1');
    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $edit_id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $id, $code, $name, $category, $short_description, $detailed_content, $duration, $price, $price_unit, $display_order, $is_active);
        if (mysqli_stmt_fetch($statement)) {
            $editing = array('id' => $id, 'code' => $code, 'name' => $name, 'category' => $category, 'short_description' => $short_description, 'detailed_content' => $detailed_content, 'duration' => $duration, 'price' => $price, 'price_unit' => $price_unit, 'display_order' => $display_order, 'is_active' => $is_active);
            $show_form = true;
        }
        mysqli_stmt_close($statement);
    }
}

if (infinitia_request_method() === 'POST' && infinitia_post_value('action') === 'toggle') {
    $course_id = infinitia_valid_id(infinitia_post_value('id')) ? (int) infinitia_post_value('id') : 0;
    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token')) || $course_id <= 0) {
        infinitia_admin_flash('error', 'Action non autorisée.');
    } else {
        $now = date('Y-m-d H:i:s');
        $statement = mysqli_prepare($conn, 'UPDATE training_courses SET is_active = IF(is_active = 1, 0, 1), updated_at = ? WHERE id = ?');
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'si', $now, $course_id);
            $saved = mysqli_stmt_execute($statement) && mysqli_stmt_affected_rows($statement) === 1;
            mysqli_stmt_close($statement);
        } else {
            $saved = false;
        }
        infinitia_admin_flash($saved ? 'success' : 'error', $saved ? 'Le statut de la formation a été modifié.' : 'La formation n’a pas pu être modifiée.');
    }
    header('Location: formations.php');
    exit;
}

if (infinitia_request_method() === 'POST' && infinitia_post_value('action') === 'save') {
    $show_form = true;
    $editing = array(
        'id' => infinitia_valid_id(infinitia_post_value('id')) ? (int) infinitia_post_value('id') : 0,
        'code' => strtolower(infinitia_post_value('code')),
        'name' => infinitia_post_value('name'),
        'category' => infinitia_post_value('category'),
        'short_description' => infinitia_post_value('short_description'),
        'detailed_content' => infinitia_post_value('detailed_content'),
        'duration' => infinitia_post_value('duration'),
        'price' => str_replace(',', '.', infinitia_post_value('price')),
        'price_unit' => infinitia_post_value('price_unit'),
        'display_order' => infinitia_post_value('display_order'),
        'is_active' => infinitia_post_value('is_active') === '1' ? 1 : 0
    );

    if (!infinitia_csrf_is_valid(infinitia_post_value('csrf_token'))) {
        $errors[] = 'La session du formulaire a expiré.';
    }
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $editing['code']) || infinitia_strlen($editing['code']) > 80) {
        $errors[] = 'Le code doit contenir uniquement des lettres minuscules, chiffres et tirets.';
    }
    if ($editing['name'] === '' || infinitia_strlen($editing['name']) > 190 || $editing['category'] === '' || infinitia_strlen($editing['category']) > 120 || $editing['duration'] === '' || infinitia_strlen($editing['duration']) > 100) {
        $errors[] = 'Le nom, la catégorie et la durée sont obligatoires et doivent respecter les longueurs autorisées.';
    }
    if ($editing['short_description'] === '' || infinitia_strlen($editing['short_description']) > 500 || infinitia_strlen($editing['detailed_content']) > 5000 || infinitia_strlen($editing['price_unit']) > 50) {
        $errors[] = 'Une description est manquante ou un contenu dépasse la longueur autorisée.';
    }
    if ($editing['price'] !== '' && (!is_numeric($editing['price']) || (float) $editing['price'] < 0 || (float) $editing['price'] > 99999999.99)) {
        $errors[] = 'Le tarif est invalide.';
    }
    if (!ctype_digit((string) $editing['display_order']) || (int) $editing['display_order'] > 100000) {
        $errors[] = 'L’ordre d’affichage doit être un entier positif ou nul.';
    }

    if ((int) $editing['id'] > 0) {
        $code_statement = mysqli_prepare($conn, 'SELECT code FROM training_courses WHERE id = ? LIMIT 1');
        if ($code_statement) {
            mysqli_stmt_bind_param($code_statement, 'i', $editing['id']);
            mysqli_stmt_execute($code_statement);
            mysqli_stmt_bind_result($code_statement, $stored_code);
            $found = mysqli_stmt_fetch($code_statement);
            mysqli_stmt_close($code_statement);
            if (!$found || $stored_code !== $editing['code']) {
                $errors[] = 'Le code technique d’une formation existante ne peut pas être modifié.';
            }
        }
    } else {
        $duplicate = mysqli_prepare($conn, 'SELECT id FROM training_courses WHERE code = ? LIMIT 1');
        if ($duplicate) {
            mysqli_stmt_bind_param($duplicate, 's', $editing['code']);
            mysqli_stmt_execute($duplicate);
            mysqli_stmt_store_result($duplicate);
            if (mysqli_stmt_num_rows($duplicate) > 0) {
                $errors[] = 'Ce code est déjà utilisé par une autre formation.';
            }
            mysqli_stmt_close($duplicate);
        }
    }

    if (count($errors) === 0) {
        $now = date('Y-m-d H:i:s');
        $price_value = $editing['price'];
        $price_unit_value = $editing['price_unit'] !== '' ? $editing['price_unit'] : null;
        $detailed_value = $editing['detailed_content'] !== '' ? $editing['detailed_content'] : null;
        $order_value = (int) $editing['display_order'];
        if ((int) $editing['id'] > 0) {
            $sql = 'UPDATE training_courses SET name = ?, category = ?, short_description = ?, detailed_content = ?, duration = ?, price = NULLIF(?, \'\'), price_unit = ?, is_active = ?, display_order = ?, updated_at = ? WHERE id = ?';
            $statement = mysqli_prepare($conn, $sql);
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'sssssssiisi', $editing['name'], $editing['category'], $editing['short_description'], $detailed_value, $editing['duration'], $price_value, $price_unit_value, $editing['is_active'], $order_value, $now, $editing['id']);
            }
        } else {
            $sql = 'INSERT INTO training_courses (code, name, category, short_description, detailed_content, duration, price, price_unit, is_active, display_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, \'\'), ?, ?, ?, ?, ?)';
            $statement = mysqli_prepare($conn, $sql);
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'ssssssssiiss', $editing['code'], $editing['name'], $editing['category'], $editing['short_description'], $detailed_value, $editing['duration'], $price_value, $price_unit_value, $editing['is_active'], $order_value, $now, $now);
            }
        }
        if ($statement) {
            $saved = mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
        } else {
            $saved = false;
        }
        if ($saved) {
            infinitia_admin_flash('success', (int) $editing['id'] > 0 ? 'La formation a été mise à jour.' : 'La formation a été ajoutée.');
            header('Location: formations.php');
            exit;
        }
        $errors[] = 'La formation n’a pas pu être enregistrée.';
    }
}

$courses = infinitia_get_training_courses($conn, false);
infinitia_admin_header('Formations', 'courses');
?>
<div class="admin-page-heading"><div><span>Catalogue</span><h1>Formations</h1></div><?php if (!$show_form) { ?><a href="formations.php?action=create" class="btn admin-primary"><i class="bi bi-plus-lg"></i> Ajouter une formation</a><?php } ?></div>
<?php if ($show_form) { ?>
<div class="admin-panel mb-4"><h2><?php echo (int) $editing['id'] > 0 ? 'Modifier la formation' : 'Ajouter une formation'; ?></h2>
<?php if (count($errors) > 0) { ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error) { ?><li><?php echo infinitia_h($error); ?></li><?php } ?></ul></div><?php } ?>
<form method="post" action="formations.php<?php echo (int) $editing['id'] > 0 ? '?edit=' . (int) $editing['id'] : '?action=create'; ?>"><input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
<div class="row g-3">
<div class="col-lg-8"><label class="form-label" for="name">Nom de la formation *</label><input class="form-control" id="name" name="name" maxlength="190" required value="<?php echo infinitia_h($editing['name']); ?>"></div>
<div class="col-lg-4"><label class="form-label" for="code">Code *</label><input class="form-control" id="code" name="code" maxlength="80" required pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="<?php echo infinitia_h($editing['code']); ?>"<?php echo (int) $editing['id'] > 0 ? ' readonly' : ''; ?>></div>
<div class="col-md-6"><label class="form-label" for="category">Catégorie *</label><input class="form-control" id="category" name="category" maxlength="120" required value="<?php echo infinitia_h($editing['category']); ?>"></div>
<div class="col-md-3"><label class="form-label" for="duration">Durée *</label><input class="form-control" id="duration" name="duration" maxlength="100" required value="<?php echo infinitia_h($editing['duration']); ?>"></div>
<div class="col-md-3"><label class="form-label" for="display_order">Ordre *</label><input class="form-control" type="number" min="0" max="100000" id="display_order" name="display_order" required value="<?php echo infinitia_h($editing['display_order']); ?>"></div>
<div class="col-12"><label class="form-label" for="short_description">Description courte *</label><textarea class="form-control" id="short_description" name="short_description" maxlength="500" rows="3" required><?php echo infinitia_h($editing['short_description']); ?></textarea></div>
<div class="col-12"><label class="form-label" for="detailed_content">Contenu détaillé</label><textarea class="form-control" id="detailed_content" name="detailed_content" maxlength="5000" rows="6"><?php echo infinitia_h($editing['detailed_content']); ?></textarea><div class="form-text">Texte simple uniquement. Les retours à la ligne seront conservés.</div></div>
<div class="col-md-4"><label class="form-label" for="price">Tarif</label><input class="form-control" type="number" step="0.01" min="0" id="price" name="price" value="<?php echo infinitia_h($editing['price']); ?>"></div>
<div class="col-md-4"><label class="form-label" for="price_unit">Unité tarifaire</label><input class="form-control" id="price_unit" name="price_unit" maxlength="50" placeholder="/ mois" value="<?php echo infinitia_h($editing['price_unit']); ?>"></div>
<div class="col-md-4"><label class="form-label" for="is_active">Statut</label><select class="form-select" id="is_active" name="is_active"><option value="1"<?php echo (int) $editing['is_active'] === 1 ? ' selected' : ''; ?>>Active</option><option value="0"<?php echo (int) $editing['is_active'] === 0 ? ' selected' : ''; ?>>Inactive</option></select></div>
<div class="col-12 d-flex gap-2"><button class="btn admin-primary" type="submit">Enregistrer</button><a href="formations.php" class="btn btn-outline-secondary">Annuler</a></div>
</div></form></div>
<?php } ?>
<div class="admin-panel"><div class="table-responsive"><table class="table align-middle admin-table"><thead><tr><th>Ordre</th><th>Formation</th><th>Catégorie</th><th>Durée</th><th>Tarif</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($courses as $course) { ?><tr><td><?php echo (int) $course['display_order']; ?></td><td><strong><?php echo infinitia_h($course['name']); ?></strong><br><small><?php echo infinitia_h($course['code']); ?></small></td><td><?php echo infinitia_h($course['category']); ?></td><td><?php echo infinitia_h($course['duration']); ?></td><td><?php echo infinitia_h(infinitia_format_training_price($course['price'], $course['price_unit'])); ?></td><td><span class="admin-badge"><?php echo (int) $course['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span></td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="formations.php?edit=<?php echo (int) $course['id']; ?>">Modifier</a><form method="post" action="formations.php"><input type="hidden" name="csrf_token" value="<?php echo infinitia_h(infinitia_csrf_token()); ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>"><button class="btn btn-sm btn-outline-secondary" type="submit"><?php echo (int) $course['is_active'] === 1 ? 'Désactiver' : 'Activer'; ?></button></form></div></td></tr><?php } ?>
</tbody></table></div></div>
<?php infinitia_admin_footer(); ?>
