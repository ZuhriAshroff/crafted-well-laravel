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

                        <!-- Dynamic message container -->
                        <div id="messageContainer"></div>

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
                                <input type="email" id="email" name="email" required
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
                                <input type="password" id="password" name="password" required
                                    class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember"
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
                        <button type="submit" id="submitButton"
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
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');

            if (loginForm) {
                loginForm.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = document.getElementById('submitButton');
                    const buttonIcon = submitButton.querySelector('svg');
                    const buttonText = submitButton.querySelector('span');

                    // Show loading state
                    submitButton.disabled = true;
                    buttonText.textContent = 'Signing in...';

                    // Clear previous messages
                    clearMessages();

                    try {
                        const response = await fetch('/admin/login', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': getCSRFToken(),
                                'Accept': 'application/json'
                            }
                        });

                        const responseText = await response.text();
                        console.log('Response status:', response.status);
                        console.log('Raw response:', responseText);

                        let result;
                        try {
                            result = JSON.parse(responseText);
                            console.log('Parsed result:', result);
                        } catch (parseError) {
                            console.error('Failed to parse JSON:', parseError);
                            showError('Server returned invalid response. Please try again.');
                            return;
                        }

                        // Check for successful login
                        if (response.ok) {
                            if (result.success === true || result.status === 'success') {
                                // Show GREEN success message
                                showSuccess('Login successful! Redirecting to dashboard...');

                                // Disable form to prevent resubmission
                                const inputs = loginForm.querySelectorAll('input');
                                inputs.forEach(input => input.disabled = true);

                                // Don't reset button state for success case
                                submitButton.textContent = 'Redirecting...';

                                // Redirect after showing success message
                                setTimeout(() => {
                                    const redirectUrl = result.redirect || '/admin/dashboard';
                                    console.log('Redirecting to:', redirectUrl);
                                    window.location.href = redirectUrl;
                                }, 1500);

                                return; // Exit here - don't reset button
                            }
                        }

                        // Handle different error cases
                        if (response.status === 422) {
                            // Validation errors
                            if (result.errors) {
                                const errorMessages = Object.values(result.errors).flat();
                                showError(errorMessages.join('<br>'));
                            } else {
                                showError(result.message || 'Please check your input and try again.');
                            }
                        } else if (response.status === 419) {
                            // CSRF token expired
                            showError('Session expired. Please refresh the page and try again.');
                            setTimeout(() => window.location.reload(), 2000);
                        } else if (response.status === 401) {
                            // Invalid credentials
                            showError(result.message || 'Invalid email or password. Please try again.');
                        } else {
                            // Other errors
                            showError(result.message || `Error ${response.status}: Login failed. Please try again.`);
                        }

                    } catch (error) {
                        console.error('Login error:', error);
                        showError('Network error. Please check your connection and try again.');
                    } finally {
                        // Only reset button state if we haven't succeeded
                        if (!submitButton.textContent.includes('Redirecting')) {
                            submitButton.disabled = false;
                            buttonText.textContent = 'Sign In';
                        }
                    }
                });
            }
        });

        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (!metaTag) {
                console.error('CSRF token meta tag not found');
                return '';
            }
            return metaTag.content;
        }

        function showError(message) {
            const messageContainer = document.getElementById('messageContainer');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'animate__animated animate__fadeIn bg-red-50 border-l-4 border-red-400 p-4 mb-4';
            errorDiv.innerHTML = `
        <div class="flex items-start">
            <svg class="h-5 w-5 text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-red-700">${message}</div>
        </div>
    `;
            messageContainer.appendChild(errorDiv);
        }

        function showSuccess(message) {
            const messageContainer = document.getElementById('messageContainer');
            const successDiv = document.createElement('div');
            successDiv.className = 'animate__animated animate__fadeIn bg-green-50 border-l-4 border-green-400 p-4 mb-4';
            successDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-green-700 font-medium">${message}</div>
        </div>
    `;
            messageContainer.appendChild(successDiv);
        }

        function clearMessages() {
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.innerHTML = '';
        }
    </script>

</body>

</html>