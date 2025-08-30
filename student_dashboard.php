<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: index.php');
    exit();
}

$student_id = $_SESSION['user_id'];

// Get student's grades
$grades = $pdo->prepare("SELECT g.*, u.first_name, u.last_name FROM grades g LEFT JOIN users u ON g.faculty_id = u.id WHERE g.student_id = ? ORDER BY g.academic_year DESC, g.semester");
$grades->execute([$student_id]);
$student_grades = $grades->fetchAll();

// Get student's attendance
$attendance = $pdo->prepare("SELECT a.*, u.first_name, u.last_name FROM attendance a LEFT JOIN users u ON a.faculty_id = u.id WHERE a.student_id = ? ORDER BY a.attendance_date DESC LIMIT 20");
$attendance->execute([$student_id]);
$student_attendance = $attendance->fetchAll();

// Get class schedules
$schedules = $pdo->query("SELECT * FROM schedules ORDER BY day_of_week, start_time")->fetchAll();

// Get upcoming events
$events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 10")->fetchAll();

// Get notifications for student
$notifications = $pdo->prepare("SELECT n.*, u.first_name, u.last_name FROM notifications n LEFT JOIN users u ON n.sender_id = u.id WHERE n.receiver_id = ? OR n.receiver_id IS NULL ORDER BY n.created_at DESC LIMIT 15");
$notifications->execute([$student_id]);
$student_notifications = $notifications->fetchAll();

// Calculate overall attendance percentage
$attendance_stats = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
    FROM attendance WHERE student_id = ?");
$attendance_stats->execute([$student_id]);
$stats = $attendance_stats->fetch();
$attendance_percentage = $stats['total'] > 0 ? round(($stats['present'] + $stats['late'] * 0.5) / $stats['total'] * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edu.Konek - Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
                <div class="logo-dashboard">
                <img src="assets/images/logo.png" alt="Edu.Konek Logo">
                <h1>Edu.Konek</h1>
                </div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </header>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="#grades">My Grades</a></li>
                <li><a href="#attendance">Attendance</a></li>
                <li><a href="#schedule">Class Schedule</a></li>
                <li><a href="#events">School Events</a></li>
                <li><a href="#notifications">Notifications</a></li>
            </ul>
        </nav>

        <main class="dashboard-content">
            <!-- Grades Section -->
            <section id="grades" class="dashboard-section">
                <h2>My Academic Grades</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($student_grades as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                <td class="<?php echo $grade['grade'] >= 90 ? 'grade-excellent' : ($grade['grade'] >= 80 ? 'grade-good' : ($grade['grade'] >= 70 ? 'grade-average' : 'grade-poor')); ?>">
                                    <?php echo htmlspecialchars($grade['grade']); ?>%
                                </td>
                                <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                                <td><?php echo htmlspecialchars($grade['academic_year']); ?></td>
                                <td><?php echo htmlspecialchars($grade['first_name'] . ' ' . $grade['last_name']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Attendance Section -->
            <section id="attendance" class="dashboard-section">
                <h2>Attendance Record</h2>
                
                <div class="attendance-stats">
                    <div class="stat-card">
                        <h3>Overall Attendance</h3>
                        <div class="stat-value"><?php echo $attendance_percentage; ?>%</div>
                        <div class="stat-details">
                            <span>Present: <?php echo $stats['present']; ?></span>
                            <span>Late: <?php echo $stats['late']; ?></span>
                            <span>Absent: <?php echo $stats['absent']; ?></span>
                        </div>
                    </div>
                </div>

                <h3>Recent Attendance</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($student_attendance as $record): ?>
                            <tr class="attendance-<?php echo $record['status']; ?>">
                                <td><?php echo date('M j, Y', strtotime($record['attendance_date'])); ?></td>
                                <td><?php echo htmlspecialchars($record['subject']); ?></td>
                                <td class="status-<?php echo $record['status']; ?>">
                                    <?php echo ucfirst($record['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Schedule Section -->
            <section id="schedule" class="dashboard-section">
                <h2>Class Schedule</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['subject']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                <td><?php echo date('g:i A', strtotime($schedule['start_time'])) . ' - ' . date('g:i A', strtotime($schedule['end_time'])); ?></td>
                                <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                                <td>
                                    <?php
                                    if ($schedule['faculty_id']) {
                                        $faculty = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                                        $faculty->execute([$schedule['faculty_id']]);
                                        $fac = $faculty->fetch();
                                        if ($fac) {
                                            echo htmlspecialchars($fac['first_name'] . ' ' . $fac['last_name']);
                                        } else {
                                            echo 'N/A';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
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
                    <?php foreach ($student_notifications as $notification): ?>
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