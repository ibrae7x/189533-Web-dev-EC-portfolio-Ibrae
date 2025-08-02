# 🎯 Ibrae Portfolio - Professional Student Portfolio

A clean, modern portfolio website for Ibrae Mamo, Computer Science student at Strathmore University.

## ✨ Features

- **Modern Design**: Clean, responsive Bootstrap 5 interface
- **User Authentication**: Secure sign-up/sign-in with password hashing
- **Contact System**: Database-integrated contact form with confirmation
- **Dynamic Navbar**: Shows user name when logged in
- **Professional Projects**: Text-based project showcase
- **Database Integration**: All data stored in MySQL database

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 8+ with unified API
- **Database**: MySQL 8+
- **Server**: Apache (XAMPP)

## 📁 Project Structure

```
├── index.html              # Homepage
├── signin.html             # Sign-in page
├── signup.html             # Registration page
├── api.php                 # Unified backend API
├── css/style.css           # Styling
├── js/main.js              # All JavaScript functionality
├── database/
│   ├── complete_setup.sql  # Complete database setup
│   └── config.php          # Database connection
└── images/profile.jpg      # Professional photo
```

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Modern web browser

### Installation

1. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

2. **Setup Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Import: `database/complete_setup.sql`
   - This creates `ibrae_portfolio` database with sample data

3. **Access Portfolio**
   - Portfolio: `http://localhost/ibrae-portfolio`
   - Sign-in: `http://localhost/ibrae-portfolio/signin.html`
   - Sign-up: `http://localhost/ibrae-portfolio/signup.html`

## 🔧 API Usage

The unified `api.php` handles all backend operations:

```javascript
// Contact Form
fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        action: 'contact',
        firstName: 'John',
        lastName: 'Doe',
        email: 'john@example.com',
        subject: 'Hello',
        message: 'Your message here'
    })
});

// User Sign-in
fetch('api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'signin',
        email: 'user@example.com',
        password: 'password123'
    })
});

// User Registration
fetch('api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'signup',
        firstName: 'Jane',
        lastName: 'Doe',
        email: 'jane@example.com',
        password: 'securepassword',
        confirmPassword: 'securepassword'
    })
});
```

## 📊 Database Schema

### Users Table
- `id` - Auto increment primary key
- `first_name` - User's first name
- `last_name` - User's last name
- `email` - Unique email address
- `password` - Hashed password
- `created_at` - Registration timestamp
- `last_login` - Last login timestamp
- `is_active` - Account status

### Contacts Table
- `id` - Auto increment primary key
- `first_name` - Contact's first name
- `last_name` - Contact's last name
- `email` - Contact email
- `phone` - Optional phone number
- `subject` - Message subject
- `message` - Contact message
- `newsletter` - Newsletter subscription
- `ip_address` - Sender's IP
- `status` - Message status (new/read/replied)
- `created_at` - Submission timestamp

## 🔐 Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Input sanitization and validation
- CSRF protection with session management
- Email validation
- Secure database connections

## 🎨 Customization

### Update Profile Information
Edit these sections in `index.html`:
- Hero section name and description
- About section content
- Projects section
- Contact information

### Styling
Modify `css/style.css` for custom styling and branding.

### Database Configuration
Update `database/config.php` with your database credentials.

## 📞 Contact

**Ibrae Mamo**
- Email: ibrae@strathmore.edu
- University: Strathmore University
- Program: Computer Science

## 📄 License

This project is for educational purposes as part of university coursework.

---
*Built with ❤️ by Ibrae Mamo | Strathmore University*
