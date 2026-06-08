<?php
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables
$page_title = "Edit Service - Worship Management System";
$active_page = "services";

$error_msg = "";
$id = intval($_GET['id']);

// Fetch existing service
$stmt = $conn->prepare("SELECT * FROM SERVICES WHERE service_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) die("Service not found.");

// Fetch all members for dropdowns
$members = $conn->query("SELECT member_id, CONCAT(first_name,' ',last_name) as name FROM MEMBERS ORDER BY member_id ASC");

// Fetch all songs for multi-select
$songs = $conn->query("SELECT song_id, song_title FROM SONGS ORDER BY song_id ASC");

// Convert existing songs_sung to array
$existing_songs = !empty($service['songs_sung']) ? explode(",", $service['songs_sung']) : [];

/* Handle POST */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $service_date    = trim($_POST['service_date']);
    $leader          = $_POST['leader'] ?? null;
    $worship_leader  = $_POST['worship_leader'] ?? null;
    $preacher        = $_POST['preacher'] ?? null;
    $songs_sung      = $_POST['songs_sung'] ?? [];
    $notes           = trim($_POST['notes']);

    // Convert songs array to comma-separated string
    $songs_sung_str = implode(",", $songs_sung);

    // ---------- VALIDATION ----------
    if (empty($service_date)) $error_msg = "Service date is required.";

    // ---------- DATABASE UPDATE ----------
    if (empty($error_msg)) {
        $stmt = $conn->prepare("
            UPDATE SERVICES 
            SET service_date = ?, leader = ?, worship_leader = ?, preacher = ?, songs_sung = ?, notes = ?
            WHERE service_id = ?
        ");
        $stmt->bind_param(
            "siiissi",
            $service_date,
            $leader,
            $worship_leader,
            $preacher,
            $songs_sung_str,
            $notes,
            $id
        );

        if ($stmt->execute()) {
            header("Location: view_services.php");
            exit;
        } else {
            $error_msg = "Database error: " . $stmt->error;
        }
    }
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-warning text-dark">
      <h3 class="mb-0">Edit Service</h3>
    </div>
    <div class="card-body">

      <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-3">

        <div class="col-md-4">
          <label class="form-label">Service Date *</label>
          <input type="date" name="service_date" class="form-control" value="<?= htmlspecialchars($service['service_date']) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Leader</label>
          <select name="leader" class="form-select">
            <option value="">-- Select Leader --</option>
            <?php while($m = $members->fetch_assoc()): ?>
              <option value="<?= $m['member_id'] ?>" <?= ($service['leader'] == $m['member_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Worship Leader</label>
          <select name="worship_leader" class="form-select">
            <option value="">-- Select Worship Leader --</option>
            <?php $members->data_seek(0); while($m = $members->fetch_assoc()): ?>
              <option value="<?= $m['member_id'] ?>" <?= ($service['worship_leader'] == $m['member_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Preacher</label>
          <select name="preacher" class="form-select">
            <option value="">-- Select Preacher --</option>
            <?php $members->data_seek(0); while($m = $members->fetch_assoc()): ?>
              <option value="<?= $m['member_id'] ?>" <?= ($service['preacher'] == $m['member_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-8">
          <label class="form-label">Songs Sung</label>
          <select name="songs_sung[]" class="form-select" multiple>
            <?php while($s = $songs->fetch_assoc()): ?>
              <option value="<?= $s['song_id'] ?>" <?= in_array($s['song_id'], $existing_songs) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['song_title']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($service['notes']) ?></textarea>
        </div>

        <div class="col-12 text-end">
          <a href="view_services.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-warning">Update Service</button>
        </div>

      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
