<?php
session_start();
require_once 'config.php';

$message = "";
$message_type = ""; // 'success' or 'danger'
$is_verified = false;

// Check if there is an active OTP session
$has_session = isset($_SESSION['email']) && (isset($_SESSION['otp']) || isset($_SESSION['debug_otp']));

// Handle redirection message
$status_notification = "";
if (isset($_SESSION['success_msg'])) {
    $status_notification = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']); // Show only once
}

if (isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);
    
    // Get stored OTP (either real session OTP or fallback)
    $stored_otp = isset($_SESSION['otp']) ? $_SESSION['otp'] : '';

    if ($stored_otp != "" && $entered_otp == $stored_otp) {
        $message = "🎉 Login Successful! Welcome to your secure dashboard.";
        $message_type = "success";
        $is_verified = true;
        
        // Clean up OTP to prevent reuse, keep email to represent logged-in state
        unset($_SESSION['otp']);
        unset($_SESSION['debug_otp']);
        $_SESSION['logged_in'] = true;
    } else {
        $message = "❌ Invalid OTP. Please check the code sent to your email and try again.";
        $message_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | Secure Access</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(17, 24, 39, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: #9ca3af;
            --accent-success: #10b981;
            --accent-success-glow: rgba(16, 185, 129, 0.2);
            --accent-primary: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.4);
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --header-font: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(circle at top left, #111827, #0b0f19 80%);
            color: var(--text-primary);
            font-family: var(--font-family);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract glowing background circles */
        body::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            top: 10%;
            left: 15%;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            bottom: 10%;
            right: 15%;
            z-index: 0;
            pointer-events: none;
        }

        .container {
            z-index: 10;
            position: relative;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-success) 0%, #047857 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .brand-icon svg {
            width: 28px;
            height: 28px;
            fill: #ffffff;
        }

        h2 {
            font-family: var(--header-font);
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 32px;
            font-weight: 400;
        }

        .form-label {
            color: #e5e7eb;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            color: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 4px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--accent-success);
            box-shadow: 0 0 12px var(--accent-success-glow);
            color: #ffffff;
        }

        .form-control::placeholder {
            color: #4b5563;
            letter-spacing: normal;
            font-weight: normal;
            font-size: 15px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-success) 0%, #047857 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            font-family: var(--header-font);
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, var(--accent-success) 100%);
        }

        .alert-custom-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border-radius: 12px;
            font-size: 14px;
            padding: 14px;
            margin-bottom: 24px;
            text-align: center;
        }

        .alert-custom-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
            border-radius: 12px;
            font-size: 14px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .alert-custom-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            font-size: 14px;
            padding: 14px;
            margin-bottom: 24px;
            text-align: center;
        }

        .alert-custom-no-session {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.15);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
        }

        .btn-action-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: #e5e7eb;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-family: var(--header-font);
            font-weight: 500;
        }

        .btn-action-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .user-email-badge {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 13px;
            color: #e5e7eb;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .copyright-text {
            color: #4b5563;
            font-size: 12px;
            text-align: center;
            margin-top: 32px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="login-card">

                <?php if ($has_session || $is_verified): ?>
                    
                    <!-- Header for active verify flow -->
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12,1L3,5v6c0,5.55,3.84,10.74,9,12c5.16-1.26,9-6.45,9-12V5L12,1z M11,16l-4-4l1.41-1.41L11,13.17l5.59-5.59L18,9L11,16z"/>
                        </svg>
                    </div>

                    <h2 class="text-center"><?php echo $is_verified ? "Access Granted" : "Verify Security Code"; ?></h2>
                    <p class="subtitle text-center mb-2">
                        <?php echo $is_verified ? "You have successfully authenticated" : "Please enter the 6-digit OTP code"; ?>
                    </p>

                    <div class="text-center">
                        <span class="user-email-badge">
                            📧 <?php echo htmlspecialchars($_SESSION['email']); ?>
                        </span>
                    </div>

                    <!-- Dynamic Notifications -->
                    <?php if ($status_notification != ""): ?>
                        <div class="alert-custom-info">
                            <?php echo $status_notification; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($message != ""): ?>
                        <div class="alert-custom-<?php echo $message_type; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Verification Form -->
                    <?php if (!$is_verified): ?>
                        <form method="POST" autocomplete="off">
                            <div class="mb-4">
                                <label class="form-label d-block text-center mb-2">One-Time Password</label>
                                <input type="text" 
                                       name="otp" 
                                       class="form-control" 
                                       maxlength="6" 
                                       placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" 
                                       required 
                                       autofocus>
                            </div>

                            <button type="submit" 
                                    name="verify" 
                                    class="btn btn-submit w-100 mb-3">
                                Complete Verification
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="index.php" style="color: var(--text-secondary); font-size: 14px; text-decoration: none;">
                                &larr; Back to login / Change email
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Successful Login Actions -->
                        <div class="text-center mt-2">
                            <a href="logout.php" class="btn btn-submit w-100 mb-3">
                                Sign Out / Logout
                            </a>
                            <a href="index.php" class="btn-action-outline w-100">
                                Back to Main Page
                            </a>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <!-- No active session error state -->
                    <div class="alert-custom-no-session">
                        <div class="text-danger mb-3">
                            <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                            </svg>
                        </div>
                        <h4 class="mb-2">Session Expired or Missing</h4>
                        <p class="text-secondary small mb-4">
                            We couldn't find an active login request. Please request a new OTP first.
                        </p>
                        <a href="index.php" class="btn btn-submit w-100">
                            Go to Login Page
                        </a>
                    </div>

                <?php endif; ?>

                <div class="copyright-text">
                    Secured by Brevo &bull; &copy; <?php echo date("Y"); ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>