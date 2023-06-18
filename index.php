<?php

require_once("config.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Google reCAPTCHA script link -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="auth-page">
        <div class="auth-card card">
            <div class="login" style="display: block;">
                <div class="auth-heading mt-15">
                    <h2>Welcome Back</h2>
                    <p>Created by Elvis Mutinda.</p>
                </div>
                <div class="auth-form">
                    <form action="private/classes/login.php" method="POST">
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> --> 
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <a href="#" class="auth-switch pull-right mt-10 text-muted text-thin" data-show=".forgot">Forgot Password?</a>
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <p class="text-muted text-thin mt-40">Don't have an account?
                        <a href="#" class="auth-switch text-primary" data-show=".register">Create an account</a>
                    </p>
                </div>
            </div>
            <div class="forgot" style="display: none;">
                <div class="auth-heading mt-15">
                    <h2>Forgot Password</h2>
                    <p>Enter your email to begin the reset password process.</p>
                </div>
                <div class="auth-form">
                    <form action="private/classes/forgot.php" method="POST">
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                    <!-- <input type="hidden" name="crsf-token"> -->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <div class="g-recaptcha" data-sitekey="6LeMF6smAAAAACKyqr7IRaOKU2sVunXbnRBmei_e"></div>
                                    <!-- Use your recaptcha site key in the data-sitekey attribute-->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <p class="text-muted text-thin mt-40">Remembered your password?
                        <a href="#" class="auth-switch text-primary" data-show=".login">Go Back to Login</a>
                    </p>
                </div>
            </div>
            <div class="register" style="display: none;">
                <div class="auth-heading mt-15">
                    <h2>Create an Account</h2>
                    <p>Created by Elvis Mutinda.</p>
                </div>
                <div class="auth-form">
                    <form action="private/classes/account.php" method="POST">
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="outer">
                                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <p class="text-muted text-thin mt-40">Already have an account?
                        <a href="#" class="auth-switch text-primary" data-show=".login">Login</a>
                    </p>
                </div>
            </div>
        </div>
        <p class="copyright text-thin text-muted">
            © 2023 Elvocool Expense Tracker
            <span>•</span>
            All Rights Reserved.
        </p>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var registerLink = document.querySelector('.auth-switch[data-show=".register"]');
                var loginLink = document.querySelector('.auth-switch[data-show=".login"]');
                var forgotLink = document.querySelector('.auth-switch[data-show=".forgot"]');
                var registerForm = document.querySelector('.register');
                var loginForm = document.querySelector('.login');
                var forgotForm = document.querySelector('.forgot');

                registerLink.addEventListener('click', function (event) {
                    event.preventDefault();
                    registerForm.style.display = 'block';
                    loginForm.style.display = 'none';
                    forgotForm.style.display = 'none';
                });

                loginLink.addEventListener('click', function (event) {
                    event.preventDefault();
                    registerForm.style.display = 'none';
                    loginForm.style.display = 'block';
                    forgotForm.style.display = 'none';
                });

                forgotLink.addEventListener('click', function (event) {
                    event.preventDefault();
                    registerForm.style.display = 'none';
                    loginForm.style.display = 'none';
                    forgotForm.style.display = 'block';
                });
            });
        </script>
    </div>
</body>
</html>
