<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .glass-bg {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .grid-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.025) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-100 grid-bg relative overflow-hidden">
    <!-- Accent Elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-slate-900/10"></div>

    <div class="min-h-screen flex items-center justify-center p-6 relative">
        <div class="w-full max-w-lg animate__animated animate__fadeIn">
            <!-- Login Container -->
            <div class="glass-bg rounded-lg shadow-2xl overflow-hidden border border-slate-200">
                <!-- Header Section -->
                <div class="p-8 text-center border-b border-slate-200">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-blue-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Admin Login</h1>
                    <p class="text-slate-600 mt-2">Please sign in to access the admin panel</p>
                </div>

                <!-- Login Form -->
                <div class="p-8">
                    <!-- Session Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                        </div>
                    @endif

                    @if(request('session_expired'))
                        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <p class="text-sm text-yellow-700">Your session has expired. Please login again.</p>
                        </div>
                    @endif

                    <form id="loginForm" class="space-y-6">
                        @csrf
                        
                        <!-- Error Alert -->
                        <div id="errorAlert"
                            class="hidden animate__animated animate__fadeIn bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p id="errorMessage" class="text-sm text-red-700"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input type="email" id="email" required
                                    class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                    placeholder="admin@example.com">
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" id="password" required
                                    class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="remember"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-slate-700">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-blue-600 hover:text-blue-700 transition-colors">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-all duration-300 flex items-center justify-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Sign In</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Text -->
            <p class="text-center text-sm text-slate-600 mt-6">
                Protected by {{ config('app.name', 'Laravel') }} Security
            </p>
        </div>
    </div>

    <script>
       // Replace your admin login JavaScript with this:

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.textContent = 'Signing in...';
            
            // Clear previous errors
            clearErrors();
            
            try {
                // Use relative URL instead of absolute HTTP URL
                const response = await fetch('/admin/login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    showSuccess('Login successful! Redirecting...');
                    
                    // Redirect to admin dashboard
                    setTimeout(() => {
                        window.location.href = result.redirect || '/admin/dashboard';
                    }, 1000);
                } else {
                    // Show error message
                    showError(result.message || 'Invalid credentials. Please try again.');
                }
                
            } catch (error) {
                console.error('Login error:', error);
                
                if (error.message.includes('Failed to fetch')) {
                    showError('Network error. Please check your connection and try again.');
                } else {
                    showError('Login failed. Please try again.');
                }
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
});

// Helper function to get CSRF token
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (!metaTag) {
        console.error('CSRF token meta tag not found. Make sure your layout includes the CSRF meta tag.');
        return '';
    }
    return metaTag.content;
}

// Show error message
function showError(message) {
    clearErrors();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    const form = document.getElementById('loginForm');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
    }
}

// Show success message
function showSuccess(message) {
    clearErrors();
    
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
    successDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    const form = document.getElementById('loginForm');
    if (form) {
        form.insertBefore(successDiv, form.firstChild);
    }
}

// Clear existing error/success messages
function clearErrors() {
    const existingMessages = document.querySelectorAll('.error-message, .success-message');
    existingMessages.forEach(message => message.remove());
}

// Optional: Add some visual feedback for form fields
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.add('ring-2', 'ring-blue-300');
        });
        
        input.addEventListener('blur', function() {
            this.classList.remove('ring-2', 'ring-blue-300');
        });
        
        // Clear errors when user starts typing
        input.addEventListener('input', function() {
            clearErrors();
        });
    });
});
    </script>

</body>

</html>