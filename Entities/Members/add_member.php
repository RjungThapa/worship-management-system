<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables for header/navbar
$page_title = "Add Member - Worship Management System";
$active_page = "members"; // for nav highlighting

$error_msg = "";
$first_name = $last_name = $email = $phone = $role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tidy up data
    $first_name = ucwords(strtolower(trim($_POST['first_name']))); 
    $last_name  = ucwords(strtolower(trim($_POST['last_name'])));
    $email      = strtolower(trim($_POST['email']));
    $phone      = trim($_POST['phone']); 
    $role       = trim($_POST['role']);

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error_msg = "The phone number must be exactly 10 digits.";
    } elseif (empty($role)) {
        $error_msg = "Please select a role.";
    } else {
        // Check for duplicates
        $check_email = $conn->query("SELECT member_id FROM MEMBERS WHERE email = '$email'");
        $check_phone = $conn->query("SELECT member_id FROM MEMBERS WHERE phone = '$phone'");

        if ($check_email->num_rows > 0) {
            $error_msg = "Error: This email address is already registered.";
        } elseif ($check_phone->num_rows > 0) {
            $error_msg = "Error: This phone number is already in use.";
        } else {
            // Insert into database
            $sql = "INSERT INTO MEMBERS (first_name, last_name, email, phone, role) 
                    VALUES ('$first_name', '$last_name', '$email', '$phone', '$role')";

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
        <div class="card-header bg-primary text-white">
          <h2 class="h4 mb-0">Create New Member</h2>
        </div>
        <div class="card-body">
          
          <?php if ($error_msg): ?>
              <div class="alert alert-danger" role="alert">
                  <?= htmlspecialchars($error_msg) ?>
              </div>
          <?php endif; ?>

          <form action="" method="POST" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control"
                     value="<?= htmlspecialchars($first_name) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control"
                     value="<?= htmlspecialchars($last_name) ?>" required>
            </div>

            <div class="col-md-8">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Phone (10 digits)</label>
              <input type="text" name="phone" class="form-control" maxlength="10"
                     value="<?= htmlspecialchars($phone) ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="Leader" <?= ($role === "Leader") ? "selected" : "" ?>>Leader</option>
                <option value="Worship Leader" <?= ($role === "Worship Leader") ? "selected" : "" ?>>Worship Leader</option>
                <option value="Preacher" <?= ($role === "Preacher") ? "selected" : "" ?>>Preacher</option>
                <option value="Singer" <?= ($role === "Singer") ? "selected" : "" ?>>Singer</option>
                <option value="Musician" <?= ($role === "Musician") ? "selected" : "" ?>>Musician</option>
                <option value="Other" <?= ($role === "Other") ? "selected" : "" ?>>Other</option>
              </select>
            </div>

            <div class="col-12 text-end">
              <a href="view_members.php" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
