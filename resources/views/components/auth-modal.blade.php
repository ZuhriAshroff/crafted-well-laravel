<div id="authModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md relative mx-4">
        <button id="closeAuthModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Tab Headers -->
        <div class="flex justify-center mb-6 border-b">
            <button id="loginTabBtn" class="px-6 py-3 text-lg font-semibold border-b-2 border-pink-500 text-pink-600 transition-all">
                Login
            </button>
            <button id="registerTabBtn" class="px-6 py-3 text-lg font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all">
                Register
            </button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="auth-form">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" 
                       name="email" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                       placeholder="Enter your email"
                       required />
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                           placeholder="Enter your password"
                           required />
                    <button type="button" 
                            class="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                            onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="error-message mb-4 text-red-500 text-sm hidden"></div>

            <button type="submit" class="submit-btn w-full bg-pink-600 text-white py-3 px-4 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all font-semibold">
                Sign In
            </button>

            <div class="text-center mt-4">
                <a href="#" class="text-sm text-pink-600 hover:text-pink-700">Forgot your password?</a>
            </div>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="auth-form hidden">
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                    <input type="text" 
                           name="first_name" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                           placeholder="First name"
                           required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                    <input type="text" 
                           name="last_name" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                           placeholder="Last name"
                           required />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" 
                       name="email" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                       placeholder="Enter your email"
                       required />
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                <input type="tel" 
                       name="phone_number" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                       placeholder="Enter your phone number"
                       required />
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition-all" 
                           placeholder="Create a password"
                           required />
                    <button type="button" 
                            class="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                            onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters long</p>
            </div>

            <div class="mb-4">
                <label class="flex items-start">
                    <input type="checkbox" 
                           name="terms" 
                           class="mr-2 mt-1 form-checkbox text-pink-500 focus:ring-pink-500"
                           required>
                    <span class="text-sm text-gray-600">
                        I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-pink-600 hover:text-pink-700">Terms of Service</a> 
                        and <a href="{{ route('privacy') }}" target="_blank" class="text-pink-600 hover:text-pink-700">Privacy Policy</a>
                    </span>
                </label>
            </div>

            <div class="error-message mb-4 text-red-500 text-sm hidden"></div>

            <button type="submit" class="submit-btn w-full bg-pink-600 text-white py-3 px-4 rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 transition-all font-semibold">
                Create Account
            </button>
        </form>

        <!-- Social Login Options -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all">
                    <i class="fab fa-google text-red-500 mr-2"></i>
                    Google
                </button>
                <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all">
                    <i class="fab fa-facebook text-blue-600 mr-2"></i>
                    Facebook
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Password toggle functionality
function togglePassword(button) {
    const input = button.previousElementSibling;
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

// Phone number formatting
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('input[name="phone_number"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
            }
            e.target.value = value;
        });
    }
});
</script>
@endpush