<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: index.php');
    exit();
}

$faculty_id = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['input_grade'])) {
        // Input grade
        $student_id = $_POST['student_id'];
        $subject = trim($_POST['subject']);
        $grade = $_POST['grade'];
        $semester = trim($_POST['semester']);
        $academic_year = trim($_POST['academic_year']);

        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject, grade, semester, academic_year, faculty_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $subject, $grade, $semester, $academic_year, $faculty_id]);
    } elseif (isset($_POST['mark_attendance'])) {
        // Mark attendance
        $student_id = $_POST['student_id'];
        $subject = trim($_POST['subject']);
        $attendance_date = $_POST['attendance_date'];
        $status = $_POST['status'];

        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, subject, attendance_date, status, faculty_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $subject, $attendance_date, $status, $faculty_id]);
    }
}

// Get all students
$students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY first_name, last_name")->fetchAll();

// Get faculty's schedules
$schedules = $pdo->prepare("SELECT * FROM schedules WHERE faculty_id = ? ORDER BY day_of_week, start_time");
$schedules->execute([$faculty_id]);
$faculty_schedules = $schedules->fetchAll();

// Get upcoming events
$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5")->fetchAll();

// Get notifications for faculty
$notifications = $pdo->prepare("SELECT n.*, u.first_name, u.last_name FROM notifications n LEFT JOIN users u ON n.sender_id = u.id WHERE n.receiver_id = ? OR n.receiver_id IS NULL ORDER BY n.created_at DESC LIMIT 10");
$notifications->execute([$faculty_id]);
$faculty_notifications = $notifications->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - Student Information System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Faculty Dashboard</h1>
            <div class="user-info">
                <span>Welcome, Prof. <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="#grades">Input Grades</a></li>
                <li><a href="#attendance">Attendance</a></li>
                <li><a href="#schedules">My Schedule</a></li>
                <li><a href="#events">School Events</a></li>
                <li><a href="#notifications">Notifications</a></li>
            </ul>
        </nav>

        <main class="dashboard-content">
            <!-- Grade Input Section -->
            <section id="grades" class="dashboard-section">
                <h2>Input Student Grades</h2>
                <div class="form-container">
                    <form method="POST">
                        <select name="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="subject" placeholder="Subject" required>
                        <input type="number" name="grade" step="0.01" min="0" max="100" placeholder="Grade (0-100)" required>
                        <input type="text" name="semester" placeholder="Semester (e.g., Fall 2024)">
                        <input type="text" name="academic_year" placeholder="Academic Year (e.g., 2024-2025)">
                        <button type="submit" name="input_grade" class="btn btn-primary">Submit Grade</button>
                    </form>
                </div>
            </section>

            <!-- Attendance Section -->
            <section id="attendance" class="dashboard-section">
                <h2>Mark Attendance</h2>
                <div class="form-container">
                    <form method="POST">
                        <select name="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="subject" placeholder="Subject" required>
                        <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
                        <select name="status" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                        <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
                    </form>
                </div>
            </section>

            <!-- Schedule Section -->
            <section id="schedules" class="dashboard-section">
                <h2>My Class Schedule</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faculty_schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['subject']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                <td><?php echo date('g:i A', strtotime($schedule['start_time'])) . ' - ' . date('g:i A', strtotime($schedule['end_time'])); ?></td>
                                <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Events Section -->
            <section id="events" class="dashboard-section">
                <h2>Upcoming School Events</h2>
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

            <!-- Notifications Section -->
            <section id="notifications" class="dashboard-section">
                <h2>Notifications</h2>
                <div class="notifications-list">
                    <?php foreach ($faculty_notifications as $notification): ?>
                    <div class="notification-item">
                        <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                        <p><?php echo htmlspecialchars($notification['message']); ?></p>
                        <div class="notification-meta">
                            <span>From: <?php echo htmlspecialchars($notification['first_name'] . ' ' . $notification['last_name']); ?></span>
                            <span><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
