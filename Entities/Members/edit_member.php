<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables for header/navbar
$page_title = "Edit Member - Worship Management System";
$active_page = "members";

$error_msg = "";

// Validate & fetch ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: view_members.php");
    exit();
}

// Fetch current member data
$result = $conn->query("SELECT * FROM MEMBERS WHERE member_id = $id");
if (!$result || $result->num_rows === 0) {
    header("Location: view_members.php");
    exit();
}
$member = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tidy up data
    $first_name = ucwords(strtolower(trim($_POST['first_name']))); 
    $last_name  = ucwords(strtolower(trim($_POST['last_name'])));
    $email      = strtolower(trim($_POST['email']));
    $phone      = trim($_POST['phone']);
    $role       = trim($_POST['role']);

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Error: Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error_msg = "Error: Phone number must be exactly 10 digits.";
    } elseif (empty($role)) {
        $error_msg = "Error: Please select a role.";
    } else {
        // Check for duplicates excluding current member
        $check_duplicate = $conn->query(
            "SELECT member_id FROM MEMBERS 
             WHERE (email = '$email' OR phone = '$phone') 
             AND member_id != $id"
        );

        if ($check_duplicate->num_rows > 0) {
            $error_msg = "Error: This email or phone is already assigned to another member.";
        } else {
            // Update member record
            $sql = "
                UPDATE MEMBERS SET
                    first_name = '$first_name',
                    last_name  = '$last_name',
                    email      = '$email',
                    phone      = '$phone',
                    role       = '$role'
                WHERE member_id = $id
            ";

            if ($conn->query($sql) === TRUE) {
                header("Location: view_members.php");
                exit();
            } else {
                $error_msg = "Database Error: " . $conn->error;
            }
        }
    }
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-warning text-dark">
          <h2 class="h4 mb-0">Edit Member</h2>
        </div>
        <div class="card-body">

          <?php if ($error_msg): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($error_msg) ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control"
                     value="<?= htmlspecialchars($member['first_name']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control"
                     value="<?= htmlspecialchars($member['last_name']) ?>" required>
            </div>

            <div class="col-md-8">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($member['email']) ?>" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Phone (10 digits)</label>
              <input type="text" name="phone" class="form-control" maxlength="10"
                     value="<?= htmlspecialchars($member['phone']) ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <?php
                $roles = ["Leader", "Worship Leader", "Preacher", "Singer", "Musician", "Other"];
                foreach ($roles as $r):
                ?>
                  <option value="<?= $r ?>" <?= ($member['role'] === $r) ? "selected" : "" ?>>
                    <?= $r ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 text-end">
              <a href="view_members.php" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-warning">Update Member</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
