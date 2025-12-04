<?php
include_once '../config/database.php';
include_once '../models/Evenement.php';
include_once '../models/Participation.php';

$database = new Database();
$db = $database->getConnection();

$evenement = new Evenement($db);
$participation = new Participation($db);

$totalEvenements = $evenement->read()->rowCount();
$totalParticipations = $participation->read()->rowCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #00FF41;
            --secondary-purple: #BD00FF;
            --accent-pink: #FF006E;
            --warning-orange: #FF9500;
            --dark-bg: #0a0a0a;
            --darker-bg: #050505;
            --card-bg: #1a1a1a;
            --sidebar-bg: #0d0d0d;
            --text-white: #ffffff;
            --text-gray: #888888;
            --text-light-gray: #aaaaaa;
            --border-color: #333333;
            --success-green: #00FF41;
            --danger-red: #FF0055;
        }

        body {
            font-family: 'Press Start 2P', cursive;
            background-color: var(--dark-bg);
            background-image: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 65, 0.02) 2px,
                    rgba(0, 255, 65, 0.02) 4px
                );
            color: var(--text-white);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 0;
            padding: 2rem;
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--sidebar-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 30px rgba(0, 255, 65, 0.2);
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .page-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-view-site {
            padding: 0.8rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-green), #00cc33);
            color: var(--darker-bg);
            border: none;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-view-site:hover {
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--primary-green);
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 65, 0.3);
        }

        .stat-card h3 {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 3rem;
            color: var(--primary-green);
            text-shadow: 0 0 15px var(--primary-green);
            margin-bottom: 1.5rem;
            font-family: 'VT323', monospace;
        }

        .stat-link {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.6rem;
            transition: all 0.3s;
        }

        .stat-link:hover {
            background-color: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
        }

        /* Recent Actions */
        .recent-actions {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .table-container {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 2px solid var(--border-color);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: rgba(0, 255, 65, 0.1);
            border-bottom: 2px solid var(--primary-green);
        }

        th {
            padding: 1.5rem 1rem;
            text-align: left;
            font-size: 0.6rem;
            color: var(--primary-green);
        }

        td {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.7rem;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background-color: rgba(0, 255, 65, 0.05);
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            font-size: 0.5rem;
            text-decoration: none;
            border: 2px solid;
            transition: all 0.3s;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .btn-edit {
            background: transparent;
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .btn-edit:hover {
            background-color: var(--primary-green);
            color: var(--darker-bg);
        }

        .btn-delete {
            background: transparent;
            border-color: var(--danger-red);
            color: var(--danger-red);
        }

        .btn-delete:hover {
            background-color: var(--danger-red);
            color: var(--text-white);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .top-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .top-bar-left,
            .top-bar-right {
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 0.8rem;
            }

            table {
                font-size: 0.5rem;
            }

            th, td {
                padding: 0.8rem 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 0.5rem;
            }

            .btn-view-site {
                display: none;
            }

            .stat-number {
                font-size: 2rem;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker-bg);
            border-left: 1px solid var(--border-color);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--primary-green), var(--secondary-purple));
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00cc33, #9900cc);
        }
    </style>
</head>
<body>
    <?php include_once 'views/back/header.php'; ?>

    <div class="main-content">
        <div class="top-bar">
            <div class="top-bar-left">
                <h1 class="page-title">TABLEAU DE BORD</h1>
            </div>
            <div class="top-bar-right">
                <a href="../index.php" class="btn-view-site">Voir le Site</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>ÉVÉNEMENTS</h3>
                <div class="stat-number"><?php echo $totalEvenements; ?></div>
                <a href="evenements.php" class="stat-link">GÉRER LES ÉVÉNEMENTS</a>
            </div>
            <div class="stat-card">
                <h3>PARTICIPATIONS</h3>
                <div class="stat-number"><?php echo $totalParticipations; ?></div>
                <a href="participations.php" class="stat-link">VOIR LES INSCRIPTIONS</a>
            </div>
        </div>

        <div class="recent-actions">
            <h2 class="section-title">ÉVÉNEMENTS RÉCENTS</h2>
            <?php
            $stmt = $evenement->read();
            $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>NOM</th>
                            <th>JEU</th>
                            <th>DATE</th>
                            <th>LIEU</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice($evenements, 0, 5) as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['nom']); ?></td>
                            <td><?php echo htmlspecialchars($event['jeu']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($event['date_debut'])); ?></td>
                            <td><?php echo htmlspecialchars($event['lieu']); ?></td>
                            <td>
                                <a href="evenements/edit.php?id=<?php echo $event['id_evenement']; ?>" class="btn-edit">MODIFIER</a>
                                <a href="evenements/delete.php?id=<?php echo $event['id_evenement']; ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr ?')">SUPPRIMER</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include_once 'views/back/footer.php'; ?>
</body>
</html>