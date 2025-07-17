<?php
session_start();
require_once 'db.php';

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Function to check if user has admin role
function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

// Function to check if user has operator role
function is_operator() {
    return is_logged_in() && $_SESSION['user_role'] === 'operator';
}

// Function to login user
function login_user($email, $password) {
    global $conn;
    
    $email = sanitize_input($email);
    
    $query = "SELECT id, email, password, role, nama FROM admin WHERE email = ?";
    $result = execute_query($query, [$email], 's');
    
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['login_time'] = time();
            
            return true;
        }
    }
    
    return false;
}

// Function to logout user
function logout_user() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Function to require admin access
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: ../public/index.php");
        exit();
    }
}

// Function to require operator or admin access
function require_operator() {
    require_login();
    if (!is_operator() && !is_admin()) {
        header("Location: ../public/index.php");
        exit();
    }
}

// Function to check session timeout (30 minutes)
function check_session_timeout() {
    if (is_logged_in()) {
        $timeout_duration = 1800; // 30 minutes
        
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout_duration) {
            logout_user();
        }
        
        // Update last activity time
        $_SESSION['login_time'] = time();
    }
}

// Check session timeout on every page load
check_session_timeout();
?>
