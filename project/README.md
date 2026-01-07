# Study Hub - Modern Learning Management System

A comprehensive, modern LMS built with **Core PHP, HTML, CSS, and JavaScript**.

## 🎨 Design System

**Theme:** Modern Blue / Calm Ocean  
**Color Palette:**
- Primary Blue: #0A8BCB
- Secondary Blue: #1E9ED8
- Background: #EAF6FB
- Dark Text: #0F3A5B
- White: #FFFFFF

**Typography:**
- Headings: Poppins (600-700)
- Body: Inter (400-500)
- Buttons: Poppins (600)

## 🚀 Features

### Student Features
- ✅ Enrollment & Course Management
- ✅ Assignment Submission with Drag & Drop
- ✅ Attendance Tracking
- ✅ Grade Viewing
- ✅ Digital Library (Books, Notes, Question Banks)
- ✅ Certificate Generation & Download
- ✅ Progress Tracking

### Teacher Features
- ✅ Course Creation & Management
- ✅ Module & Lesson Organization
- ✅ Assignment Creation & Grading
- ✅ Manual Attendance
- ✅ QR-Based Attendance (Time-limited)
- ✅ Gradebook & Student Management
- ✅ Messaging System

### Admin Features
- ✅ User Management (Students, Teachers, Admins)
- ✅ Course Approval System
- ✅ Content Moderation
- ✅ Analytics & Reports
- ✅ Library Management
- ✅ System Settings

## 📁 Project Structure

```
project/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php        # Database connection
├── includes/
│   ├── header.php          # HTML head & navbar
│   ├── footer.php          # Footer
│   ├── navbar.php          # Navigation bar
│   └── sidebar.php         # Dashboard sidebar
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   └── js/
│       └── main.js         # JavaScript functions
├── auth/
│   ├── login.php           # Login page
│   ├── register.php        # Registration page
│   └── logout.php          # Logout handler
├── student/
│   ├── dashboard.php       # Student dashboard
│   ├── courses.php         # My courses
│   ├── assignments.php     # Assignments
│   ├── attendance.php      # Attendance view
│   ├── grades.php          # Grades
│   └── certificates.php    # Certificates
├── teacher/
│   ├── dashboard.php       # Teacher dashboard
│   ├── create-course.php   # Course creation
│   ├── assignments.php     # Assignment management
│   ├── attendance.php      # Manual attendance
│   ├── qr-attendance.php   # QR attendance
│   └── gradebook.php       # Gradebook
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── users.php           # User management
│   ├── courses.php         # Course management
│   ├── course-approval.php # Course approval
│   ├── library.php         # Library management
│   └── reports.php         # Analytics & reports
├── database/
│   └── schema.sql          # Database schema
├── index.php               # Landing page
├── courses.php             # Course listing
└── library.php             # Digital library
```

## 🗄️ Database Setup

1. Create a MySQL database named `study_hub_lms`
2. Import the database schema:

```bash
mysql -u root -p study_hub_lms < database/schema.sql
```

Or manually execute the SQL file in phpMyAdmin.

## 🔐 Default Login Credentials

After running the schema.sql, use these credentials:

**Admin:**
- Email: admin@studyhub.com
- Password: admin123

**Teacher:**
- Email: teacher@studyhub.com
- Password: admin123

**Student:**
- Email: student@studyhub.com
- Password: admin123

## ⚙️ Configuration

Edit `config/database.php` to match your database settings:

```php
private $host = 'localhost';
private $db_name = 'study_hub_lms';
private $username = 'root';
private $password = '';
```

## 🌐 Installation

1. Clone or download this project to your web server directory (htdocs/www)
2. Create the database and import schema.sql
3. Update database credentials in config/database.php
4. Access the project via: `http://localhost/project`

## 📱 Responsive Design

Fully responsive design that works on:
- Desktop (1920px+)
- Laptop (1366px)
- Tablet (768px)
- Mobile (375px)

## 🔒 Security Features

- Password hashing with `password_hash()`
- PDO prepared statements
- CSRF token protection
- Session-based authentication
- Role-based access control
- Input sanitization
- File upload validation

## 🎯 Key Technologies

- **Backend:** Core PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Icons:** Font Awesome 6.4
- **Fonts:** Google Fonts (Poppins, Inter)

## 📊 Database Tables

- users
- courses
- enrollments
- modules
- lessons
- lesson_progress
- assignments
- submissions
- attendance
- qr_sessions
- grades
- library
- certificates
- notifications
- messages
- course_requests

## 🎓 Usage

### For Students:
1. Register/Login
2. Browse & enroll in courses
3. Submit assignments
4. Check attendance & grades
5. Download certificates
6. Access library resources

### For Teachers:
1. Create & manage courses
2. Upload course materials
3. Create assignments
4. Mark attendance (manual/QR)
5. Grade submissions
6. View student progress

### For Admins:
1. Manage all users
2. Approve/reject courses
3. Monitor platform activity
4. Generate reports
5. Manage library
6. Configure system settings

## 🚀 Future Enhancements

- Live video classes integration
- Discussion forums
- Real-time notifications
- Mobile app
- Multi-language support
- Payment gateway integration
- Advanced analytics
- Email notifications

## 📄 License

This project is open-source and available for educational purposes.

## 👨‍💻 Author

Built with ❤️ as a portfolio-ready, college-ready LMS project.

---

**Study Hub** - Learn Without Limits 🎓
