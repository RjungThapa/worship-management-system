<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/db/db_connection.php'; 

// Page variables for header/navbar
$page_title = "Worship Management System | Dashboard";
$active_page = "dashboard";

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- Hero Section -->
<header class="hero-section text-center mb-5 shadow-sm">
  <div class="container">
    <h1 class="display-5 fw-bold text-primary">Worship Management System</h1>
    <p class="lead text-muted">
      Centrally managing congregation members, songs, and church services.
    </p>
  </div>
</header>

<main class="container">
  <!-- Database Connection Status -->
  <div class="row mb-4">
    <div class="col-12 text-center">
      <?php if ($conn): ?>
        <div class="alert alert-success d-inline-block shadow-sm py-2 px-4" role="alert">
          System Online: Connected to database!
        </div>
      <?php else: ?>
        <div class="alert alert-danger" role="alert">
          System Offline: Database connection failed.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Dashboard Grid -->
  <div class="row g-4 justify-content-center">

    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center d-flex flex-column">
          <h5 class="fw-bold mb-3">Members</h5>
          <p class="card-text text-muted small">
            Manage congregation members and their service roles.
          </p>
          <a href="./Entities/Members/view_members.php"
             class="btn btn-outline-primary mt-auto stretched-link">
             Open Members
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0 border-primary border-top border-4">
        <div class="card-body text-center d-flex flex-column">
          <h5 class="fw-bold mb-3">Song Library</h5>
          <p class="card-text text-muted small">
            Organise hymns, keys, tempos, and lyric resources.
          </p>
          <a href="./Entities/Songs/view_songs.php"
             class="btn btn-outline-primary mt-auto stretched-link">
             Manage Songs
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center d-flex flex-column">
          <h5 class="fw-bold mb-3">Services</h5>
          <p class="card-text text-muted small">
            Plan church services, leaders, and songs used.
          </p>
          <a href="./Entities/Services/view_services.php"
             class="btn btn-outline-primary mt-auto stretched-link">
             Plan Services
          </a>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
q