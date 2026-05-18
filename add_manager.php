<?php
include 'db.php';

if(isset($_POST['save_manager'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $exp = (int)$_POST['exp'];

    
    $stmt = $conn->prepare("INSERT INTO managers (name, role, experience) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $role, $exp);
    
    if($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Manage Coaching Staff | FC Manager</title>
</head>
<body>
    <div class="sidebar">
        <h2>FC MANAGER</h2>
        <a href="index.php">Dashboard</a>
        <a href="add_manager.php" class="active">Manage Staff</a>
    </div>

    <div class="main-content">
        <div class="card" style="max-width:500px; margin: 40px auto;">
            <h2 style="color: var(--pitch-green); margin-bottom: 20px;">Managerial Contract</h2>
            
            <form method="POST">
                <div class="input-group">
                    <label style="color: var(--text-muted); font-size: 0.8rem; font-weight: bold;">MANAGER/OTHERS FULL NAME</label>
                    <input type="text" name="name" placeholder="e.g. Pep Guardiola" required 
                           style="width:100%; padding: 12px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px; margin-top: 5px;">
                </div>

                <div style="margin-top: 15px;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; font-weight: bold;">OFFICIAL ROLE</label>
                    <select name="role" style="width:100%; padding: 12px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px; margin-top: 5px;">
                        <option value="Head Coach">Head Coach</option>
                        <option value="Assistant Manager">Assistant Manager</option>
                        <option value="Tactical Analyst">Tactical Analyst</option>
                        <option value="Scout">Chief Scout</option>
                    </select>
                </div>

                <div style="margin-top: 15px;">
                    <label style="color: var(--text-muted); font-size: 0.8rem; font-weight: bold;">YEARS OF EXPERIENCE</label>
                    <input type="number" name="exp" placeholder="5" min="0" required
                           style="width:100%; padding: 12px; background: #0d1117; border: 1px solid #30363d; color: white; border-radius: 6px; margin-top: 5px;">
                </div>

                <button type="submit" name="save_manager" class="btn-primary" style="width: 100%; margin-top: 25px;">
                    HERE WE GO!!
                </button>
            </form>
        </div>

        <div class="card" style="max-width:500px; margin: 20px auto; background: rgba(0,0,0,0.3);">
            <h3 style="margin-bottom: 15px;">Current Staff<span style="float: right;">Experience</span></h3> 
    
            <table style="width: 100%; color: white;">
                <?php
                $staff = $conn->query("SELECT * FROM managers");
                while($m = $staff->fetch_assoc()) {
                    echo "<tr>
                            <td style='padding: 10px; border-bottom: 1px solid #333;'>
                                <strong>{$m['name']}</strong><br>
                                <small style='color: var(--pitch-green);'>{$m['role']}</small>
                            </td>
                            <td style='text-align: right; border-bottom: 1px solid #333;'>
                                {$m['experience']} Yrs
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>