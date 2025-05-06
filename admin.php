<?php
// admin.php
session_start();

// Simple authentication - GANTI PASSWORD INI
$admin_password = 'admin123';

// Check login
if (!isset($_SESSION['loggedin'])) {
    if ($_POST['password'] ?? '' === $admin_password) {
        $_SESSION['loggedin'] = true;
    } else {
        show_login_form();
        exit;
    }
}

// Handle logout
if ($_GET['action'] ?? '' === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle export to Excel
if ($_GET['action'] ?? '' === 'export') {
    export_to_excel();
    exit;
}

// Main admin dashboard
show_admin_dashboard();

// ===== FUNCTIONS ===== //

function export_to_excel() {
    $rsvp_data = load_rsvp_data();
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="rsvp_data_'.date('Y-m-d').'.xls"');
    
    echo '<table border="1">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kontak</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Ucapan</th>
            <th>Waktu</th>
        </tr>';
    
    foreach ($rsvp_data as $index => $rsvp) {
        echo '
        <tr>
            <td>'.($index + 1).'</td>
            <td>'.htmlspecialchars($rsvp['name']).'</td>
            <td>'.htmlspecialchars($rsvp['email']).'</td>
            <td>'.$rsvp['jumlah'].'</td>
            <td>'.ucfirst($rsvp['kehadiran']).'</td>
            <td>'.htmlspecialchars($rsvp['ucapan'] ?? '').'</td>
            <td>'.$rsvp['timestamp'].'</td>
        </tr>';
    }
    
    echo '</table>';
}

function show_login_form() {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; }
            .login-form { max-width: 400px; margin: 100px auto; padding: 20px; background: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Admin Login</h2>
            <form method="post">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    ';
}

function show_admin_dashboard() {
    $rsvp_data = load_rsvp_data();
    
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { padding: 20px; background-color: #f8f9fa; }
            .dashboard-header { margin-bottom: 30px; }
            .table-responsive { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
            .badge-attending { background-color: #28a745; }
            .badge-not-attending { background-color: #dc3545; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="dashboard-header d-flex justify-content-between align-items-center">
                <h1>RSVP Dashboard</h1>
                <div>
                    <a href="admin.php?action=export" class="btn btn-success">Export to Excel</a>
                    <a href="admin.php?action=logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Responses</h5>
                            <p class="card-text display-4">'.count($rsvp_data).'</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Attending</h5>
                            <p class="card-text display-4">'.count_attending($rsvp_data).'</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Not Attending</h5>
                            <p class="card-text display-4">'.count_not_attending($rsvp_data).'</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Ucapan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    foreach ($rsvp_data as $index => $rsvp) {
        echo '
                        <tr>
                            <td>'.($index + 1).'</td>
                            <td>'.htmlspecialchars($rsvp['name']).'</td>
                            <td>'.htmlspecialchars($rsvp['email']).'</td>
                            <td>'.$rsvp['jumlah'].'</td>
                            <td><span class="badge '.($rsvp['kehadiran'] === 'hadir' ? 'badge-attending' : 'badge-not-attending').'">'.ucfirst($rsvp['kehadiran']).'</span></td>
                            <td>'.nl2br(htmlspecialchars($rsvp['ucapan'] ?? '')).'</td>
                            <td>'.$rsvp['timestamp'].'</td>
                        </tr>';
    }
    
    echo '
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>
    ';
}

function load_rsvp_data() {
    $filename = 'rsvp_data.json';
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true) ?: [];
    }
    return [];
}

function count_attending($data) {
    return count(array_filter($data, function($item) {
        return $item['kehadiran'] === 'hadir';
    }));
}

function count_not_attending($data) {
    return count(array_filter($data, function($item) {
        return $item['kehadiran'] === 'tidak-hadir';
    }));
}


?>