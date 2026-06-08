<?php
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables
$page_title = "Edit Song - Worship Management System";
$active_page = "songs";

$error_msg = "";
$id = intval($_GET['id']);

// Fetch existing song
$stmt = $conn->prepare("SELECT * FROM SONGS WHERE song_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$song = $result->fetch_assoc();

if (!$song) die("Song not found.");

/* Split key_signature */
$key_root = '';
$key_type = 'Major';
if (!empty($song['key_signature'])) {
    $parts = explode(' ', $song['key_signature']);
    $key_root = $parts[0];
    $key_type = $parts[1] ?? 'Major';
}

/* Handle POST */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $song_title = ucwords(strtolower(trim($_POST['song_title'])));
    $hymn_number = $_POST['hymn_number'] ?: NULL;
    $tempo = $_POST['tempo'] ?: NULL;
    $time_signature = trim($_POST['time_signature']);
    $notes = trim($_POST['notes']);
    $key_input = strtoupper(trim($_POST['key_root']));
    $key_type = $_POST['key_type'];
    $key_signature = $key_input . ' ' . $key_type;

    $lyrics_pdf_path = $song['lyrics_pdf_path'];

    /* Remove PDF */
    if (isset($_POST['remove_pdf']) && !empty($_POST['current_pdf'])) {
        $file_to_delete = __DIR__ . '/../../' . $_POST['current_pdf'];
        if (file_exists($file_to_delete)) unlink($file_to_delete);
        $lyrics_pdf_path = "";
    } else {
        $lyrics_pdf_path = $_POST['current_pdf'] ?? $song['lyrics_pdf_path'];
    }

    if (empty($song_title)) $error_msg = "Song title is required.";

    /* PDF Upload */
    if (empty($error_msg) && isset($_FILES['lyrics_pdf']) && $_FILES['lyrics_pdf']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['lyrics_pdf']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error_msg = "Only PDF files are allowed.";
        } elseif ($_FILES['lyrics_pdf']['size'] > 10485760) {
            $error_msg = "PDF must be under 10MB.";
        } else {
            $upload_dir = __DIR__ . '/../../assets/pdfs/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $safe_title = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $song_title);
            $file_name = $safe_title . '.' . $ext;
            $upload_path = $upload_dir . $file_name;

            if (!empty($lyrics_pdf_path) && file_exists(__DIR__ . '/../../' . $lyrics_pdf_path)) {
                unlink(__DIR__ . '/../../' . $lyrics_pdf_path);
            }

            if (move_uploaded_file($_FILES['lyrics_pdf']['tmp_name'], $upload_path)) {
                $lyrics_pdf_path = 'assets/pdfs/' . $file_name;
            } else {
                $error_msg = "Failed to save uploaded file.";
            }
        }
    }

    /* Update database */
    if (empty($error_msg)) {
        $stmt = $conn->prepare("
            UPDATE SONGS SET
                song_title = ?, hymn_number = ?, tempo = ?, time_signature = ?, key_signature = ?, lyrics_pdf_path = ?, notes = ?
            WHERE song_id = ?
        ");
        $stmt->bind_param(
            "siissssi",
            $song_title,
            $hymn_number,
            $tempo,
            $time_signature,
            $key_signature,
            $lyrics_pdf_path,
            $notes,
            $id
        );

        if ($stmt->execute()) {
            header("Location: view_songs.php");
            exit;
        } else {
            $error_msg = "Database error.";
        }
    }
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-warning text-dark">
      <h3 class="mb-0">Edit Song</h3>
    </div>
    <div class="card-body">

      <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-8">
          <label class="form-label">Song Title *</label>
          <input type="text" name="song_title" class="form-control" value="<?= htmlspecialchars($song['song_title']) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Hymn Number</label>
          <input type="number" name="hymn_number" class="form-control" value="<?= htmlspecialchars($song['hymn_number']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Key Signature</label>
          <div class="input-group">
            <input type="text" name="key_root" class="form-control" placeholder="C, D#, Bb" value="<?= htmlspecialchars($key_root) ?>">
            <select name="key_type" class="form-select" style="max-width:110px;">
              <option value="Major" <?= $key_type==='Major'?'selected':'' ?>>Major</option>
              <option value="Minor" <?= $key_type==='Minor'?'selected':'' ?>>Minor</option>
            </select>
          </div>
        </div>

        <div class="col-md-4">
          <label class="form-label">Tempo (BPM)</label>
          <input type="number" name="tempo" class="form-control" value="<?= htmlspecialchars($song['tempo']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Time Signature</label>
          <input type="text" name="time_signature" class="form-control" value="<?= htmlspecialchars($song['time_signature']) ?>">
        </div>

        <div class="col-md-6">
        <label class="form-label">Lyrics PDF</label>
        <div class="d-flex align-items-center gap-2">
            <input type="file" name="lyrics_pdf" class="form-control" accept=".pdf">

            <?php if (!empty($song['lyrics_pdf_path']) && file_exists(__DIR__ . '/../../' . $song['lyrics_pdf_path'])): ?>
            <a href="<?= '/worship_management_system/' . $song['lyrics_pdf_path'] ?>" target="_blank" class="btn btn-success btn-sm">
                View PDF
            </a>
            <a href="remove_pdf.php?id=<?= $song['song_id'] ?>" class="btn btn-danger btn-sm">
                Remove PDF
            </a>
            <?php endif; ?>
        </div>
        <small class="text-muted">PDF only, max 10MB</small>
        </div>


        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($song['notes']) ?></textarea>
        </div>

        <div class="col-12 text-end">
          <a href="view_songs.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-warning">Update Song</button>
        </div>

      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
