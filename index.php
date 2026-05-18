<?php
session_start();
if (!isset($_SESSION['auth_user'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch Professional Analytics
$total_players = $conn->query("SELECT COUNT(*) as t FROM players")->fetch_assoc()['t'];
$total_goals = $conn->query("SELECT SUM(goals) as g FROM players")->fetch_assoc()['g'];
$avg_fitness = $conn->query("SELECT AVG(fitness) as f FROM players")->fetch_assoc()['f'];
$top_scorer = $conn->query("SELECT name, goals FROM players ORDER BY goals DESC LIMIT 1")->fetch_assoc();

// Search & Filter Logic
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$pos_filter = mysqli_real_escape_string($conn, $_GET['pos_filter'] ?? '');

$sql = "SELECT * FROM players WHERE name LIKE '%$search%'";
if($pos_filter != '') { 
    $sql .= " AND position = '$pos_filter'"; 
}
$sql .= " ORDER BY goals DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FC Manager | Pro Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2.0">
    
    <style>
        body {
            background: linear-gradient(rgba(10, 12, 22, 0.4), rgba(10, 12, 22, 0.65)), 
                        url('https://4kwallpapers.com/images/wallpapers/fc-barcelona-camp-3840x2160-19432.jpeg') no-repeat center center fixed !important;
            background-size: cover !important;
        }

        .main-content::before {
            background-image: repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(0, 255, 133, 0.01) 80px, rgba(0, 255, 133, 0.01) 160px) !important;
            background-color: transparent !important;
            opacity: 0.3;
        }

        .card, .stat-card {
            background: rgba(16, 22, 39, 0.70) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4) !important;
        }

        tr td {
            background: rgba(255, 255, 255, 0.01) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.03) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .sidebar {
            background: rgba(10, 12, 22, 0.6) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
        }
    </style>
</head>
<body>


<div class="sidebar">
    <h2>FC BARCELONA</h2>
    <a href="index.php" class="active"><span>📊</span> Dashboard</a>
    <a href="add_player.php"><span>⚽</span> Sign Player</a>
    <a href="add_manager.php"><span>📋</span> Managers and Others</a>
    <a href="add_match.php"><span>🏟️</span> Record Match</a>
    <a href="logout.php" style="margin-top: auto; color: var(--danger);"><span>🚪</span> Logout</a>
</div>

<div class="main-content">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'PlayerReleased'): ?>
        <div style="background: rgba(255, 71, 87, 0.1); border-left: 4px solid var(--danger); color: var(--danger); padding: 15px; border-radius: 12px; margin-bottom: 25px;">
            <strong>Contract Terminated:</strong> The player has been officially released from the squad.
        </div>
    <?php endif; ?>

    <header style="margin-bottom: 40px;">
        <h1 style="margin: 0; font-weight: 700; font-size: 2rem; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">FC BARCELONA PLAYER MANAGEMENT</h1>
        <p style="color: var(--text-muted); margin: 5px 0 0 0;">Manage player roster, track fitness, and analyze performance.</p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px;">
        <div class="card stat-card">
            <small style="color: var(--text-muted); font-weight: 600;">Total Player</small>
            <h2><?php echo $total_players; ?></h2>
        </div>
        <div class="card stat-card" style="border-bottom: 2px solid var(--pitch-green);">
            <small style="color: var(--text-muted); font-weight: 600;">Top Scorer</small>
            <h2 style="color: var(--pitch-green); text-shadow: 0 0 10px var(--neon-glow);"><?php echo $top_scorer['name'] ?? '---'; ?></h2>
            <small style="color: var(--pitch-green);"><?php echo $top_scorer['goals'] ?? 0; ?> Goals</small>
        </div>
        <div class="card stat-card">
            <small style="color: var(--text-muted); font-weight: 600;">Total Team Goals</small>
            <h2><?php echo $total_goals ?? 0; ?></h2>
        </div>
        <div class="card stat-card">
            <small style="color: var(--text-muted); font-weight: 600;">Avg Fitness</small>
            <h2><?php echo number_format($avg_fitness, 1); ?>%</h2>
        </div>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: center;">
            <input type="text" name="search" placeholder="Search squad..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 3; margin: 0; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px; border-radius: 8px;">
            <select name="pos_filter" style="flex: 1; margin: 0; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px; border-radius: 8px; outline: none; cursor: pointer;">
                <option value="" style="background: #101627;">All Positions</option>
                <option value="Goalkeeper" style="background: #101627;" <?php if($pos_filter=='Goalkeeper') echo 'selected'; ?>>Goalkeeper</option>
                <option value="Defender" style="background: #101627;" <?php if($pos_filter=='Defender') echo 'selected'; ?>>Defender</option>
                <option value="Midfielder" style="background: #101627;" <?php if($pos_filter=='Midfielder') echo 'selected'; ?>>Midfielder</option>
                <option value="Forward" style="background: #101627;" <?php if($pos_filter=='Forward') echo 'selected'; ?>>Forward</option>
            </select>
            <button type="submit" class="btn-primary" style="padding: 12px 25px;">Apply</button>
            <?php if($search || $pos_filter): ?>
                <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.8rem;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3 style="margin: 0 0 20px 0;">Active Roster</h3>
        <table>
            <thead>
                <tr>
                    <th>Jersey_number</th>
                    <th>Player</th>
                    <th>Goals</th>
                    <th>Fitness</th>
                    <th>Value</th>
                    <th>Management</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><span style="color: var(--pitch-green); font-weight: 800;">#<?php echo $row['jersey_number']; ?></span></td>
                        <td>
                            <strong style="font-size: 1rem; color: white;"><?php echo $row['name']; ?></strong><br>
                            <small style="color: var(--text-muted); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;"><?php echo $row['position']; ?></small>
                        </td>
                        <td>
                            <span style="font-size: 1.1rem; font-weight: 700; color: white;"><?php echo $row['goals']; ?></span>
                        </td>
                        <td>
                            <div class="progress-container" style="background: rgba(0,0,0,0.5);">
                                <div class="progress-bar" style="width: <?php echo $row['fitness']; ?>%; background: <?php echo ($row['fitness'] < 60) ? 'var(--danger)' : 'var(--pitch-green)'; ?>; box-shadow: 0 0 10px <?php echo ($row['fitness'] < 60) ? 'rgba(255,71,87,0.3)' : 'rgba(0,255,133,0.3)'; ?>;"></div>
                            </div>
                            <small style="font-size: 10px; color: var(--text-muted); display: block; margin-top: 4px;"><?php echo $row['fitness']; ?>%</small>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: white;">€<?php echo number_format($row['market_value'] / 1000000, 1); ?>M</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 15px;">
                                <a href="edit_player.php?id=<?php echo $row['id']; ?>" style="color: var(--pitch-green); text-decoration: none; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.5px;">EDIT</a>
                                <a href="delete_player.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Release <?php echo addslashes($row['name']); ?> from contract?');"
                                   style="color: var(--danger); text-decoration: none; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.5px;">RELEASE</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No players found in the registry.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>