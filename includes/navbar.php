<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="<?= isset($home_path) ? $home_path : '../../index.php'; ?>">
      Worship Management System
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <?php
          $navItems = [
            'home' => ['label' => 'Home', 'path' => $home_path ?? '../../index.php'],
            'members' => ['label' => 'Members', 'path' => $members_path ?? '../Members/view_members.php'],
            'songs' => ['label' => 'Songs', 'path' => $songs_path ?? '../Songs/view_songs.php'],
            'services' => ['label' => 'Services', 'path' => $services_path ?? '../Services/view_services.php'],
          ];

          foreach ($navItems as $key => $item) {
            $activeClass = (isset($active_page) && $active_page === $key) ? 'active' : '';
            echo "<li class='nav-item'>
                    <a class='nav-link $activeClass' href='{$item['path']}'>{$item['label']}</a>
                  </li>";
          }
        ?>
      </ul>
    </div>
  </div>
</nav>
