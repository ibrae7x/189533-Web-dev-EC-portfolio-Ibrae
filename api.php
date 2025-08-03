<?php
/**
 * Ibrae Portfolio - Unified API Handler
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
date_default_timezone_set('Africa/Nairobi');

// Include database configuration
require_once __DIR__ . '/database/config.php';

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

/**
 * Send JSON response and exit
 */
function sendJsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * Get request data (JSON or POST)
 */
function getRequestData() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return $data !== null ? $data : $_POST;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendJsonResponse(false, 'Method not allowed');
}

// Get request data
$data = getRequestData();
$action = sanitizeInput($data['action'] ?? '');

// Log the incoming request for debugging
error_log("API Request - Action: $action, Data: " . json_encode($data));

// Route to appropriate handler
switch ($action) {
    case 'contact':
        handleContact($pdo, $data);
        break;
    case 'signin':
        handleSignIn($pdo, $data);
        break;
    case 'signup':
        handleSignUp($pdo, $data);
        break;
    default:
        sendJsonResponse(false, 'Invalid action specified');
}

/**
 * Handle contact form submission
 */
function handleContact($pdo, $data) {
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $subject = sanitizeInput($data['subject'] ?? '');
    $message = sanitizeInput($data['message'] ?? '');
    $newsletter = isset($data['newsletter']) ? ($data['newsletter'] ? 1 : 0) : 0;

    // Validation
    $errors = [];
    
    if (empty($firstName)) $errors[] = 'First name is required';
    if (empty($lastName)) $errors[] = 'Last name is required';
    
    if (empty($email)) {
        $errors[] = 'Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) $errors[] = 'Subject is required';
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }

    if (!empty($errors)) {
        sendJsonResponse(false, implode('. ', $errors));
    }

    try {
        $sql = "INSERT INTO contacts (first_name, last_name, email, phone, subject, message, newsletter, ip_address) 
                VALUES (:first_name, :last_name, :email, :phone, :subject, :message, :newsletter, :ip_address)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone ?: null,
            'subject' => $subject,
            'message' => $message,
            'newsletter' => $newsletter,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        if ($result) {
            sendJsonResponse(true, 'Thank you for your message! I will get back to you soon.');
        } else {
            throw new Exception('Failed to save contact data');
        }
        
    } catch (PDOException $e) {
        error_log("Contact form error: " . $e->getMessage());
        sendJsonResponse(false, 'Error saving your message. Please try again later.');
    }
}

/**
 * Handle user sign in
 */
function handleSignIn($pdo, $data) {
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $data['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        sendJsonResponse(false, 'Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(false, 'Please enter a valid email address');
    }

    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Remove password from response
            unset($user['password']);
            
            sendJsonResponse(true, 'Login successful', ['user' => $user]);
        } else {
            sendJsonResponse(false, 'Invalid email or password');
        }
    } catch (PDOException $e) {
        error_log("Sign in error: " . $e->getMessage());
        sendJsonResponse(false, 'An error occurred. Please try again.');
    }
}

/**
 * Handle user sign up
 */
function handleSignUp($pdo, $data) {
    error_log("Signup function called with data: " . json_encode($data));
    
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitizeInput($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';

    error_log("Parsed signup data - First: $firstName, Last: $lastName, Email: $email, Phone: $phone");

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        sendJsonResponse(false, 'All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonResponse(false, 'Please enter a valid email address');
    }

    if ($password !== $confirmPassword) {
        sendJsonResponse(false, 'Passwords do not match');
    }

    if (strlen($password) < 6) {
        sendJsonResponse(false, 'Password must be at least 6 characters long');
    }

    try {
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->fetch()) {
            sendJsonResponse(false, 'An account with this email already exists');
        }
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user with phone field
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $phone ?: null, $hashedPassword]);
        
        $userId = $pdo->lastInsertId();
        
        sendJsonResponse(true, 'Account created successfully!', [
            'user' => [
                'id' => $userId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Sign up error: " . $e->getMessage());
        sendJsonResponse(false, 'An error occurred. Please try again.');
    }
}
?>
