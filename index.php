<?php
require 'vendor/autoload.php';
session_start();
include('config.php'); 
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน!";
    } else {
        $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                echo "<script>
                        window.location.href = 'main.php';
                      </script>";
            } else {
                $error = "รหัสผ่านไม่ถูกต้อง!";
            }
        } else {
            $error = "ไม่มีชื่อผู้ใช้นี้ในระบบ!";
        }
    }
}
$show_success_modal = false;
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $telegram_chat_id = isset($_POST['telegram_chat_id']) ? trim($_POST['telegram_chat_id']) : null;

    if ($password !== $confirm_password) {
        $error_message = "รหัสผ่านไม่ตรงกัน!";
        $username_input = $username;
        $email_input = $email;
        $telegram_chat_id_input = $telegram_chat_id;
        echo "<script>
            $(document).ready(function() {
                $('#registerModal').modal('show');
            });
        </script>";
    } else {
        $query = "SELECT * FROM users WHERE Email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $query_username = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt_username = $conn->prepare($query_username);
        $stmt_username->bind_param("s", $username);
        $stmt_username->execute();
        $result_username = $stmt_username->get_result();

        if ($result->num_rows > 0) {
            $error_message = "อีเมลนี้มีการสมัครสมาชิกแล้ว!";
            $username_input = $username;
            $email_input = $email;
            $telegram_chat_id_input = $telegram_chat_id;
            echo "<script>
                $(document).ready(function() {
                    $('#registerModal').modal('show');
                });
            </script>";
        }
        elseif ($result_username->num_rows > 0) {
            $error_message = "ชื่อผู้ใช้งานนี้มีการสมัครสมาชิกแล้ว!";
            $username_input = $username;
            $email_input = $email;
            $telegram_chat_id_input = $telegram_chat_id;
            echo "<script>
                $(document).ready(function() {
                    $('#registerModal').modal('show');
                });
            </script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password, telegram_chat_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $telegram_chat_id);

            if ($stmt->execute()) {
                $show_success_modal = true;
            
                $mail = new PHPMailer(true);
                try {
                    // ตั้งค่าเซิร์ฟเวอร์อีเมล
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'mitinventor015@gmail.com'; // อีเมล Gmail ของคุณ
                    $mail->Password = 'ukebjwmfzmwuipjw'; // ใช้ App Password แทน
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // ตั้งค่าอีเมลผู้ส่งและผู้รับ
                    $mail->setFrom('mitinventor015@gmail.com', 'WEB STOCK');
                    $mail->addAddress($email); // อีเมลผู้รับจากฟอร์ม
                    $mail->CharSet = 'UTF-8';
            
                    // เนื้อหาอีเมล
                    $mail->isHTML(true);
                    $mail->Subject = 'ยินดีต้อนรับสู่ระบบ!';
                    $mail->Body = "
                        <h1>สวัสดีคุณ $username!</h1>
                        <p>ขอบคุณที่สมัครสมาชิกกับเรา ระบบของเราได้รับข้อมูลของคุณเรียบร้อยแล้ว.</p>
                        <p>คุณสามารถเข้าสู่ระบบได้ที่ <a href='web-checkstock.unaux.com'>คลิกที่นี่</a></p>
                        <br>
                        <p>ทีมงานของเรา</p>
                    ";
            
                    // ส่งอีเมล
                    $mail->send();
                } catch (Exception $e) {
                    echo "<script>alert('ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}');</script>";
                }                
            }
             else {
                echo "<script>alert('Error: Could not register.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

    <style>
    * {
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #9B9AFF;
    }

    .login-container {
        width: 100%;
        max-width: 450px;
        padding: 20px;
    }

    .login-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 45px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .input-icon {
        position: relative;

        .form-control {
            padding-top: 8px;
            padding-left: 14%;
        }

    }

    .input-icon i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        margin-left: 3%;
    }

    .forgot-password-link {
        text-align: end;
    }

    .signup-link {
        text-align: center;
    }

    .signup-link:hover,
    .forgot-password-link:hover {
        text-decoration: underline;
    }

    form .btn {
        width: 50%;
    }

    .login-text {
        font-weight: bold;
    }

    a {
        font-size: 16px;
        color: grey;
    }

    p {
        margin: 0;
    }

    .modal-footer {
        justify-content: center
    }

    .form-group.password {
        position: relative;
    }

    .form-group.password .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .modal-header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .modal-header .modal-title {
        margin: 0;
        font-size: 1.5rem;
        text-align: center;
    }

    .modal-header .close {
        position: absolute;
        right: 10px;
        top: 10px;
        font-size: 30px;
        color: #000;
    }

    .modal-header .close:hover {
        color: #f00;
        outline: none;
    }

    .logo-container {
        font-size: xxx-large;
    }

    .btn-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        .btn-secondary {
            width: 35%;
        }

        .btn-primary {
            width: fit-content;
        }
    }

    .modal-body .fa-circle-check {
        font-size: 6rem;
    }

    .register-complete {
        font-size: 20px;
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container mb-2">
                <i class="fa-solid fa-user"></i>
            </div>
            <h2 class="login-text mb-3">ลงชื่อเข้าใช้</h2>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="#" method="POST">
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control rounded-pill" id="username" name="username"
                            placeholder="ชื่อผู้ใช้" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control rounded-pill" id="password" name="password"
                            placeholder="รหัสผ่าน" required>
                    </div>
                </div>
                <p class="forgot-password-link">
                    <a href="#" id="forgotPasswordLink" data-toggle="modal"
                        data-target="#forgotPasswordModal">ลืมรหัสผ่าน?</a>
                </p>
                <button type="submit" name="login" class="btn btn-primary rounded-pill mt-2">เข้าสู่ระบบ</button>
                <p class="signup-link mt-2">
                    <a href="#" data-toggle="modal" data-target="#registerModal">สมัครสมาชิก</a>
                </p>
            </form>

        </div>
    </div>


    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content registor">
                <div class="modal-header align-items-center">
                    <div class="logo-container">
                        <h5 class="modal-title mx-auto" id="registerModalLabel">สมัครสมาชิก</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body px-4">
                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <div class="form-group">
                            <input type="text" name="username" id="username" class="form-control rounded-pill"
                                placeholder="ชื่อผู้ใช้"
                                value="<?php echo isset($username_input) ? htmlspecialchars($username_input) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control rounded-pill"
                                placeholder="ที่อยู่อีเมลล์"
                                value="<?php echo isset($email_input) ? htmlspecialchars($email_input) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                                class="form-control rounded-pill" placeholder="Telegram Chat ID"
                                value="<?php echo isset($telegram_chat_id_input) ? htmlspecialchars($telegram_chat_id_input) : ''; ?>">
                        </div>
                        <div class="form-group password">
                            <input type="password" name="password" id="registerPassword"
                                class="form-control rounded-pill" placeholder="รหัสผ่าน (อย่างน้อย 3 ตัว)" required>
                            <i class="fa fa-eye-slash toggle-password" data-target="registerPassword"></i>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="confirm_password" id="confirmPassword"
                                class="form-control rounded-pill" placeholder="ยืนยันรหัสผ่าน " required>
                            <i class="fa fa-eye-slash toggle-password" data-target="confirmPassword"></i>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="register" class="btn btn-primary rounded-pill">สมัครสมาชิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal">
                <div class="modal-header align-items-center">
                    <h5 class="modal-title mx-auto">ลืมรหัสผ่าน?</h5>
                </div>
                <div class="modal-body px-4">
                    <form id="forgotPasswordForm" method="POST" action="process_forgot_password.php">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control rounded-pill"
                                placeholder="กรุณาใส่อีเมล" required>
                        </div>
                        <div class="btn-footer">
                            <button type="submit" class="btn btn-primary rounded-pill">ส่งลิงค์ไปยังอีเมล</button>
                            <button type="button" class="btn btn-secondary rounded-pill"
                                data-dismiss="modal">ยกเลิก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" style="padding: 15px 30px 30px" ;>
                    <i class="fa-regular fa-circle-check" style="color: #7ddb98;"></i>
                    <h1 class="mt-4">ลงทะเบียน</h1>
                    <p class="register-complete">ขอบคุณสำหรับการลงทะเบียน ระบบได้รับข้อมูลของท่านแล้ว
                        และจะส่งอีเมลยืนยันการเข้าระบบภายใน 30 วินาที</p>
                    <button type="button" class="btn btn-primary mt-4" data-dismiss="modal">ตกลง</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    document.querySelectorAll(".toggle-password").forEach(function(icon) {
        icon.addEventListener("click", function() {
            const input = document.getElementById(this.getAttribute("data-target"));
            if (input.type === "password") {
                input.type = "text";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            } else {
                input.type = "password";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            }
        });
    });

    $(document).ready(function() {
        <?php if (isset($error_message)): ?>
        $('#registerModal').modal('show');
        <?php endif; ?>

        $('#registerModal').on('hide.bs.modal', function(event) {
            $(this).find('form')[0].reset();
            $(this).find('.alert-danger').remove();
            $(this).find('#username').val('');
            $(this).find('#surname').val('');
            $(this).find('#email').val('');
            $(this).find('#phone').val('');
        });
    });

    $(document).ready(function() {
        $('#forgotPasswordModal').on('show.bs.modal', function(event) {
            $(this).find('input[name="email"]').val('');
        });
    });

    $(document).ready(function() {
        <?php if ($show_success_modal): ?>
        $('#successModal').modal('show'); // เรียกใช้ modal เมื่อสมัครสำเร็จ
        <?php endif; ?>
    });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>