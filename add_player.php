<?php
include 'db.php';

$error_msg = ""; //if a number is taken

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $pos = $_POST['pos'];
    $num = (int)$_POST['num'];
    $val = (float)$_POST['val']; 
    
    //check same jersynumber
    $check_stmt = $conn->prepare("SELECT id FROM players WHERE jersey_number = ?");
    $check_stmt->bind_param("i", $num);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // number is already taken
        $error_msg = "Jersey #$num is already assigned to another active squad member!";
    } else {
        $stmt = $conn->prepare("INSERT INTO players (name, position, jersey_number, market_value, fitness) VALUES (?, ?, ?, ?, 100)");
        $stmt->bind_param("ssid", $name, $pos, $num, $val);
        
        if($stmt->execute()) {
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign New Player | FC Manager Pro</title>
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
            background: rgba(255, 71, 87, 0.1); 
            border-left: 4px solid var(--danger, #ff4757); 
            color: var(--danger, #ff4757); 
            padding: 15px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>FC MANAGER</h2>
    <a href="index.php">Dashboard</a>
    <a href="add_player.php" class="active">Sign Player</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>TRANSFER MARKET</h2>
            <p style="color: #888;">Acquire fresh talent for your active squad.</p>
        </div>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="background: #161b22; padding: 30px; border-radius: 15px;">
            <form method="POST">
                <div class="input-group">
                    <label class="field-label">Full Name</label>
                    <input type="text" name="name" placeholder="e.g. Erling Haaland" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required 
                           style="width:100%; padding: 12px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                </div>

                <div class="input-group">
                    <label class="field-label">Position on Pitch</label>
                    <div class="pos-grid">
                        <label>
                            <input type="radio" name="pos" value="Goalkeeper" <?php echo (isset($_POST['pos']) && $_POST['pos'] == 'Goalkeeper') ? 'checked' : ''; ?> required>
                            <div class="pos-option">GK</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Defender" <?php echo (isset($_POST['pos']) && $_POST['pos'] == 'Defender') ? 'checked' : ''; ?>>
                            <div class="pos-option">DEF</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Midfielder" <?php echo (isset($_POST['pos']) && $_POST['pos'] == 'Midfielder') ? 'checked' : ''; ?>>
                            <div class="pos-option">MID</div>
                        </label>
                        <label>
                            <input type="radio" name="pos" value="Forward" <?php echo (isset($_POST['pos']) && $_POST['pos'] == 'Forward') ? 'checked' : ''; ?>>
                            <div class="pos-option">FWD</div>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 24px;">
                    <div class="input-group" style="flex: 1;">
                        <label class="field-label">Jersey Number</label>
                        <input type="number" name="num" placeholder="9" min="1" max="99" value="<?php echo isset($_POST['num']) ? (int)$_POST['num'] : ''; ?>" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label class="field-label">Market Value (€)</label>
                        <input type="number" name="val" placeholder="75000000" value="<?php echo isset($_POST['val']) ? htmlspecialchars($_POST['val']) : ''; ?>" required
                               style="width:90%; padding: 10px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" name="submit" class="btn-primary" 
                            style="width: 100%; padding: 15px; background: #00ff85; color: #000; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem;">
                        HERE WE GO!!
                    </button>
                </div>
            </form>
        </div>
        
        <p style="text-align: center; color: #888; font-size: 0.8rem; margin-top: 20px;">
            * New players are assigned 100% fitness by default.
        </p>
    </div>
</div>

</body>
</html>