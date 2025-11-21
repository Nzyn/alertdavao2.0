<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlertDavao - Login</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            max-width: 900px;
            width: 100%;
            min-height: 500px;
            border: 3px solid #3b82f6;
        }

        .auth-image {
            flex: 1;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400"><defs><linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:%234f46e5;stop-opacity:0.8" /><stop offset="100%" style="stop-color:%236366f1;stop-opacity:0.9" /></linearGradient></defs><rect width="600" height="400" fill="url(%23bg)"/><rect x="50" y="50" width="500" height="150" fill="white" opacity="0.1" rx="8"/><rect x="70" y="220" width="460" height="130" fill="white" opacity="0.1" rx="8"/><circle cx="150" cy="120" r="15" fill="white" opacity="0.3"/><circle cx="200" cy="140" r="12" fill="white" opacity="0.3"/><circle cx="250" cy="110" r="18" fill="white" opacity="0.3"/><circle cx="300" cy="135" r="14" fill="white" opacity="0.3"/><circle cx="350" cy="115" r="16" fill="white" opacity="0.3"/><circle cx="400" cy="125" r="13" fill="white" opacity="0.3"/><circle cx="450" cy="105" r="15" fill="white" opacity="0.3"/></svg>');
            background-size: cover;
            background-position: center;
            position: relative;
            display: none;
        }

        .auth-form {
            flex: 1;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 400px;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }

        .password-group {
            margin-bottom: 0;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: #3b82f6;
        }

        .submit-btn {
            width: 100%;
            background: #1D3557;
            color: white;
            border: none;
            padding: 0.875rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 1.5rem;
        }

        .submit-btn:hover {
            background: #0f172a;
        }

        .submit-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .register-link {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .register-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        @media (min-width: 768px) {
            .auth-image {
                display: block;
            }
        }

        @media (max-width: 640px) {
            .auth-container {
                margin: 1rem;
                border-radius: 12px;
            }

            .auth-form {
                padding: 2rem 1.5rem;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-image"></div>
        <div class="auth-form">
            <h1 class="auth-title">Welcome Back to AlertDavao!</h1>
            <p class="auth-subtitle">Sign into your account</p>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 1rem;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="email" class="form-label">Your Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        value="<?php echo e(old('email')); ?>"
                        required
                        placeholder="Enter your email address"
                    >
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group password-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        required
                        placeholder="Enter your password"
                    >
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>

                <button type="submit" class="submit-btn">Login</button>

                <div class="register-link">
                    Don't have an account? <a href="<?php echo e(route('register')); ?>">Register</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Codes\Laravel.ReactNative\AlertDavao2.0\AdminSide\admin\resources\views/auth/login.blade.php ENDPATH**/ ?>