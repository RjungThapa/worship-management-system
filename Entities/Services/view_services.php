<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables
$page_title = "View Services - Worship Management System";
$active_page = "services";

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<main class="container mt-5 pt-3">
  <h2>Worship Services</h2>
  <a href="add_service.php" class="btn btn-success mb-3">Add New Service</a> 

  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Service Date</th>
        <th>Leader</th>
        <th>Worship Leader</th>
        <th>Preacher</th>
        <th>Songs Sung</th>
        <th>Notes</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Fetch services with member names in one query
      $sql = "
        SELECT s.*,
               CONCAT(m1.first_name,' ',m1.last_name) AS leader_name,
               CONCAT(m2.first_name,' ',m2.last_name) AS worship_name,
               CONCAT(m3.first_name,' ',m3.last_name) AS preacher_name
        FROM SERVICES s
        LEFT JOIN MEMBERS m1 ON s.leader = m1.member_id
        LEFT JOIN MEMBERS m2 ON s.worship_leader = m2.member_id
        LEFT JOIN MEMBERS m3 ON s.preacher = m3.member_id
        ORDER BY s.service_id ASC
      ";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
          while($row = $result->fetch_assoc()):
              $sid = (int)$row['service_id'];
      ?>
          <tr>
              <td><?= $sid ?></td>
              <td><?= htmlspecialchars($row['service_date']) ?></td>
              <td><?= htmlspecialchars($row['leader_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['worship_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['preacher_name'] ?? '-') ?></td>
              <td><?= !empty($row['songs_sung']) ? htmlspecialchars($row['songs_sung']) : '-' ?></td>
              <td><?= !empty($row['notes']) ? htmlspecialchars($row['notes']) : '-' ?></td>
              <td>
                  <a href="edit_service.php?id=<?= urlencode($sid) ?>" class="btn btn-sm btn-warning">Edit</a>
                  <button type="button" class="deleteBtn btn btn-sm btn-danger"
                          data-name="<?= htmlspecialchars($row['service_date']) ?>"
                          data-href="delete_service.php?id=<?= $sid ?>">
                    Delete
                  </button>
              </td>
          </tr>
      <?php
          endwhile;
      else:
          echo "<tr><td colspan='8' class='text-center'>No services found</td></tr>";
      endif;
      ?>
    </tbody>
  </table>

  <!-- Reusable Delete Modal -->
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
          <a href="#" class="btn btn-danger px-4" id="confirmDeleteBtn">Delete</a>
        </div>
      </div>
    </div>
  </div>

</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="/worship_management_system/assets/js/delete-modal.js"></script>
