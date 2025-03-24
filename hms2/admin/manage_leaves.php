<?php
include '../includes/header.php';
include '../includes/db_connect.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h1 {
            margin-top: 70px;
            color: #444;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        .success, .error {
            display: none;
            font-size: 16px;
            padding: 10px;
            margin: 10px auto;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f1f1f1;
        }
        tr:hover { background-color: #f9f9f9; }
        select, button {
            padding: 8px 10px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        button {
            background-color: #007bff;
            radius: 30px;
            color: white;
            border: none;
        }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<h1>Manage Leave Requests</h1>
<button><a href="dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a></button>
<div class="success" id="successMessage">Leave status updated successfully!</div>
<div class="error" id="errorMessage">Error updating leave status.</div>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT l.id, u.name, l.start_date, l.end_date, l.reason, l.status FROM leaves l JOIN students s ON l.student_id = s.id JOIN users u ON s.user_id = u.id");
            while ($leave = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $leave['id']; ?></td>
                    <td><?php echo htmlspecialchars($leave['name']); ?></td>
                    <td><?php echo $leave['start_date']; ?></td>
                    <td><?php echo $leave['end_date']; ?></td>
                    <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                    <td>
                        <select class="status-update" data-id="<?php echo $leave['id']; ?>">
                            <option value="Pending" <?php if ($leave['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Approved" <?php if ($leave['status'] === 'Approved') echo 'selected'; ?>>Approved</option>
                            <option value="Denied" <?php if ($leave['status'] === 'Denied') echo 'selected'; ?>>Denied</option>
                        </select>
                    </td>
                    <td>
                        <button class="update-btn" data-id="<?php echo $leave['id']; ?>">Update</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $(".update-btn").click(function() {
            var leaveId = $(this).data("id");
            var status = $(".status-update[data-id='" + leaveId + "']").val();
            
            if (confirm("Are you sure you want to update this status?")) {
                $.post("update_leave_status.php", { leave_id: leaveId, status: status }, function(response) {
                    if (response.success) {
                        $("#successMessage").fadeIn().delay(2000).fadeOut();
                    } else {
                        $("#errorMessage").fadeIn().delay(2000).fadeOut();
                    }
                }, "json");
            }
        });
    });
</script>
</body>
</html>
