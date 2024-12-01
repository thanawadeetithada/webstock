<?php
session_start();
include('config.php'); 

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

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
        $error = "ไม่มี Username นี้ในระบบ!";
    }
}

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error_message = "รหัสผ่านไม่ตรงกัน!";
        $username_input = $username;
        $email_input = $email;
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
    
    if ($result->num_rows > 0) {
        $error_message = "อีเมลนี้มีการสมัครสมาชิกแล้ว!";
        $username_input = $username;
        echo "<script>
            $(document).ready(function() {
                $('#registerModal').modal('show');
            });
        </script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                echo "<script>
                alert('สมัครสมาชิกสำเร็จแล้ว!');
                window.location.href = 'index.php';
            </script>";
        } else {
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
        max-width: 400px;
        padding: 20px;
    }

    .login-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 45px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .user-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #ffd740;
        display: inline-block;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-group input {
        width: 100%;
        padding: 8px 40px;
        border: 1px solid;
        border-radius: 1rem;
        font-size: 16px;
        box-sizing: border-box;
        flex: 1;
    }

    .input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .signup-link,
    .forgot-password-link {
        font-size: 14px;
        color: #333;
        margin-top: 10px;
        cursor: pointer;
        text-align: end;
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

    .forgot-btn {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        button {
            width: auto;
        }
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

    .toggle-password {
        font-size: 20px;
    }

    .modal-header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .modal-header .logo-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .modal-header .user-icon {
        margin-bottom: 10px;
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
    }

    .logo-container {
        font-size: xxx-large;
    }
   
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container mb-3">
                <i class="fa-solid fa-user"></i>
            </div>
            <h2 class="login-text">ลงชื่อเข้าใช้</h2>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="#" method="POST">
                <div class="input-group mt-3">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="ชื่อผู้ใช้" required>
                </div>
                <div class="input-group mt-3">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="รหัสผ่าน" required>
                </div>
                <!-- <p class="signup-link">
                    <a href="#" data-toggle="modal" data-target="#registerModal">สมัครสมาชิก</a>
                </p> -->
                <p class="forgot-password-link">
                    <a href="#" id="forgotPasswordLink" data-toggle="modal" data-target="#forgotPasswordModal">ลืมรหัสผ่าน?</a>
                </p>
                <button type="submit" name="login" class="btn btn-primary rounded-pill mt-3">เข้าสู่ระบบ</button>
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
                    <button type="button" class="close" data-dismiss="modal"  aria-label="Close">
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
                                placeholder="Username"
                                value="<?php echo isset($username_input) ? htmlspecialchars($username_input) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control rounded-pill"
                                placeholder="Email Address"
                                value="<?php echo isset($email_input) ? htmlspecialchars($email_input) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="password" id="registerPassword"
                                class="form-control rounded-pill" placeholder="Password" required>
                            <i class="fa fa-eye-slash toggle-password" data-target="registerPassword"></i>
                        </div>
                        <div class="form-group password">
                            <input type="password" name="confirm_password" id="confirmPassword"
                                class="form-control rounded-pill" placeholder="Confirm" required>
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
                    <h5 class="modal-title mx-auto">ลืมรหัสผ่าน</h5>
                </div>
                <div class="modal-body px-4">
                    <form id="forgotPasswordForm" method="POST" action="process_forgot_password.php">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control rounded-pill"
                                placeholder="กรุณาใส่อีเมล" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill">ส่งลิงค์ไปยังอีเมล</button>
                            <button type="button" class="btn btn-link" data-dismiss="modal">ยกเลิก</button>
                        </div>
                    </form>
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
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
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
    </script>
</body>

</html>