<?php
include 'db.php';

if (isset($_POST['save_match'])) {
    $opp = $_POST['opp'];
    $our = (int)$_POST['our'];
    $them = (int)$_POST['them'];
    $date = $_POST['date'];
    
    //result
    $res = ($our > $them) ? 'Win' : (($our == $them) ? 'Draw' : 'Loss');

    $stmt = $conn->prepare("INSERT INTO matches (opponent, our_score, opp_score, match_date, result) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiss", $opp, $our, $them, $date, $res);
    $stmt->execute();

    // Update Player Goals 
    if (!empty($_POST['scorers'])) {
        $updateStmt = $conn->prepare("UPDATE players SET goals = goals + 1 WHERE id = ?");
        foreach ($_POST['scorers'] as $pid) {
            $updateStmt->bind_param("i", $pid);
            $updateStmt->execute();
        }
    }
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Record Match - FC BARCELONA</title>
    <style>
        body {
            background: linear-gradient(rgba(10, 12, 22, 0.5), rgba(10, 12, 22, 0.75)), 
                        url('https://4kwallpapers.com/images/wallpapers/fc-barcelona-camp-3840x2160-19432.jpeg') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        .sidebar {
            background: rgba(10, 12, 22, 0.65) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        .scroller-box input[type='checkbox'] {
            accent-color: #00ff85;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }       
        .scroller-box label:hover {
            color: #00ff85 !important;
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>FC BARCELONA</h2>
        <a href="index.php" style="color: #fff; text-decoration: none; opacity: 0.8;">← Back</a>
    </div>

    <div class="main-content">
        <div class="card" style="max-width:500px; margin: 40px auto; padding: 25px; background: rgba(26, 26, 26, 0.85); backdrop-filter: blur(12px); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.05);">
            <h2 style="color: #fff; margin-bottom: 25px; font-family: sans-serif; font-weight: 700; text-transform: uppercase; font-size: 1.4rem; letter-spacing: 0.5px;">Record Match Result</h2>
            
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="color: #ccc; display: block; margin-bottom: 5px; font-family: sans-serif;">Opponent</label>
                    <input type="text" name="opp" placeholder="Opponent Name" required 
                           style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; outline: none;">
                </div>

                <div style="display:flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex:1;">
                        <label style="color: #ccc; display: block; margin-bottom: 5px; font-family: sans-serif;">Our Score</label>
                        <input type="number" name="our" value="0" min="0" required 
                               style="width:100%; padding: 5px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; outline: none;">
                    </div>
                    <div style="flex:1;">
                        <label style="color: #ccc; display: block; margin-bottom: 5px; font-family: sans-serif;">Their Score</label>
                        <input type="number" name="them" value="0" min="0" required 
                               style="width:100%; padding: 5px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; outline: none;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="color: #ccc; display: block; margin-bottom: 5px; font-family: sans-serif;">Match Date</label>
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required 
                           style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; outline: none;">
                </div>

                <h4 style="color: #fff; margin: 20px 0 10px 0; font-family: sans-serif; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Select Goal Scorers:</h4>
                
                <div class="scroller-box" style="max-height:180px; overflow-y:auto; border:1px solid rgba(255,255,255,0.1); padding:12px; border-radius:8px; 
                     background: linear-gradient(rgba(15, 17, 26, 0.75), rgba(15, 17, 26, 0.85)), url('https://4kwallpapers.com/images/wallpapers/fc-barcelona-camp-3840x2160-19432.jpeg') no-repeat center center; background-size: cover;">
                    <?php
                    $players = $conn->query("SELECT id, name FROM players ORDER BY name ASC");
                    if($players && $players->num_rows > 0) {
                        while($p = $players->fetch_assoc()) {
                            echo "
                            <label style='display:flex; align-items:center; gap:12px; padding: 6px 8px; border-radius: 4px; margin: 4px 0; color: #ffffff; cursor:pointer; font-weight: 500; font-family: sans-serif; font-size: 0.9rem;'>
                                <input type='checkbox' name='scorers[]' value='{$p['id']}'> 
                                <span>" . htmlspecialchars($p['name']) . "</span>
                            </label>";
                        }
                    } else {
                        echo "<p style='color: #94a3b8; padding: 10px; font-family: sans-serif;'>No players found in database.</p>";
                    }
                    ?>
                </div>
                
                <button type="submit" name="save_match" class="btn btn-add" 
                        style="width:100%; margin-top:25px; padding: 15px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; background: #00ff85; color: #000; text-transform: uppercase; letter-spacing: 0.5px;">
                    Save Result
                </button>
            </form>
        </div>
    </div>
</body>
</html>