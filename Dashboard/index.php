<?php
// Admin.php
// Dashboard page for Edu.KONEK with PHP + MySQL (PDO) + AJAX
// Assumes XAMPP, place project in htdocs/edukonek (for example).

require_once __DIR__ . '/db.php';

// Fetch counts for cards (Students / Teachers / Active Classes)
try {
  $pdo = get_pdo();

  // Count students
  $stmt = $pdo->query("SELECT COUNT(*) AS c FROM accounts WHERE role = 'student'");
  $students_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];

  // Count teachers
  $stmt = $pdo->query("SELECT COUNT(*) AS c FROM accounts WHERE role = 'teacher'");
  $teachers_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];

  // Count active classes
  $stmt = $pdo->query("SELECT COUNT(*) AS c FROM classes WHERE is_active = 1");
  $classes_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];

} catch (Throwable $e) {
  // Fallback in case DB isn't ready yet
  $students_count = $teachers_count = $classes_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduKONEK/Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-container">
        <div class="sidebar-header">
          <img src="Logo/Logo.png" alt="EduKONEK">
          <h2>Edu.KONEK</h2>
        </div>

        <ul class="sidebar-link">
          <h4>
            <span>Main Menu</span>
            <div class="Menu-separator"></div>
          </h4>
          <li>
            <a href="#" class="material-icons" id="openSearch">
              <span class="material-icons">search</span>
            </a>
          </li>
          <li><a href="#home" class="subnav-link"><span class="material-icons">home</span>Dashboard</a></li>
          <li><a href="#" id="openCreateAccount"><span class="material-icons">person</span>Create Account</a></li>
          <li><a href="#statistics" class="subnav-link"><span class="material-icons">graphic_eq</span>Update School Statistics</a></li>

          <h4>
            <span>General</span>
            <div class="Menu-separator"></div>
          </h4>
          <li><a href="#notifications" class="subnav-link"><span class="material-icons">notifications_active</span>Notifications</a></li>
          <li><a href="#schedules" class="subnav-link"><span class="material-icons">calendar_today</span>Update Schedules</a></li>

          <h4>
            <span>Account</span>
            <div class="Menu-separator"></div>
          </h4>
          <li><a href="#profile" class="subnav-link"><span class="material-icons">person_outline</span>Profile</a></li>
          <li><a href="#settings" class="subnav-link"><span class="material-icons">settings</span>Settings</a></li>
          <li><a href="#logout" id="logoutBtn"><span class="material-icons">logout</span>Logout</a></li>
        </ul>

        <div class="user-account">
          <div class="user-profile">
            <img src="Logo/up.jpg" alt="User Profile Picture">
            <div class="user-info">
              <h3>Admin</h3>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Dashboard -->
    <main class="dashboard" id="home">

      <header class="main-header">
        <div class="left">
          <button class="hamburger" id="hamburger" aria-label="Toggle sidebar">
            <span class="material-icons">menu</span>
          </button>
          <img src="Logo/Logo.png" alt="Logo">
          <div class="logo">Edu.Konek</div>
        </div>

        <nav class="sub-nav">
          <ul>
              <li class="highlight" data-target="#home">Home</li>
              <li data-target="#schedules">Schedules</li>
              <li data-target="#events">Events</li>
              <li data-target="#email">Email</li>
              <li data-target="#notifications">Notifications</li>
              <li data-target="#statistics">Statistics</li>
              <li id="openCreateAccountTop">Create Account</li>
          </ul>
        </nav>
      </header>

      <!-- Stats -->
      <section class="stats-overview" id="statistics">
        <div class="stat-card">
          <div class="stat-row">
            <h3>Total Students</h3>
            <span class="material-icons" style="color: #02813a;">group</span>
          </div>
          <p id="totalStudents"><?php echo htmlspecialchars((string)$students_count); ?></p>
        </div>
        <div class="stat-card">
          <div class="stat-row">
            <h3>Total Teachers</h3>
            <span class="material-icons" style="color: #02813a;">person</span>
          </div>
          <p id="totalTeachers"><?php echo htmlspecialchars((string)$teachers_count); ?></p>
        </div>
        <div class="stat-card">
          <div class="stat-row">
            <h3>Active Classes</h3>
            <span class="material-icons" style="color: #02813a;">class</span>
          </div>
          <p id="activeClasses"><?php echo htmlspecialchars((string)$classes_count); ?></p>
        </div>
      </section>

      <!-- Events + Schedules -->
      <section class="events-schedules-wrapper">
        <!-- Events -->
        <section class="events-section" id="events">
          <h2>Events</h2>

          <!-- Add Event Form -->
          <form id="eventForm" class="inline-form" autocomplete="off">
            <input type="text" name="title" placeholder="Event title" required />
            <input type="date" name="date_start" required />
            <input type="date" name="date_end" />
            <button type="submit"><span class="material-icons">add</span> Add Event</button>
          </form>

          <div class="events-section-cards" id="events-list">
            <!-- AJAX will render event cards -->
          </div>
        </section>

        <!-- Schedules -->
        <section class="schedules-section" id="schedules">
          <h2>School Schedules</h2>

          <!-- Add Schedule Form -->
          <form id="scheduleForm" class="inline-form">
            <input type="text" name="title" placeholder="Schedule title" required />
            <input type="date" name="date_start" required />
            <input type="date" name="date_end" />
            <button type="submit"><span class="material-icons">add</span> Add Schedule</button>
          </form>

          <div class="schedules-section-cards" id="schedules-list">
            <!-- AJAX will render schedule cards -->
          </div>
        </section>
      </section>

      <!-- Footer -->
      <footer class="main-footer" id="email">
        <p>&copy; <?php echo date('Y'); ?> EduKONEK. All rights reserved.</p>
        <p>Developed by <a href="https://github.com/your-profile">Your Name</a></p>
      </footer>
    </main>
  </div>

  <!-- Create Account Modal -->
  <div class="modal" id="createAccountModal" aria-hidden="true">
    <div class="modal-backdrop" data-close="modal"></div>
    <div class="modal-dialog" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h3>Create Account</h3>
        <button class="icon-btn" data-close="modal" aria-label="Close"><span class="material-icons">close</span></button>
      </div>
      <form id="createAccountForm">
        <div class="form-grid">
          <label>Role
            <select name="role" required>
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </label>
          <label>First Name <input type="text" name="first_name" required /></label>
          <label>Last Name <input type="text" name="last_name" required /></label>
          <label>Email <input type="email" name="email" required /></label>
          <label>Password <input type="password" name="password" minlength="6" required /></label>
        </div>
        <div class="modal-actions">
          <button type="submit"><span class="material-icons">person_add</span> Create</button>
        </div>
      </form>
    </div>
  </div>

  <script src="admin.js"></script>
</body>
</html>
