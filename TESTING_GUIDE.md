# Student Information System - Manual Testing Guide

## Prerequisites
1. XAMPP installed and running (Apache + MySQL)
2. Database setup completed (import schema.sql via phpMyAdmin)
3. Files placed in htdocs folder (e.g., C:\xampp\htdocs\sis\)

## Testing Steps

### 1. Database Setup Verification
- Open phpMyAdmin (http://localhost/phpmyadmin)
- Create database: `student_management_system`
- Import `database/schema.sql` file
- Verify tables are created: users, events, schedules, grades, attendance, notifications
- Verify sample data is inserted

### 2. Basic Functionality Testing

#### A. Login Page (http://localhost/sis/)
- [ ] Verify page loads correctly
- [ ] Test with invalid credentials (should show error)
- [ ] Test with valid credentials (should redirect to dashboard)

#### B. Signup Page (http://localhost/sis/signup.php)
- [ ] Verify page loads correctly
- [ ] Test form validation (empty fields, password mismatch)
- [ ] Test successful registration (redirects to login with success message)
- [ ] Test duplicate username/email (should show error)

### 3. Role-Based Access Testing

#### Admin Login (Username: admin, Password: password)
- [ ] Login as admin
- [ ] Verify admin dashboard loads
- [ ] Check all navigation links work
- [ ] Verify user management section is accessible
- [ ] Verify event management section is accessible

#### Faculty Login (Username: prof.smith, Password: password)
- [ ] Login as faculty
- [ ] Verify faculty dashboard loads
- [ ] Check grade input functionality
- [ ] Check attendance marking functionality
- [ ] Verify schedule viewing works

#### Student Login (Username: student1, Password: password)
- [ ] Login as student
- [ ] Verify student dashboard loads
- [ ] Check grade viewing functionality
- [ ] Check attendance viewing functionality
- [ ] Verify schedule viewing works

### 4. CRUD Operations Testing

#### Admin - User Management
- [ ] Create new user (Admin → User Management → Create New User)
- [ ] Verify user appears in user list
- [ ] Test login with newly created user

#### Admin - Event Management
- [ ] Create new event
- [ ] Verify event appears in events list
- [ ] Check event details display correctly

#### Faculty - Grade Input
- [ ] Login as faculty
- [ ] Input grade for a student
- [ ] Verify grade appears in student's view
- [ ] Test grade validation (0-100 range)

#### Faculty - Attendance
- [ ] Mark attendance for a student
- [ ] Verify attendance record appears
- [ ] Test different statuses (present, absent, late)

### 5. Navigation and UI Testing
- [ ] Test all dashboard navigation links
- [ ] Verify responsive design on different screen sizes
- [ ] Check form validations and error messages
- [ ] Test logout functionality

### 6. Security Testing
- [ ] Verify session management (try accessing dashboard without login)
- [ ] Test role-based access (student trying to access admin pages)
- [ ] Verify SQL injection prevention (test with special characters in forms)

### 7. Database Operations Testing
- [ ] Verify all CRUD operations work correctly
- [ ] Check data persistence across page refreshes
- [ ] Test concurrent operations (if possible)

### 8. Error Handling Testing
- [ ] Test with invalid form inputs
- [ ] Test database connection errors
- [ ] Verify proper error messages are displayed

### 9. Performance Testing
- [ ] Test page load times
- [ ] Verify database query performance
- [ ] Check memory usage with multiple operations

### 10. Cross-Browser Testing
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Safari (if available)
- [ ] Test in Edge

## Expected Results

### Successful Test Indicators:
- ✅ Pages load without errors
- ✅ Forms submit and process correctly
- ✅ Database operations complete successfully
- ✅ Role-based access works as expected
- ✅ Error messages are clear and helpful
- ✅ UI is responsive and user-friendly

### Common Issues to Watch For:
- ❌ Database connection errors
- ❌ PHP syntax errors
- ❌ Form validation issues
- ❌ Session management problems
- ❌ Cross-browser compatibility issues

## Troubleshooting

### If you encounter issues:
1. Check Apache and MySQL are running in XAMPP
2. Verify database connection settings in `config/database.php`
3. Check PHP error logs in XAMPP
4. Verify file permissions
5. Check for any PHP syntax errors

### Common Fixes:
- Ensure database name matches in `config/database.php`
- Verify MySQL credentials in `config/database.php`
- Check if all required PHP extensions are enabled
- Verify file paths are correct

## Test Completion Checklist

- [ ] Database setup verified
- [ ] Login/Signup functionality tested
- [ ] All role-based dashboards tested
- [ ] CRUD operations verified
- [ ] Navigation and UI tested
- [ ] Security features verified
- [ ] Error handling tested
- [ ] Cross-browser compatibility checked
- [ ] Performance acceptable
- [ ] All issues resolved

After completing these tests, the Student Information System should be fully functional and ready for production use.
