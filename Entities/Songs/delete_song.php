<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables for header/navbar
$page_title = "Delete Song - Worship Management System";
$active_page = "songs"; // for nav highlighting

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Check if 'id' parameter is provided via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Execute DELETE
        $stmt = $conn->prepare("DELETE FROM SONGS WHERE song_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: view_songs.php?msg=deleted");
            exit();
        } else {
            echo "<div class='container mt-5'><div class='alert alert-danger'>Error: Could not delete the song.</div></div>";
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            // Foreign key constraint (song in service setlist)
            echo "<div class='container mt-5'>
                    <div class='alert alert-warning'>
                        Cannot delete song: It is currently part of an active service setlist (SERVICE_SONGS table).
                    </div>
                    <a href='view_songs.php' class='btn btn-secondary mt-3'>Go Back</a>
                  </div>";
        } else {
            echo "<div class='container mt-5'>
                    <div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>
                  </div>";
        }
    }
} else {
    // Redirect if no valid ID is provided
    header("Location: view_songs.php");
    exit();
}

include __DIR__ . '/../../includes/footer.php';
