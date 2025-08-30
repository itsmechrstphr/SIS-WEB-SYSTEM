# Student Information System (SIS)

A comprehensive web-based Student Information System built with PHP and MySQL that provides role-based access for Admin, Faculty, and Students.

## Features

### Authentication System
- User registration and login
- Role-based access control (Admin, Faculty, Student)
- Secure password hashing

### Admin Dashboard
- Create and manage user accounts (Admin, Faculty, Student)
- Manage school events and announcements
- View and manage class schedules
- Send notifications to users

### Faculty Dashboard
- Input and manage student grades
- Track student attendance
- View teaching schedule
- Monitor school events
- Receive notifications

### Student Dashboard
- View academic grades and performance
- Check attendance records
- Access class schedules
- View school events
- Receive notifications from admin and faculty

## Database Structure

The system uses MySQL with the following tables:
- `users` - User authentication and profiles
- `events` - School events and announcements
- `schedules` - Class schedules
- `grades` - Student academic grades
- `attendance` - Student attendance records
- `notifications` - System notifications

## Installation

1. **Setup XAMPP**
   - Install XAMPP with Apache and MySQL
   - Start Apache and MySQL services

2. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database/schema.sql` file
   - This will create the database and sample data

3. **Configure Database**
   - Update `config/database.php` with your MySQL credentials if different from default

4. **Deploy Application**
   - Place all files in your XAMPP htdocs folder (e.g., `C:\xampp\htdocs\sis\`)
   - Access the application at `http://localhost/sis/`

## Default Login Credentials

### Admin Account
- Username: `admin`
- Password: `password` (hashed in database)

### Faculty Accounts
- Username: `prof.smith` / Password: `password`
- Username: `dr.jones` / Password: `password`

### Student Accounts
- Username: `student1` / Password: `password`
- Username: `student2` / Password: `password`
- Username: `student3` / Password: `password`

## File Structure

```
sis/
├── config/
│   └── database.php          # Database configuration
├── css/
│   └── style.css            # Stylesheet
├── database/
│   └── schema.sql           # Database schema
├── index.php               # Login page
├── login.php              # Login processing
├── signup.php             # Registration page
├── dashboard.php          # Dashboard router
├── admin_dashboard.php    # Admin interface
├── faculty_dashboard.php  # Faculty interface
├── student_dashboard.php  # Student interface
├── logout.php            # Logout functionality
└── README.md             # This file
```

## Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- Role-based access control
- Input validation and sanitization
- SQL injection prevention using prepared statements

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP)
- **Styling**: Custom CSS with responsive design

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Development Notes

- The system uses PDO for database operations
- All passwords are hashed using bcrypt
- Session management ensures secure user authentication
- Responsive design works on desktop and mobile devices

## Future Enhancements

- Email notifications
- File upload functionality
- Advanced reporting
- Mobile app integration
- Real-time chat features
- Calendar integration

## Support

For technical support or questions, please contact the system administrator.
