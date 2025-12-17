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
    <title><?= htmlspecialchars(t('app_name') . ' - ' . t('history_title'), ENT_QUOTES) ?></title>
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
                    <li><a href="index.php?action=front&method=historique" class="active"><?= htmlspecialchars(t('history')) ?></a></li>
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
            <h1 class="page-title"><?= htmlspecialchars(t('history_title')) ?></h1>
            
            <?php if (empty($reclamations)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2><?= htmlspecialchars(t('empty_title')) ?></h2>
                    <p><?= htmlspecialchars(t('empty_desc')) ?></p>
                </div>
            <?php else: ?>
                <div class="reclamations-grid">
                    <?php foreach ($reclamations as $r): 
                        $reponse = null;
                        if (isset($reponses) && is_array($reponses)) {
                            foreach ($reponses as $rep) {
                                if (isset($rep['reclamationId']) && $rep['reclamationId'] == $r['id']) {
                                    $reponse = $rep;
                                    break;
                                }
                            }
                        }
                        $status = $reponse ? t('status_answered') : t('status_pending');
                        $statusClass = $reponse ? 'status-resolved' : 'status-pending';
                    ?>
                    <div class="reclamation-card">
                        <div class="card-header">
                            <h3><?= htmlspecialchars($r['titre'] ?? '') ?></h3>
                            <span class="status <?= $statusClass ?>"><?= $status ?></span>
                        </div>
                        
                        <div class="card-date">
                            <?= isset($r['date_creation']) ? date('d/m/Y à H:i', strtotime($r['date_creation'])) : htmlspecialchars(t('unknown_date')) ?>
                        </div>
                        
                        <div class="card-info">
                            <div><strong><?= htmlspecialchars(t('client_label')) ?> :</strong> <?= htmlspecialchars($r['nomClient'] ?? '') ?></div>
                            <div><strong><?= htmlspecialchars(t('type_short_label')) ?> :</strong> <?= htmlspecialchars($translateType((string)($r['typeReclamation'] ?? ''))) ?></div>
                        </div>
                        
                        <div class="card-description">
                            <?= nl2br(htmlspecialchars(substr($r['description'] ?? '', 0, 200))) ?>
                            <?= strlen($r['description'] ?? '') > 200 ? '...' : '' ?>
                        </div>
                        
                        <div class="card-footer">
                            <?php if ($reponse): ?>
                                <span class="response-received">
                                    ✅ <?= htmlspecialchars(t('response_received_on')) ?> <?= htmlspecialchars($reponse['dateReponse'] ?? '') ?>
                                </span>
                            <?php else: ?>
                                <span class="response-pending">
                                    ⏳ <?= htmlspecialchars(t('waiting_response_short')) ?>
                                </span>
                            <?php endif; ?>
                            
                            <a href="index.php?action=front&method=details&id=<?= $r['id'] ?>" class="btn btn-small">
                                👁️ <?= htmlspecialchars(t('view_details')) ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="reclamations-count">
                    <?= htmlspecialchars(t('total')) ?> : <?= count($reclamations) ?> <?= htmlspecialchars(count($reclamations) > 1 ? t('claim_plural') : t('claim_singular')) ?>
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