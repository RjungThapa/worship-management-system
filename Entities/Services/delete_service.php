<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables for header/navbar
$page_title = "Delete Service - Worship Management System";
$active_page = "services";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Check if 'id' parameter is provided via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Prepare and execute the DELETE statement
        $stmt = $conn->prepare("DELETE FROM SERVICES WHERE service_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Success: Redirect to view with message
            header("Location: view_services.php?msg=deleted");
            exit();
        } else {
            echo "<div class='container mt-5'>
                    <div class='alert alert-danger'>Error: Could not delete the service.</div>
                  </div>";
        }

        $stmt->close();

    } catch (mysqli_sql_exception $e) {
        // Handle any database errors
        echo "<div class='container mt-5'>
                <div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>
              </div>";
    }

} else {
    // No valid ID provided
    header("Location: view_services.php");
    exit();
}

include __DIR__ . '/../../includes/footer.php';
