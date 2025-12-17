<?php
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
    <title><?= htmlspecialchars(t('app_name') . ' - ' . t('submit_title'), ENT_QUOTES) ?></title>
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
                    <li><a href="index.php?action=front" class="active"><?= htmlspecialchars(t('new_claim')) ?></a></li>
                    <li><a href="index.php?action=front&method=historique"><?= htmlspecialchars(t('history')) ?></a></li>
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
            <h1 class="page-title"><?= htmlspecialchars(t('submit_title')) ?></h1>

            <div class="form-container">
                <div class="ai-assist-card" data-ai-assist data-ai-endpoint="<?= htmlspecialchars(url('api/ai-assist.php')) ?>" data-lang="<?= htmlspecialchars($lang) ?>">
                    <div class="ai-assist-header">
                        <h2 class="ai-assist-title"><?= htmlspecialchars(t('ai_title')) ?></h2>
                        <p class="ai-assist-subtitle"><?= htmlspecialchars(t('ai_subtitle')) ?></p>
                    </div>

                    <div class="ai-assist-body">
                        <textarea id="aiAssistInput" class="ai-assist-textarea" rows="4" placeholder="<?= htmlspecialchars(t('ai_placeholder'), ENT_QUOTES) ?>"></textarea>
                        <div class="ai-assist-actions">
                            <button type="button" class="btn btn-secondary ai-assist-btn" id="aiAssistBtn">
                                <?= htmlspecialchars(t('ai_button')) ?>
                            </button>
                            <div class="ai-assist-status" id="aiAssistStatus"
                                 data-loading="<?= htmlspecialchars(t('ai_loading'), ENT_QUOTES) ?>"
                                 data-done="<?= htmlspecialchars(t('ai_done'), ENT_QUOTES) ?>"
                                 data-error="<?= htmlspecialchars(t('ai_error'), ENT_QUOTES) ?>"
                                 data-min="<?= htmlspecialchars(t('ai_min'), ENT_QUOTES) ?>"></div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="index.php?action=front&method=add" id="reclamationForm" novalidate data-validate="reclamation">
                    <div class="form-group">
                        <label class="form-label"><?= htmlspecialchars(t('name_label')) ?></label>
                        <input type="text" name="nomClient" id="nomClient" placeholder="<?= htmlspecialchars(t('name_placeholder'), ENT_QUOTES) ?>" />
                        <div class="error-message" id="nomError"><?= htmlspecialchars(t('name_error')) ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= htmlspecialchars(t('email_label')) ?></label>
                        <input type="text" name="emailClient" id="emailClient" placeholder="<?= htmlspecialchars(t('email_placeholder'), ENT_QUOTES) ?>" />
                        <div class="error-message" id="emailError"><?= htmlspecialchars(t('email_error')) ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= htmlspecialchars(t('type_label')) ?></label>
                        <select name="typeReclamation" id="typeReclamation">
                            <option value=""><?= htmlspecialchars(t('type_placeholder')) ?></option>
                            <option value="problème de commande"><?= htmlspecialchars(t('type_order_issue')) ?></option>
                            <option value="produit défectueux"><?= htmlspecialchars(t('type_defective_product')) ?></option>
                            <option value="retard de livraison"><?= htmlspecialchars(t('type_delivery_delay')) ?></option>
                            <option value="service client"><?= htmlspecialchars(t('type_customer_service')) ?></option>
                            <option value="autre"><?= htmlspecialchars(t('type_other')) ?></option>
                        </select>
                        <div class="error-message" id="typeError"><?= htmlspecialchars(t('type_error')) ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= htmlspecialchars(t('title_label')) ?></label>
                        <input type="text" name="titre" id="titre" placeholder="<?= htmlspecialchars(t('title_placeholder'), ENT_QUOTES) ?>" />
                        <div class="error-message" id="titreError"><?= htmlspecialchars(t('title_error')) ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><?= htmlspecialchars(t('description_label')) ?></label>
                        <textarea name="description" id="description" placeholder="<?= htmlspecialchars(t('description_placeholder'), ENT_QUOTES) ?>"></textarea>
                        <div class="error-message" id="descriptionError"><?= htmlspecialchars(t('description_error')) ?></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?= htmlspecialchars(t('send')) ?></button>
                </form>
                
                <div class="form-footer">
                    <p><?= htmlspecialchars(t('already_submitted')) ?></p>
                    <a href="index.php?action=front&method=historique" class="btn btn-secondary">
                        <?= htmlspecialchars(t('see_history')) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p><?= htmlspecialchars(t('footer_text')) ?></p>
        </div>
    </div>

    <div class="footer-bar-animation"></div>
    
    <script src="<?= asset('assets/js/front.js') ?>"></script>
    <script src="<?= asset('assets/js/forms-validation.js') ?>"></script>
    <script src="<?= asset('assets/js/ai-assist.js') ?>"></script>
</body>
</html>