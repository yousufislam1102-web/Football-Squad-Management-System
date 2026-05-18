<?php
session_start();
if (!isset($_SESSION['auth_user'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$error_msg = "";

// UPDATE 
if (isset($_POST['update_player'])) {
    $name = $_POST['name'];
    $pos = $_POST['pos'];
    $num = (int)$_POST['num'];
    $val = (float)$_POST['val'];
    $goals = (int)$_POST['goals'];
    $fitness = (int)$_POST['fitness'];

    $check_stmt = $conn->prepare("SELECT id FROM players WHERE jersey_number = ? AND id != ?");
    $check_stmt->bind_param("ii", $num, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_msg = "Jersey #$num is already assigned to another active squad member!";
    } else {
        $stmt = $conn->prepare("UPDATE players SET name = ?, position = ?, jersey_number = ?, market_value = ?, goals = ?, fitness = ? WHERE id = ?");
        $stmt->bind_param("ssidiid", $name, $pos, $num, $val, $goals, $fitness, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error_msg = "Error executing database updates: " . $conn->error;
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM players WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// If the requested player ID does not exist, return to dashboard 
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$player = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Player Profile | FC Manager Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container { max-width: 600px; margin: 40px auto; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .input-group { margin-bottom: 20px; }
        label.field-label { display: block; color: var(--text-muted, #ccc); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 600; }
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h2 { font-size: 2rem; color: #00ff85; margin: 0; }
        .pos-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 20px; }
        .pos-grid input[type="radio"] { display: none; }
        .pos-option { background: #0f111a; border: 1px solid #2d313d; padding: 15px; border-radius: 10px; text-align: center; cursor: pointer; transition: all 0.3s ease; color: #fff; font-weight: 600; }
        .pos-option:hover { border-color: #00ff85; }
        input[type="radio"]:checked + .pos-option { background: rgba(0, 255, 133, 0.2); border-color: #00ff85; color: #00ff85; box-shadow: 0 0 15px rgba(0, 255, 133, 0.3); }
        
        .alert-danger {
            background: rgba(255, 71, 87, 0.1); border-left: 4px solid var(--danger, #ff4757); color: var(--danger, #ff4757); 
            padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; font-weight: 600;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>FC MANAGER</h2>
    <a href="index.php">Dashboard</a>
    <a href="add_player.php">Sign Player</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>EDIT PLAYER PROFILE</h2>
            <p style="color: #888;">Update information and performance values for <?php echo htmlspecialchars($player['name']); ?>.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-danger">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="background: #161b22; padding: 30px; border-radius: 15px;">
            <form method="POST">
                <div class="input-group">
                    <label class="field-label">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($player['name']); ?>" required 
                           style="width:100%; padding: 12px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                </div>

                <div class="input-group">
                    <label class="field-label">Position on Pitch</label>
                    <div class="pos-grid">
                        <label>
                            <input type="radio" name="pos" value="Goalkeeper" <?php echo ($player['position'] === 'Goalkeeper') ? 'checked' : ''; ?> required>
                            <div class="pos-option">GK</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Defender" <?php echo ($player['position'] === 'Defender') ? 'checked' : ''; ?>>
                            <div class="pos-option">DEF</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Midfielder" <?php echo ($player['position'] === 'Midfielder') ? 'checked' : ''; ?>>
                            <div class="pos-option">MID</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Forward" <?php echo ($player['position'] === 'Forward') ? 'checked' : ''; ?>>
                            <div class="pos-option">FWD</div>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 24px;">
                    <div class="input-group" style="flex: 1;">
                        <label class="field-label">Jersey Number</label>
                        <input type="number" name="num" value="<?php echo (int)$player['jersey_number']; ?>" min="1" max="99" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label class="field-label">Market Value (€)</label>
                        <input type="number" name="val" value="<?php echo (float)$player['market_value']; ?>" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="input-group">
                        <label class="field-label">Goals Scored</label>
                        <input type="number" name="goals" value="<?php echo (int)$player['goals']; ?>" min="0" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                    <div class="input-group">
                        <label class="field-label">Fitness Level (%)</label>
                        <input type="number" name="fitness" value="<?php echo (int)$player['fitness']; ?>" min="0" max="100" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <a href="index.php" style="flex: 1; padding: 15px; background: #30363d; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; text-align: center; font-size: 1rem;">
                        CANCEL
                    </a>
                    <button type="submit" name="update_player" class="btn-primary" 
                            style="flex: 2; padding: 15px; background: #00ff85; color: #000; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem;">
                        SAVE CHANGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>