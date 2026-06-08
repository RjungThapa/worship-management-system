<?php
require_once __DIR__ . '/../../db/db_connection.php';

/* ---------- Variables for includes ---------- */
$page_title   = "Add Song - Worship Management System";
$active_page  = "songs";

$error_msg = "";

$song_title = $hymn_number = "";
$key_root = $key_type = "";
$tempo = "";
$time_signature = $custom_time_signature = "";
$notes = "";
$lyrics_pdf_path = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ---------- INPUT CLEANING ----------
    $song_title = ucwords(strtolower(trim($_POST['song_title'])));
    $hymn_number = trim($_POST['hymn_number']);
    $key_root = strtoupper(trim($_POST['key_root']));
    $key_type = trim($_POST['key_type']);
    $key_signature = trim($key_root . ' ' . $key_type);
    $tempo = trim($_POST['tempo']);
    $time_signature = trim($_POST['time_signature']);
    $custom_time_signature = trim($_POST['custom_time_signature']);
    if ($time_signature === 'Other') $time_signature = $custom_time_signature;
    $notes = trim($_POST['notes']);

    // ---------- Remove PDF ----------
    if (isset($_POST['remove_pdf']) && !empty($_POST['current_pdf'])) {
        $file_to_delete = __DIR__ . '/../../' . $_POST['current_pdf'];
        if (file_exists($file_to_delete)) unlink($file_to_delete);
        $lyrics_pdf_path = "";
    } else {
        $lyrics_pdf_path = $_POST['current_pdf'] ?? "";
    }

    // ---------- VALIDATION ----------
    if (empty($song_title)) {
        $error_msg = "Song title is required.";
    } elseif (!empty($hymn_number) && !is_numeric($hymn_number)) {
        $error_msg = "Hymn number must be numeric.";
    } elseif (empty($key_root) || empty($key_type)) {
        $error_msg = "Key signature is required.";
    } elseif (!empty($tempo) && !is_numeric($tempo)) {
        $error_msg = "Tempo must be numeric.";
    } elseif (empty($time_signature)) {
        $error_msg = "Time signature is required.";
    }

    // ---------- PDF UPLOAD ----------
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

    // ---------- DATABASE INSERT ----------
    if (empty($error_msg)) {
        $sql = "INSERT INTO SONGS
            (song_title, hymn_number, tempo, time_signature, key_signature, lyrics_pdf_path, notes)
            VALUES (
                '$song_title',
                " . ($hymn_number ? "'$hymn_number'" : "NULL") . ",
                " . ($tempo ? "'$tempo'" : "NULL") . ",
                '$time_signature',
                '$key_signature',
                " . ($lyrics_pdf_path ? "'$lyrics_pdf_path'" : "NULL") . ",
                '$notes'
            )";

        if ($conn->query($sql)) {
            header("Location: view_songs.php");
            exit();
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
    }
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h3 class="mb-0">Add New Song</h3>
    </div>
    <div class="card-body">
      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-8">
          <label class="form-label">Song Title *</label>
          <input type="text" name="song_title" class="form-control" value="<?= htmlspecialchars($song_title) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Hymn Number</label>
          <input type="number" name="hymn_number" class="form-control" value="<?= htmlspecialchars($hymn_number) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Key Signature</label>
          <div class="input-group">
            <input type="text" name="key_root" class="form-control" placeholder="C, D#, Bb" value="<?= htmlspecialchars($key_root) ?>" required>
            <select name="key_type" class="form-select" style="max-width:110px;">
              <option value="Major" <?= $key_type==='Major' ? 'selected':'' ?>>Major</option>
              <option value="Minor" <?= $key_type==='Minor' ? 'selected':'' ?>>Minor</option>
            </select>
          </div>
        </div>

        <div class="col-md-4">
          <label class="form-label">Tempo (BPM)</label>
          <input type="number" name="tempo" class="form-control" value="<?= htmlspecialchars($tempo) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Time Signature</label>
          <select name="time_signature" class="form-select" onchange="document.getElementById('customTS').style.display=(this.value==='Other')?'block':'none';">
            <option value="">-- Select --</option>
            <option value="3/4" <?= $time_signature==='3/4'?'selected':'' ?>>3/4</option>
            <option value="4/4" <?= $time_signature==='4/4'?'selected':'' ?>>4/4</option>
            <option value="6/8" <?= $time_signature==='6/8'?'selected':'' ?>>6/8</option>
            <option value="Other" <?= !in_array($time_signature,['3/4','4/4','6/8'])?'selected':'' ?>>Other</option>
          </select>
          <input type="text" name="custom_time_signature" id="customTS" class="form-control mt-2" placeholder="e.g. 5/4, 7/8" style="display:<?= !in_array($time_signature,['3/4','4/4','6/8'])?'block':'none' ?>;" value="<?= htmlspecialchars($custom_time_signature) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Lyrics PDF</label>
          <input type="file" name="lyrics_pdf" class="form-control" accept=".pdf">

          <?php if (!empty($lyrics_pdf_path) && file_exists(__DIR__ . '/../../' . $lyrics_pdf_path)): ?>
            <div class="mt-2 d-flex align-items-center">
              <a href="<?= '/worship_management_system/' . $lyrics_pdf_path ?>" target="_blank" class="btn btn-sm btn-success me-2">
                View PDF
              </a>
              <button type="submit" name="remove_pdf" value="1" class="btn btn-sm btn-danger">
                Remove PDF
              </button>
              <input type="hidden" name="current_pdf" value="<?= $lyrics_pdf_path ?>">
            </div>
          <?php endif; ?>
          <small class="text-muted">PDF only, max 10MB</small>
        </div>

        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($notes) ?></textarea>
        </div>

        <div class="col-12 text-end">
          <a href="view_songs.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Add Song</button>
        </div>

      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
