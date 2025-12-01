<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="St. Matthew Senior High School - Login">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - St. Matthew Academy of Cavite</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/images/image.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('assets/img/apple-touch-icon.png')); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/tabler-icons/tabler-icons.min.css')); ?>">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <img src="<?php echo e(asset('assets/images/image.png')); ?>" alt="SMAC Logo" height="100" class="mb-3">
                    <h2 class="fw-bold mb-2">Sign in to Your Account</h2>
                    <p class="text-muted">Enter your credentials to continue</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <?php if(session('status')): ?>
                            <div class="alert alert-success" role="alert"><?php echo e(session('status')); ?></div>
                        <?php endif; ?>
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('login.submit')); ?>" id="loginForm" novalidate>
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="togglePasswordVisibility('password', this)" style="border: none; background: transparent;">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="<?php echo e(route('admin.auth.forgotForm')); ?>" class="text-primary">Forgot password?</a>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(url('/')); ?>" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn bg-info w-100">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>

                <p class="text-center text-muted small mt-4">&copy; <?php echo e(date('Y')); ?> St. Matthew Senior High School</p>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Handle CSRF token refresh on page load
        $(document).ready(function() {
            $.get('/csrf-token', function(data) {
                $('meta[name="csrf-token"]').attr('content', data.token);
                $('input[name="_token"]').val(data.token);
            }).fail(function() {
                console.log('Could not refresh CSRF token on page load');
            });

            // Intercept form submission to ensure fresh CSRF token
            $('#loginForm').on('submit', function(e) {
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalHtml = submitBtn.html();
                
                // Check if we already processed this submission
                if (form.data('submitting')) {
                    return true;
                }
                
                e.preventDefault();
                
                // Disable submit button
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Signing in...');
                
                // Get fresh CSRF token
                $.get('/csrf-token', function(data) {
                    let tokenInput = form.find('input[name="_token"]');
                    if (tokenInput.length === 0) {
                        form.prepend('<input type="hidden" name="_token" value="' + data.token + '">');
                    } else {
                        tokenInput.val(data.token);
                    }
                    
                    form.data('submitting', true);
                    form.off('submit').submit();
                }).fail(function() {
                    submitBtn.prop('disabled', false).html(originalHtml);
                    form.data('submitting', true);
                    form.off('submit').submit();
                });
                
                return false;
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NEWSMAC\resources\views/login-select.blade.php ENDPATH**/ ?>