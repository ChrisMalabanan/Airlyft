<?php
session_start();
require_once("config.php");

if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

$tables = ['users', 'addresses', 'booking', 'lift', 'passengers', 'payments', 'places', 'schedule'];
$selected_table = isset($_GET['table']) ? $_GET['table'] : $tables[0];

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get latest backup file
$last_backup_time = "No backup yet";
$backup_dir = __DIR__ . "/backups";

if (is_dir($backup_dir)) {
    $files = glob("$backup_dir/backup_airlyft_*.sql");
    if (!empty($files)) {
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a); // Newest first
        });
        $latest_file = $files[0];
        $timestamp = filemtime($latest_file);
        $last_backup_time = date("F j, Y \\a\\t h:i A", $timestamp);
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h4 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .header {
            background: #343a40;
            color: white;
            padding: 15px;
            border-radius: 6px 6px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-sm {
            font-size: 0.85rem;
        }
        .table-container {
            margin-bottom: 40px;
        }
		.sidebar-link {
			display: flex;
			align-items: center;
			color: white;
			padding: 10px 20px;
			transition: background-color 0.2s ease-in-out;
		}

		.sidebar-link:hover {
			background-color: #495057;
			text-decoration: none;
		}

		.sidebar-link.active {
			background-color: #0d6efd;
			font-weight: bold;
			border-left: 4px solid #ffffff;
		}
		/* Collapsed Sidebar */
		body.sidebar-collapsed .sidebar {
			width: 70px;
		}

		body.sidebar-collapsed .main-content {
			margin-left: 70px;
		}

		/* Hide text labels when collapsed */
		body.sidebar-collapsed .sidebar-link {
			justify-content: center;
		}

		body.sidebar-collapsed .sidebar-link i {
			margin-right: 0;
		}

		body.sidebar-collapsed .sidebar-link::after {
			display: none;
		}

		body.sidebar-collapsed .sidebar p,
		body.sidebar-collapsed .sidebar h4,
		body.sidebar-collapsed .sidebar form,
		body.sidebar-collapsed .sidebar label,
		body.sidebar-collapsed .sidebar hr,
		body.sidebar-collapsed .sidebar .btn,
		body.sidebar-collapsed .sidebar-link span {
			display: none !important;
		}


    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Admin Panel</h4>
    <hr class="text-white">
	
	<a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
		<i class="bi bi-tools me-2"></i>
		<span>Back-up and Restore</span>
	</a>

    <hr class="text-white my-3">

    <!-- 📂 TABLES SECTION -->
    <div class="px-3">
        <p class="text-white fw-bold small mb-1">DATABASE TABLES</p>
        <?php
        $icons = [
            'users' => 'bi-person',
            'addresses' => 'bi-geo-alt',
            'booking' => 'bi-journal-bookmark',
            'lift' => 'bi-airplane',
            'passengers' => 'bi-people',
            'payments' => 'bi-credit-card',
            'places' => 'bi-geo',
            'schedule' => 'bi-calendar-week'
        ];
        ?>

        <?php foreach ($tables as $table): ?>
            <a href="?table=<?php echo urlencode($table); ?>" class="sidebar-link <?php echo $selected_table === $table ? 'active' : ''; ?>">
                <i class="bi <?php echo $icons[$table]; ?> me-2"></i>
                <span><?php echo ucfirst($table); ?></span>

            </a>
        <?php endforeach; ?>
    </div>

    <hr class="text-white my-3">

    <!-- 🚪 LOGOUT -->
    <div class="px-3">
        <a href="?logout=true" class="btn btn-danger btn-sm w-100">Logout</a>
    </div>
</div>


<!-- Main Content -->
<div class="main-content">
	<button id="toggleSidebar" class="btn btn-outline-secondary btn-sm mb-3">
		<i class="bi bi-list"></i>
	</button>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
	
	

    <div class="table-container">
        <div class="container bg-white shadow rounded">
            <div class="header">
                <h2><?php echo ucfirst($selected_table); ?></h2>
				<a href="add_record.php?table=<?php echo urlencode($selected_table); ?>" class="btn btn-success btn-sm">➕ Add New</a>

            </div>

            <?php
            try {
                $stmt = $db->query("SHOW COLUMNS FROM `$selected_table`");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error fetching columns: " . $e->getMessage() . "</div>";
                $columns = [];
            }
            ?>

            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?php echo htmlspecialchars($column['Field']); ?></th>
                        <?php endforeach; ?>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $db->query("SELECT * FROM `$selected_table`");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            foreach ($row as $key => $value) {
                                if ($selected_table === 'users' && $key === 'password') {
                                    echo "<td>••••••••</td>";
                                } else {
                                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                                }
                            }
                            $primary_key = $columns[0]['Field'];
                            $delete_url = "delete_record.php?table=" . urlencode($selected_table) . "&id=" . urlencode($row[$primary_key]) . "&pk=" . urlencode($primary_key);
							$edit_url = "edit_record.php?table=" . urlencode($selected_table) . "&id=" . urlencode($row[$primary_key]) . "&pk=" . urlencode($primary_key);

                            echo "<td>
                                <a href='$delete_url' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record from $selected_table?');\">Delete</a>
								<a href='$edit_url' class='btn btn-primary btn-sm me-1'>Edit</a>
								
                            </td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='" . (count($columns) + 1) . "'>Error fetching data: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-collapsed");
    });
});
</script>
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
		<h5 class="modal-title" id="maintenanceModalLabel">
			Database Maintenance
			<div class="fs-6 text-light mt-1">
				Last Backup: <strong><?php echo $last_backup_time; ?></strong>
			</div>
		</h5>

        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Backup Button -->
		<button id="backupBtn" class="btn btn-primary w-100 mb-3">
			<i class="bi bi-hdd me-1"></i> Backup Database
		</button>
		<div id="backupStatus" class="text-center small mt-2"></div>


        <!-- Restore Form -->
        <form action="restore.php" method="POST" enctype="multipart/form-data">
          <label class="form-label small">Restore SQL File</label>
          <input type="file" name="sql_file" class="form-control form-control-sm mb-2" required>
          <button type="submit" class="btn btn-warning w-100">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Backup
          </button>
        </form>

      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	const toggleBtn = document.getElementById("toggleSidebar");
	if (toggleBtn) {
		toggleBtn.addEventListener("click", () => {
			document.body.classList.toggle("sidebar-collapsed");

			// Optional: remember sidebar state
			localStorage.setItem("sidebar-collapsed", document.body.classList.contains("sidebar-collapsed"));
		});

		// Restore sidebar state on load
		if (localStorage.getItem("sidebar-collapsed") === "true") {
			document.body.classList.add("sidebar-collapsed");
		}
	}

	const backupBtn = document.getElementById("backupBtn");
	const statusDiv = document.getElementById("backupStatus");
	const lastBackupText = document.querySelector("#maintenanceModalLabel strong");

	if (backupBtn) {
		backupBtn.addEventListener("click", () => {
			backupBtn.disabled = true;
			backupBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Backing up...';

			fetch('backup.php', {
				method: 'GET',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(res => res.json())
			.then(data => {
				if (data.status === 'success') {
					if (statusDiv) statusDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
					if (lastBackupText) lastBackupText.textContent = data.time;
				} else {
					if (statusDiv) statusDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
				}
			})
			.catch(() => {
				if (statusDiv) statusDiv.innerHTML = `<span class="text-danger">Something went wrong.</span>`;
			})
			.finally(() => {
				backupBtn.disabled = false;
				backupBtn.innerHTML = `<i class="bi bi-hdd me-1"></i> Backup Database`;
			});
		});
	}
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	const toggleBtn = document.getElementById("toggleSidebar");
	if (toggleBtn) {
		toggleBtn.addEventListener("click", () => {
			document.body.classList.toggle("sidebar-collapsed");

			// Optional: remember sidebar state
			localStorage.setItem("sidebar-collapsed", document.body.classList.contains("sidebar-collapsed"));
		});

		// Restore sidebar state on load
		if (localStorage.getItem("sidebar-collapsed") === "true") {
			document.body.classList.add("sidebar-collapsed");
		}
	}

	const backupBtn = document.getElementById("backupBtn");
	const statusDiv = document.getElementById("backupStatus");
	const lastBackupText = document.querySelector("#maintenanceModalLabel strong");

	if (backupBtn) {
		backupBtn.addEventListener("click", () => {
			backupBtn.disabled = true;
			backupBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Backing up...';

			fetch('backup.php', {
				method: 'GET',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(res => res.json())
			.then(data => {
				if (data.status === 'success') {
					if (statusDiv) statusDiv.innerHTML = `<span class="text-success">${data.message}</span>`;
					if (lastBackupText) lastBackupText.textContent = data.time;
				} else {
					if (statusDiv) statusDiv.innerHTML = `<span class="text-danger">${data.message}</span>`;
				}
			})
			.catch(() => {
				if (statusDiv) statusDiv.innerHTML = `<span class="text-danger">Something went wrong.</span>`;
			})
			.finally(() => {
				backupBtn.disabled = false;
				backupBtn.innerHTML = `<i class="bi bi-hdd me-1"></i> Backup Database`;
			});
		});
	}
});
</script>


</body>
</html>
