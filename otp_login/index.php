<?php
session_start();
require_once 'config.php';

$error = "";
$success = "";

// Robust socket-based SMTP sending function
function send_otp_via_smtp($to_email, $otp) {
    // Open SSL socket connection to Google's SMTP server
    $socket = @fsockopen("ssl://" . SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    
    if (!$socket) {
        return [
            'success' => false, 
            'message' => "Could not connect to Gmail SMTP server. Error ($errno): $errstr"
        ];
    }

    // Helper closure to read and validate SMTP response codes
    $read_response = function($socket, $expected_code) {
        $response = "";
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") {
                break;
            }
        }
        $code = substr($response, 0, 3);
        if ($code != $expected_code) {
            return [
                'success' => false, 
                'message' => "SMTP Protocol Error: Expected $expected_code but received $code. Full response: " . trim($response)
            ];
        }
        return ['success' => true, 'response' => $response];
    };

    // 1. Initial Connection greeting
    $res = $read_response($socket, "220");
    if (!$res['success']) { fclose($socket); return $res; }

    // 2. EHLO Command
    fwrite($socket, "EHLO localhost\r\n");
    $res = $read_response($socket, "250");
    if (!$res['success']) { fclose($socket); return $res; }

    // 3. AUTH LOGIN Command
    fwrite($socket, "AUTH LOGIN\r\n");
    $res = $read_response($socket, "334");
    if (!$res['success']) { fclose($socket); return $res; }

    // 4. Send Base64 Username
    fwrite($socket, base64_encode(SMTP_USER) . "\r\n");
    $res = $read_response($socket, "334");
    if (!$res['success']) { fclose($socket); return $res; }

    // 5. Send Base64 Password (App Password)
    fwrite($socket, base64_encode(SMTP_PASS) . "\r\n");
    $res = $read_response($socket, "235");
    if (!$res['success']) { 
        fclose($socket); 
        return [
            'success' => false, 
            'message' => "Gmail authentication failed! Gmail expects a 16-character 'App Password'. Ensure you generated one and entered it in config.php. Raw error: " . trim($res['message'])
        ]; 
    }

    // 6. MAIL FROM Command
    fwrite($socket, "MAIL FROM:<" . SMTP_USER . ">\r\n");
    $res = $read_response($socket, "250");
    if (!$res['success']) { fclose($socket); return $res; }

    // 7. RCPT TO Command
    fwrite($socket, "RCPT TO:<" . $to_email . ">\r\n");
    $res = $read_response($socket, "250");
    if (!$res['success']) { fclose($socket); return $res; }

    // 8. DATA Command
    fwrite($socket, "DATA\r\n");
    $res = $read_response($socket, "354");
    if (!$res['success']) { fclose($socket); return $res; }

    // Prepare MIME SMTP headers
    $headers = [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "From: " . SENDER_NAME . " <" . SMTP_USER . ">",
        "To: <" . $to_email . ">",
        "Subject: =?UTF-8?B?" . base64_encode("🔒 Your One-Time Password (OTP) Code") . "?=",
        "Date: " . date("r"),
        "Message-ID: <" . time() . "-" . md5($to_email) . "@" . SMTP_HOST . ">"
    ];

    $htmlContent = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Secure OTP Code</title>
            <style>
                body {
                    font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f8fafc;
                    color: #1e293b;
                    padding: 40px 20px;
                    margin: 0;
                }
                .email-container {
                    max-width: 500px;
                    margin: 0 auto;
                    background: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    border: 1px solid #e2e8f0;
                }
                .email-header {
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    padding: 30px;
                    text-align: center;
                    color: #ffffff;
                }
                .email-header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 700;
                    letter-spacing: -0.5px;
                }
                .email-body {
                    padding: 40px 30px;
                    text-align: center;
                }
                .email-body p {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #475569;
                    margin: 0 0 20px 0;
                }
                .otp-box {
                    font-size: 36px;
                    font-weight: 800;
                    color: #059669;
                    letter-spacing: 6px;
                    margin: 30px 0;
                    background: #ecfdf5;
                    padding: 15px 30px;
                    border-radius: 12px;
                    display: inline-block;
                    border: 1px solid #a7f3d0;
                    box-shadow: inset 0 2px 4px rgba(16, 185, 129, 0.05);
                }
                .email-footer {
                    background-color: #f8fafc;
                    padding: 20px;
                    text-align: center;
                    font-size: 12px;
                    color: #94a3b8;
                    border-top: 1px solid #f1f5f9;
                }
                .warning-text {
                    font-size: 13px;
                    color: #94a3b8;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>' . htmlspecialchars(SENDER_NAME) . '</h1>
                </div>
                <div class="email-body">
                    <p>Hello,</p>
                    <p>You requested a One-Time Password (OTP) to securely sign into your account.</p>
                    <div class="otp-box">' . $otp . '</div>
                    <p>This verification code is valid for <strong>10 minutes</strong>. Please do not share this OTP with anyone for security reasons.</p>
                    <p class="warning-text">If you did not request this code, you can safely ignore this email.</p>
                </div>
                <div class="email-footer">
                    &copy; ' . date("Y") . ' ' . htmlspecialchars(SENDER_NAME) . '. All rights reserved.
                </div>
            </div>
        </body>
        </html>
    ';

    $dataPayload = implode("\r\n", $headers) . "\r\n\r\n" . $htmlContent . "\r\n.\r\n";
    
    fwrite($socket, $dataPayload);
    $res = $read_response($socket, "250");
    if (!$res['success']) { fclose($socket); return $res; }

    // 9. QUIT Command
    fwrite($socket, "QUIT\r\n");
    $read_response($socket, "221");

    fclose($socket);
    return ['success' => true];
}

// Handle OTP generation request
if(isset($_POST['send_otp']))
{
    $email = trim($_POST['email']);
    $otp = rand(100000, 999999);

    $send_result = send_otp_via_smtp($email, $otp);

    if($send_result['success'])
    {
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;
        $_SESSION['success_msg'] = "🔒 OTP has been successfully sent to your Gmail inbox!";
        
        // Remove old debug values
        if (isset($_SESSION['debug_otp'])) {
            unset($_SESSION['debug_otp']);
        }
        
        header("Location: verify.php");
        exit;
    }
    else
    {
        $error = "Failed to send Gmail OTP: " . htmlspecialchars($send_result['message']);
        
        // Setup session fallback in case SMTP credentials are not yet entered
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;
        $_SESSION['debug_otp'] = $otp; // Local testing fallback
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Login | Secure Access</title>
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
            --accent-primary: #10b981; /* Green accent matching Gmail/Verified theme */
            --accent-glow: rgba(16, 185, 129, 0.4);
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --header-font: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(circle at top left, #0d1e15, #0b0f19 80%);
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
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
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
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
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

        .login-card:hover {
            box-shadow: 0 25px 50px rgba(16, 185, 129, 0.15);
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-primary) 0%, #047857 100%);
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

        .input-group-custom {
            position: relative;
            margin-bottom: 24px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            color: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--accent-primary);
            box-shadow: 0 0 12px var(--accent-glow);
            color: #ffffff;
        }

        .form-control::placeholder {
            color: #6b7280;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #047857 100%);
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
            background: linear-gradient(135deg, #059669 0%, var(--accent-primary) 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-custom-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .alert-custom-debug {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            color: #fde047;
            border-radius: 12px;
            font-size: 14px;
            padding: 14px;
            margin-top: 24px;
            text-align: center;
        }

        .btn-verify-redirect {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: #e5e7eb;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-verify-redirect:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
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
                
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M18,8H17V6A5,5,0,0,0,7,6V8H6a3,3,0,0,0-3,3V21a3,3,0,0,0,3,3H18a3,3,0,0,0,3-3V11A3,3,0,0,0,18,8ZM9,6a3,3,0,0,1,6,0V8H9ZM19,21a1,1,0,0,1-1,1H6a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H18a1,1,0,0,1,1,1Z" />
                    </svg>
                </div>

                <h2 class="text-center">Welcome Back</h2>
                <p class="subtitle text-center">Enter your email address to receive a secure login OTP</p>

                <?php if($error !== ""): ?>
                    <div class="alert-custom-error d-flex align-items-center">
                        <svg class="me-2" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="input-group-custom">
                        <label class="form-label">Email Address</label>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="you@example.com" 
                               required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <button type="submit" 
                            name="send_otp" 
                            class="btn btn-submit w-100 mb-3">
                        Send Verification Code
                    </button>
                </form>

                <?php if(isset($_SESSION['debug_otp'])): ?>
                    <div class="alert-custom-debug">
                        <div class="text-warning-emphasis mb-2 font-monospace" style="font-size: 13px;">
                            <strong>⚠️ Test Mode Fallback</strong><br>
                            If you haven't configured the App Password yet, you can use this generated OTP for testing locally:
                        </div>
                        <h4 class="mb-3 font-monospace tracking-widest text-white">
                            <strong><?php echo $_SESSION['debug_otp']; ?></strong>
                        </h4>
                        <a href="verify.php" class="btn-verify-redirect w-100">
                            Proceed to Verify OTP
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center mt-3">
                        <a href="verify.php" style="color: var(--accent-primary); font-size: 14px; text-decoration: none; font-weight: 500;">
                            Already have an OTP? Verify here &rarr;
                        </a>
                    </div>
                <?php endif; ?>

                <div class="copyright-text">
                    Secured by Google SMTP &bull; &copy; <?php echo date("Y"); ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>