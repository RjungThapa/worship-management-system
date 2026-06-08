<?php 
require_once __DIR__ . '/../../db/db_connection.php';

// Page variables
$page_title = "View Members - Worship Management System";
$active_page = "members";
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<main class="container mt-5 pt-3">
  <h2>Choir Members</h2>
  <a href="add_member.php" class="btn btn-success mb-3">Add New Member</a> 
  
  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT * FROM MEMBERS ORDER BY first_name ASC";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
          while($row = $result->fetch_assoc()):
              $mid = (int)$row['member_id'];
      ?>
          <tr>
            <td><?= $mid ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
              <a href="edit_member.php?id=<?= urlencode($mid) ?>" class="btn btn-sm btn-warning">Edit</a>
              <button type="button" class="btn btn-sm btn-danger deleteBtn"
                      data-name="<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>"
                      data-href="delete_member.php?id=<?= urlencode($mid) ?>"
                      data-role="<?= htmlspecialchars($row['role']) ?>">
                  Delete
              </button>
            </td>
          </tr>
      <?php
          endwhile;
      else:
          echo "<tr><td colspan='7' class='text-center'>No members found</td></tr>";
      endif;
      ?>
    </tbody>
  </table>
</main>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="mb-1">Are you sure you want to delete this member?</p>
        <h5 class="fw-bold" id="modalItemName"></h5>
        <p class="text-muted small mt-2"><br>This action cannot be undone and may affect associated assignments.</p>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger px-4">Delete</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="/worship_management_system/assets/js/delete-modal.js"></script>
