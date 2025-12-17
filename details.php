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

$translateType = function (string $typeRaw): string {
    $typeKeyByValue = [
        'problème de commande' => 'type_order_issue',
        'produit défectueux' => 'type_defective_product',
        'retard de livraison' => 'type_delivery_delay',
        'service client' => 'type_customer_service',
        'autre' => 'type_other',
    ];

    $normalized = strtolower(trim($typeRaw));
    foreach ($typeKeyByValue as $value => $key) {
        if ($normalized === strtolower($value)) {
            return t($key);
        }
    }
    return $typeRaw;
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES) ?>" dir="<?= htmlspecialchars($dir, ENT_QUOTES) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('app_name') . ' - ' . t('details_title'), ENT_QUOTES) ?></title>
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
                    <li><a href="index.php?action=front&method=details&id=<?= $reclamation['id'] ?>" class="active"><?= htmlspecialchars(t('details')) ?></a></li>
                    <li><a href="index.php?action=front&method=edit&id=<?= $reclamation['id'] ?>"><?= htmlspecialchars(t('edit')) ?></a></li>
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
            <?php if (isset($reclamation) && $reclamation): ?>
                <h1 class="page-title"><?= htmlspecialchars(t('details_title')) ?></h1>
                
                <div class="details-card">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><?= htmlspecialchars(t('date_time_label')) ?></span>
                            <div class="detail-value">
                                <?= isset($reclamation['date_creation']) ? date('d/m/Y', strtotime($reclamation['date_creation'])) . ' à ' . date('H:i:s', strtotime($reclamation['date_creation'])) : htmlspecialchars(t('datetime_unavailable')) ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label"><?= htmlspecialchars(t('client_label')) ?></span>
                            <div class="detail-value"><?= htmlspecialchars($reclamation['nomClient'] ?? '') ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label"><?= htmlspecialchars(t('email_short_label')) ?></span>
                            <div class="detail-value"><?= htmlspecialchars($reclamation['emailClient'] ?? '') ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label"><?= htmlspecialchars(t('type_short_label')) ?></span>
                            <div class="detail-value"><?= htmlspecialchars($translateType((string)($reclamation['typeReclamation'] ?? ''))) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label"><?= htmlspecialchars(t('status_label')) ?></span>
                            <div class="detail-value">
                                <span class="status <?= isset($reponse) && $reponse ? 'status-resolved' : 'status-pending' ?>">
                                    <?= isset($reponse) && $reponse ? htmlspecialchars(t('status_answered')) : htmlspecialchars(t('status_pending')) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-full">
                        <span class="detail-label"><?= htmlspecialchars(t('title_short_label')) ?></span>
                        <div class="detail-value detail-title"><?= htmlspecialchars($reclamation['titre'] ?? '') ?></div>
                    </div>
                    
                    <div class="detail-full">
                        <span class="detail-label"><?= htmlspecialchars(t('description_short_label')) ?></span>
                        <div class="detail-value detail-description">
                            <?= nl2br(htmlspecialchars($reclamation['description'] ?? '')) ?>
                        </div>
                    </div>
                </div>
                
                <div class="response-card <?= isset($reponse) && $reponse ? 'has-response' : 'no-response' ?>">
                    <?php if (isset($reponse) && $reponse): ?>
                        <div class="response-header">
                            <h3><?= htmlspecialchars(t('team_response_title')) ?></h3>
                            <div class="response-info">
                                <?= htmlspecialchars(t('sent_on')) ?> <strong><?= htmlspecialchars($reponse['dateReponse'] ?? '') ?></strong>
                                <?= htmlspecialchars(t('at_time')) ?> <strong><?= htmlspecialchars($reponse['heureReponse'] ?? '') ?></strong>
                                <?= htmlspecialchars(t('by')) ?> <strong><?= htmlspecialchars($reponse['adminName'] ?? t('administrator_fallback')) ?></strong>
                            </div>
                        </div>
                        
                        <div class="response-content">
                            <?= nl2br(htmlspecialchars($reponse['message'] ?? '')) ?>
                        </div>
                    <?php else: ?>
                        <div class="waiting-response">
                            <div class="waiting-icon">🕐</div>
                            <h3><?= htmlspecialchars(t('waiting_response_title')) ?></h3>
                            <p><?= htmlspecialchars(t('waiting_response_desc1')) ?></p>
                            <p><?= htmlspecialchars(t('waiting_response_desc2')) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="details-actions">
                    <a href="index.php?action=front" class="btn btn-primary">🏠 <?= htmlspecialchars(t('home')) ?></a>
                    <a href="index.php?action=front&method=historique" class="btn btn-secondary">📋 <?= htmlspecialchars(t('history')) ?></a>
                    <a href="index.php?action=front&method=edit&id=<?= $reclamation['id'] ?>" class="btn btn-secondary">✏️ <?= htmlspecialchars(t('edit')) ?></a>
                </div>
            <?php else: ?>
                <div class="not-found">
                    <h1>❌ <?= htmlspecialchars(t('not_found_title')) ?></h1>
                    <p><?= htmlspecialchars(t('not_found_desc')) ?></p>
                    <a href="index.php?action=front" class="btn btn-primary">🏠 <?= htmlspecialchars(t('back_home')) ?></a>
                    <a href="index.php?action=front&method=historique" class="btn btn-secondary">📋 <?= htmlspecialchars(t('see_history_short')) ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p><?= htmlspecialchars(t('footer_short')) ?></p>
        </div>
    </div>

    <div class="footer-bar-animation"></div>
</body>
</html>