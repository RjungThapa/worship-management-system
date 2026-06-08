<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables
$page_title = "View Songs - Worship Management System";
$active_page = "songs";
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-5 pt-3">
  <h2>Worship Songs</h2>
  <a href="add_song.php" class="btn btn-success mb-3">Add New Song</a> 

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Song Title</th>
        <th>Hymn #</th>
        <th>Key</th>
        <th>Time</th>
        <th>Tempo</th>
        <th>PDF</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT * FROM SONGS ORDER BY song_title ASC";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
          while($row = $result->fetch_assoc()):
              $sid = (int)$row['song_id'];
      ?>
          <tr>
              <td><?= $sid ?></td>
              <td><?= htmlspecialchars($row['song_title']) ?></td>
              <td><?= $row['hymn_number'] ? htmlspecialchars($row['hymn_number']) : '-' ?></td>
              <td><?= $row['key_signature'] ? htmlspecialchars($row['key_signature']) : '-' ?></td>
              <td><?= $row['time_signature'] ? htmlspecialchars($row['time_signature']) : '-' ?></td>
              <td><?= $row['tempo'] ? htmlspecialchars($row['tempo']) . ' BPM' : '-' ?></td>
              <td>
                  <?php if (!empty($row['lyrics_pdf_path']) && file_exists(__DIR__ . '/../../' . $row['lyrics_pdf_path'])): ?>
                      <a href="<?= '/worship_management_system/' . $row['lyrics_pdf_path'] ?>" target="_blank" class="btn btn-sm btn-success">View PDF</a>
                  <?php else: ?>
                      -
                  <?php endif; ?>
              </td>
              <td>
                  <a href="edit_song.php?id=<?= urlencode($sid) ?>" class="btn btn-sm btn-warning">Edit</a>
                  <button type="button" class="btn btn-sm btn-danger deleteBtn"
                          data-name="<?= htmlspecialchars($row['song_title']) ?>"
                          data-href="delete_song.php?id=<?= urlencode($sid) ?>">
                      Delete
                  </button>
              </td>
          </tr>
      <?php
          endwhile;
      else:
          echo "<tr><td colspan='8' class='text-center'>No songs found</td></tr>";
      endif;
      ?>
    </tbody>
  </table>
</main>

<!-- SINGLE reusable modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="mb-1">Are you sure you want to delete this?</p>
        <h5 class="fw-bold" id="modalItemName"></h5>
        <p class="text-muted small mt-2"><br>This action cannot be undone and may affect associated schedules.</p>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger px-4">Delete</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<!-- Load reusable JS -->
<script src="/worship_management_system/assets/js/delete-modal.js"></script>
