<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables for header/navbar
$page_title = "Delete Member - Worship Management System";
$active_page = "members";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Check if 'id' parameter is provided via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Prepare DELETE statement
    $stmt = $conn->prepare("DELETE FROM MEMBERS WHERE member_id = ?");
    $stmt->bind_param("i", $id);

    try {
        if ($stmt->execute()) {
            // Successful deletion
            header("Location: view_members.php?msg=deleted");
            exit();
        } else {
            echo "
            <div class='container mt-5'>
              <div class='alert alert-danger'>
                Error: Could not delete the member.
              </div>
              <a href='view_members.php' class='btn btn-secondary mt-3'>Go Back</a>
            </div>";
        }
    } catch (mysqli_sql_exception $e) {

        // Foreign key constraint violation (used in SERVICES table)
        if ($e->getCode() == 1451) {
            echo "
            <div class='container mt-5'>
              <div class='alert alert-warning'>
                <strong>Deletion blocked.</strong><br>
                This member is currently assigned to one or more church services
                (as a Leader, Worship Leader, or Preacher).
                Please update or remove the service assignment first.
              </div>
              <a href='view_members.php' class='btn btn-secondary mt-3'>Go Back</a>
            </div>";
        } else {
            echo "
            <div class='container mt-5'>
              <div class='alert alert-danger'>
                Database Error: " . htmlspecialchars($e->getMessage()) . "
              </div>
              <a href='view_members.php' class='btn btn-secondary mt-3'>Go Back</a>
            </div>";
        }
    }

    $stmt->close();
} else {
    // Redirect if no valid ID is found
    header("Location: view_members.php");
    exit();
}

include __DIR__ . '/../../includes/footer.php';
