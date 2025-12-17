<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) session_start();
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
$form_errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['message'], $_SESSION['error'], $_SESSION['form_errors'], $_SESSION['form_data']);

$nomClient = $form_data['nomClient'] ?? ($reclamation['nomClient'] ?? '');
$emailClient = $form_data['emailClient'] ?? ($reclamation['emailClient'] ?? '');
$typeReclamation = $form_data['typeReclamation'] ?? ($reclamation['typeReclamation'] ?? '');
$titre = $form_data['titre'] ?? ($reclamation['titre'] ?? '');
$description = $form_data['description'] ?? ($reclamation['description'] ?? '');

require_once __DIR__ . '/../../config/paths.php';
require_once __DIR__ . '/../../config/i18n.php';

$lang = projetweb_get_lang();
$dir = projetweb_get_dir($lang);
$supportedLangs = projetweb_supported_languages();
$langUrl = function (string $nextLang): string {
    $params = $_GET;
    $params['lang'] = $nextLang;
    return 'index.php?' . http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES) ?>" dir="<?= htmlspecialchars($dir, ENT_QUOTES) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('app_name') . ' - ' . t('edit_title'), ENT_QUOTES) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/front.css') ?>">
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">
                        <img src="<?= asset('assets/img/logo-lv.png') ?>" alt="Logo LV" style="height:40px;">
                    </div>
                    <div class="site-title"><?= htmlspecialchars(t('app_name')) ?></div>
                </div>
            </div>

            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="../index.php" class="home-link"><?= htmlspecialchars(t('home')) ?></a></li>
                    <li><a href="index.php?action=front"><?= htmlspecialchars(t('new_claim')) ?></a></li>
                    <li><a href="index.php?action=front&method=historique"><?= htmlspecialchars(t('history')) ?></a></li>
                    <?php if (isset($reclamation) && $reclamation): ?>
                    <li><a href="index.php?action=front&method=edit&id=<?= $reclamation['id'] ?>" class="active"><?= htmlspecialchars(t('edit')) ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="nav-right">
                <div class="nav-actions">
                    <div class="lang-menu">
                        <button class="lang-menu-btn" type="button" aria-haspopup="true">
                            <span class="lang-globe">🌐</span>
                            <span class="lang-current"><?= htmlspecialchars($supportedLangs[$lang] ?? strtoupper($lang)) ?></span>
                            <span class="lang-caret">▼</span>
                        </button>
                        <div class="lang-dropdown" role="menu">
                            <ul class="lang-list">
                                <?php foreach ($supportedLangs as $code => $label): ?>
                                    <li class="lang-item">
                                        <a class="lang-link <?= $code === $lang ? 'active' : '' ?>" href="<?= htmlspecialchars($langUrl($code)) ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
                        $username = $isLoggedIn ? (string)($_SESSION['username'] ?? '') : 'Invité';
                        $role = $isLoggedIn ? strtolower(trim((string)($_SESSION['role'] ?? ''))) : '';
                        $profilePicture = $isLoggedIn ? (string)($_SESSION['profile_picture'] ?? '') : '';
                    ?>
                    <div class="user-menu">
                        <button class="user-profile-btn" type="button" aria-haspopup="true">
                            <div class="user-avatar">
                                <?php if ($profilePicture !== '' && file_exists(__DIR__ . '/../../../gaming_museum/uploads/' . $profilePicture)): ?>
                                    <img src="../gaming_museum/uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile">
                                <?php else: ?>
                                    <?php echo htmlspecialchars(strtoupper(substr($username, 0, 2))); ?>
                                <?php endif; ?>
                            </div>
                            <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                            <span class="dropdown-icon">▼</span>
                        </button>

                        <?php if ($isLoggedIn): ?>
                        <div class="user-dropdown">
                            <ul class="dropdown-menu-list">
                                <li class="dropdown-menu-item">
                                    <a href="../gaming_museum/view/frontoffice/profile.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">👤</span>
                                        <?= htmlspecialchars(t('my_profile')) ?>
                                    </a>
                                </li>
                                <?php if ($role === 'admin' || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])): ?>
                                <li class="dropdown-menu-item">
                                    <a href="../gaming_museum/view/backoffice/dashboard.php" class="dropdown-menu-link admin">
                                        <span class="dropdown-icon-left">⚙</span>
                                        <?= htmlspecialchars(t('admin_dashboard')) ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="dropdown-menu-item">
                                    <a href="index.php?action=front&method=logout" class="dropdown-menu-link logout">
                                        <span class="dropdown-icon-left">🚪</span>
                                        <?= htmlspecialchars(t('logout')) ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($role === 'admin' || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])): ?>
                        <a href="index.php?action=back" class="admin-btn">⚙ <?= htmlspecialchars(t('admin')) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <h1 class="page-title"><?= htmlspecialchars(t('edit_title')) ?></h1>
            
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="notification success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="notification error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="index.php?action=front&method=update&id=<?= $reclamation['id'] ?>" novalidate data-validate="reclamation">
                    <div class="form-group">
                        <label class="form-label" for="nomClient"><?= htmlspecialchars(t('name_label')) ?></label>
                        <input type="text" id="nomClient" name="nomClient" value="<?= htmlspecialchars($nomClient) ?>">
                        <?php if (isset($form_errors['nomClient'])): ?>
                            <div class="error-message is-visible" id="nomError"><?= htmlspecialchars($form_errors['nomClient']) ?></div>
                        <?php endif; ?>
                        <?php if (!isset($form_errors['nomClient'])): ?>
                            <div class="error-message" id="nomError"><?= htmlspecialchars(t('name_error')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="emailClient"><?= htmlspecialchars(t('email_label')) ?></label>
                        <input type="text" id="emailClient" name="emailClient" value="<?= htmlspecialchars($emailClient) ?>">
                        <?php if (isset($form_errors['emailClient'])): ?>
                            <div class="error-message is-visible" id="emailError"><?= htmlspecialchars($form_errors['emailClient']) ?></div>
                        <?php endif; ?>
                        <?php if (!isset($form_errors['emailClient'])): ?>
                            <div class="error-message" id="emailError"><?= htmlspecialchars(t('email_error')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="typeReclamation"><?= htmlspecialchars(t('type_label')) ?></label>
                        <select id="typeReclamation" name="typeReclamation">
                            <option value=""><?= htmlspecialchars(t('type_placeholder')) ?></option>
                            <option value="problème de commande" <?= $typeReclamation === 'problème de commande' ? 'selected' : '' ?>><?= htmlspecialchars(t('type_order_issue')) ?></option>
                            <option value="produit défectueux" <?= $typeReclamation === 'produit défectueux' ? 'selected' : '' ?>><?= htmlspecialchars(t('type_defective_product')) ?></option>
                            <option value="retard de livraison" <?= $typeReclamation === 'retard de livraison' ? 'selected' : '' ?>><?= htmlspecialchars(t('type_delivery_delay')) ?></option>
                            <option value="service client" <?= $typeReclamation === 'service client' ? 'selected' : '' ?>><?= htmlspecialchars(t('type_customer_service')) ?></option>
                            <option value="autre" <?= $typeReclamation === 'autre' ? 'selected' : '' ?>><?= htmlspecialchars(t('type_other')) ?></option>
                        </select>
                        <?php if (isset($form_errors['typeReclamation'])): ?>
                            <div class="error-message is-visible" id="typeError"><?= htmlspecialchars($form_errors['typeReclamation']) ?></div>
                        <?php endif; ?>
                        <?php if (!isset($form_errors['typeReclamation'])): ?>
                            <div class="error-message" id="typeError"><?= htmlspecialchars(t('type_error')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="titre"><?= htmlspecialchars(t('title_label')) ?></label>
                        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>">
                        <?php if (isset($form_errors['titre'])): ?>
                            <div class="error-message is-visible" id="titreError"><?= htmlspecialchars($form_errors['titre']) ?></div>
                        <?php endif; ?>
                        <?php if (!isset($form_errors['titre'])): ?>
                            <div class="error-message" id="titreError"><?= htmlspecialchars(t('title_error')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description"><?= htmlspecialchars(t('description_label')) ?></label>
                        <textarea id="description" name="description" rows="5"><?= htmlspecialchars($description) ?></textarea>
                        <?php if (isset($form_errors['description'])): ?>
                            <div class="error-message is-visible" id="descriptionError"><?= htmlspecialchars($form_errors['description']) ?></div>
                        <?php endif; ?>
                        <?php if (!isset($form_errors['description'])): ?>
                            <div class="error-message" id="descriptionError"><?= htmlspecialchars(t('description_error')) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-icon">✏️</span> <?= htmlspecialchars(t('save_changes')) ?>
                        </button>
                        <a href="index.php?action=front&method=details&id=<?= $reclamation['id'] ?>" class="btn btn-secondary">
                            <span class="btn-icon">↩️</span> <?= htmlspecialchars(t('cancel')) ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p><?= htmlspecialchars(t('footer_short')) ?></p>
        </div>
    </div>

    <div class="footer-bar-animation"></div>

    <script src="<?= asset('assets/js/forms-validation.js') ?>"></script>
</body>
</html>