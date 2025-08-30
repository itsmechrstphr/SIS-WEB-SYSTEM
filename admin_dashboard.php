<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user'])) {
        // Create new user
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
        $role = $_POST['role'];
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role, $first_name, $last_name]);
    } elseif (isset($_POST['create_event'])) {
        // Create new event
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $location = trim($_POST['location']);

        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $event_time, $location, $_SESSION['user_id']]);
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY role, first_name")->fetchAll();

// Get all events
$events = $pdo->query("SELECT e.*, u.first_name, u.last_name FROM events e LEFT JOIN users u ON e.created_by = u.id ORDER BY e.event_date DESC")->fetchAll();

// Get all schedules
$schedules = $pdo->query("SELECT s.*, u.first_name, u.last_name FROM schedules s LEFT JOIN users u ON s.faculty_id = u.id ORDER BY s.day_of_week, s.start_time")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Information System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="#users">Manage Users</a></li>
                <li><a href="#events">Manage Events</a></li>
                <li><a href="#schedules">Manage Schedules</a></li>
                <li><a href="#notifications">Send Notifications</a></li>
            </ul>
        </nav>

        <main class="dashboard-content">
            <!-- User Management Section -->
            <section id="users" class="dashboard-section">
                <h2>User Management</h2>
                <div class="form-container">
                    <h3>Create New User</h3>
                    <form method="POST">
                        <div class="form-row">
                            <input type="text" name="first_name" placeholder="First Name" required>
                            <input type="text" name="last_name" placeholder="Last Name" required>
                        </div>
                        <div class="form-row">
                            <input type="text" name="username" placeholder="Username" required>
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <select name="role" required>
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
                    </form>
                </div>

                <h3>Existing Users</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Event Management Section -->
            <section id="events" class="dashboard-section">
                <h2>Event Management</h2>
                <div class="form-container">
                    <h3>Create New Event</h3>
                    <form method="POST">
                        <input type="text" name="title" placeholder="Event Title" required>
                        <textarea name="description" placeholder="Event Description" rows="3"></textarea>
                        <div class="form-row">
                            <input type="date" name="event_date" required>
                            <input type="time" name="event_time">
                        </div>
                        <input type="text" name="location" placeholder="Location">
                        <button type="submit" name="create_event" class="btn btn-primary">Create Event</button>
                    </form>
                </div>

                <h3>Upcoming Events</h3>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                        <div class="event-details">
                            <span><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                            <?php if ($event['event_time']): ?>
                            <span><?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                            <?php endif; ?>
                            <?php if ($event['location']): ?>
                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Schedule Management Section -->
            <section id="schedules" class="dashboard-section">
                <h2>Class Schedules</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Faculty</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['subject']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                <td><?php echo date('g:i A', strtotime($schedule['start_time'])) . ' - ' . date('g:i A', strtotime($schedule['end_time'])); ?></td>
                                <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Notification Section -->
            <section id="notifications" class="dashboard-section">
                <h2>Send Notifications</h2>
                <div class="form-container">
                    <form>
                        <input type="text" name="notification_title" placeholder="Notification Title" required>
                        <textarea name="notification_message" placeholder="Message" rows="4" required></textarea>
                        <select name="receiver_type">
                            <option value="all">All Users</option>
                            <option value="students">Students Only</option>
                            <option value="faculty">Faculty Only</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
