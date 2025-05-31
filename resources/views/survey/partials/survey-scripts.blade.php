<script>
// CSRF Token for Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Allergy dropdown functionality
const allergyData = {
    'preservatives': 'Common preservatives (Parabens, Phenoxyethanol)',
    'fragrances': 'Natural or synthetic fragrances',
    'sulfates': 'Cleansing agents (SLS, SLES)',
    'alcohol': 'Drying alcohols (Ethanol, SD Alcohol)',
    'silicones': 'Dimethicone and similar compounds',
    'retinoids': 'Vitamin A derivatives (Retinol, Retinyl Palmitate)',
    'vitamin_c': 'Ascorbic acid and derivatives',
    'nuts': 'Nut-based ingredients (Almond oil, Shea)',
    'soy': 'Soy-derived ingredients',
    'lanolin': 'Wool-derived ingredients'
};

// Rest of your JavaScript code remains the same...
let selectedAllergies = [];

function initializeAllergyDropdown() {
    const dropdown = document.getElementById('allergyDropdown');
    const options = document.getElementById('dropdownOptions');
    const displayArea = document.getElementById('selectedAllergies');
    const dropdownText = document.getElementById('dropdownText');
    const dropdownIcon = document.getElementById('dropdownIcon');

    if (!dropdown || !options) return;

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            options.classList.add('hidden');
            if (dropdownIcon) {
                dropdownIcon.classList.add('fa-chevron-down');
                dropdownIcon.classList.remove('fa-chevron-up');
            }
        }
    });

    // Handle checkbox changes
    document.querySelectorAll('#dropdownOptions input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const value = this.value;
            const label = this.parentElement.querySelector('.font-medium').textContent;
            
            if (this.checked) {
                selectedAllergies.push({
                    value: value,
                    label: label
                });
            } else {
                selectedAllergies = selectedAllergies.filter(item => item.value !== value);
            }
            updateAllergyDisplay();
            updateDropdownText();
        });
    });
}

function toggleDropdown() {
    const options = document.getElementById('dropdownOptions');
    const dropdownIcon = document.getElementById('dropdownIcon');
    
    if (options) {
        options.classList.toggle('hidden');
    }
    if (dropdownIcon) {
        dropdownIcon.classList.toggle('fa-chevron-down');
        dropdownIcon.classList.toggle('fa-chevron-up');
    }
}

function updateAllergyDisplay() {
    const displayArea = document.getElementById('selectedAllergies');
    if (!displayArea) return;

    displayArea.innerHTML = selectedAllergies.map(item => `
        <span class="bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm flex items-center transition-all duration-200">
            ${item.label}
            <button onclick="removeAllergy('${item.value}')" class="ml-2 text-pink-600 hover:text-pink-800 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </span>
    `).join('');

    // Update the surveyData object
    if (typeof surveyData !== 'undefined') {
        surveyData.profile.allergies = selectedAllergies.map(item => item.value);
    }
}

function removeAllergy(value) {
    const checkbox = document.querySelector(`#dropdownOptions input[value="${value}"]`);
    if (checkbox) checkbox.checked = false;
    selectedAllergies = selectedAllergies.filter(item => item.value !== value);
    updateAllergyDisplay();
    updateDropdownText();
}

function updateDropdownText() {
    const dropdownText = document.getElementById('dropdownText');
    if (!dropdownText) return;

    if (selectedAllergies.length === 0) {
        dropdownText.textContent = 'Click to select allergies';
        dropdownText.classList.add('text-gray-500');
        dropdownText.classList.remove('text-gray-800');
    } else {
        dropdownText.textContent = `${selectedAllergies.length} ${selectedAllergies.length === 1 ? 'allergy' : 'allergies'} selected`;
        dropdownText.classList.remove('text-gray-500');
        dropdownText.classList.add('text-gray-800');
    }
}

// Step management functions
async function showStep(step) {
    showLoader();

    // Hide all steps first
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Simulate loading transition
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Show the target step
    document.getElementById(`step${step}`).classList.remove('hidden');

    // Update navigation buttons
    document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
    document.getElementById('nextBtn').textContent = step === totalSteps ? 'Complete Survey' : 'Next →';

    updateProgress(step);
    hideLoader();
}

// Progress bar update
function updateProgress(step) {
    const dots = document.querySelectorAll('.step-dot');
    const lines = document.querySelectorAll('.step-line');

    dots.forEach((dot, index) => {
        if (index < step) {
            dot.classList.remove('bg-pink-200');
            dot.classList.add('bg-pink-500');
        } else {
            dot.classList.remove('bg-pink-500');
            dot.classList.add('bg-pink-200');
        }
    });

    lines.forEach((line, index) => {
        if (index < (step - 1)) {
            line.classList.remove('bg-pink-100');
            line.classList.add('bg-pink-500');
        } else {
            line.classList.remove('bg-pink-500');
            line.classList.add('bg-pink-100');
        }
    });
}

// Skin type selection
document.querySelectorAll('.skin-type-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.skin-type-btn').forEach(b => {
            b.classList.remove('border-pink-500');
            b.classList.add('border-white');
        });

        this.classList.remove('border-white');
        this.classList.add('border-pink-500');
        surveyData.profile.skin_type = this.dataset.type;
        
        console.log('Selected skin type:', this.dataset.type);
    });
});

// Skin concerns selection
document.querySelectorAll('.concern-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const concern = this.dataset.concern;

        if (!surveyData.profile.primary_skin_concerns) {
            // First selection becomes primary concern
            surveyData.profile.primary_skin_concerns = concern;
            this.classList.add('border-pink-500');
            this.classList.remove('border-white');
            console.log('Primary concern set:', concern);
        } else if (concern === surveyData.profile.primary_skin_concerns) {
            // Clicking primary concern again deselects it
            surveyData.profile.primary_skin_concerns = '';
            this.classList.remove('border-pink-500');
            this.classList.add('border-white');
            console.log('Primary concern cleared');
        } else {
            // Handle secondary concerns
            const index = surveyData.profile.secondary_skin_concerns.indexOf(concern);
            if (index === -1) {
                surveyData.profile.secondary_skin_concerns.push(concern);
                this.classList.add('border-pink-500');
                this.classList.remove('border-white');
                console.log('Secondary concern added:', concern);
            } else {
                surveyData.profile.secondary_skin_concerns.splice(index, 1);
                this.classList.remove('border-pink-500');
                this.classList.add('border-white');
                console.log('Secondary concern removed:', concern);
            }
        }
    });
});

// Environment selection
document.querySelectorAll('.env-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.env-btn').forEach(b => {
            b.classList.remove('border-pink-500', 'border-2');
            b.classList.add('border-transparent');
        });

        this.classList.remove('border-transparent');
        this.classList.add('border-pink-500', 'border-2');
        surveyData.profile.environmental_factors = this.dataset.env;
        
        console.log('Selected environment:', this.dataset.env);
    });
});

// Form validation
function validateStep(step) {
    switch (step) {
        case 1:
            if (!surveyData.profile.skin_type) {
                showNotification('Please select your skin type', 'error');
                return false;
            }
            if (!surveyData.profile.primary_skin_concerns) {
                showNotification('Please select at least one skin concern', 'error');
                return false;
            }
            return true;
        case 2:
            if (!surveyData.profile.environmental_factors) {
                showNotification('Please select your environmental context', 'error');
                return false;
            }
            return true;
        case 3:
            // Allergies are optional
            return true;
        default:
            return false;
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    if (type === 'error') {
        notification.classList.add('bg-red-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
    } else if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    } else {
        notification.classList.add('bg-blue-500', 'text-white');
        notification.innerHTML = `<i class="fas fa-info-circle mr-2"></i>${message}`;
    }

    document.body.appendChild(notification);

    // Slide in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Storage functions
function saveUserData(token, userData) {
    try {
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(userData));
        console.log('User data saved successfully');
    } catch (error) {
        console.error('Error saving user data:', error);
    }
}

function getUserData() {
    try {
        const userData = localStorage.getItem('user');
        if (!userData) return null;
        return JSON.parse(userData);
    } catch (error) {
        console.error('Error parsing user data:', error);
        return null;
    }
}

function getAuthToken() {
    return localStorage.getItem('token');
}

// Modal functions
const authModal = document.getElementById('authModal');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const loginTabBtn = document.getElementById('loginTabBtn');
const registerTabBtn = document.getElementById('registerTabBtn');
const closeAuthModal = document.getElementById('closeAuthModal');

function showAuthModal() {
    if (authModal) {
        authModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideAuthModal() {
    if (authModal) {
        authModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        if (loginForm) loginForm.reset();
        if (registerForm) registerForm.reset();
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
        showLoginTab();
    }
}

function showLoginTab() {
    if (loginForm && registerForm && loginTabBtn && registerTabBtn) {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginTabBtn.classList.add('border-pink-500', 'text-pink-600');
        loginTabBtn.classList.remove('text-gray-500');
        registerTabBtn.classList.remove('border-pink-500', 'text-pink-600');
        registerTabBtn.classList.add('text-gray-500');
    }
}

function showRegisterTab() {
    if (loginForm && registerForm && loginTabBtn && registerTabBtn) {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
        registerTabBtn.classList.add('border-pink-500', 'text-pink-600');
        registerTabBtn.classList.remove('text-gray-500');
        loginTabBtn.classList.remove('border-pink-500', 'text-pink-600');
        loginTabBtn.classList.add('text-gray-500');
    }
}

// Loading spinner HTML
const loadingSpinner = `
    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
`;

// Authentication handlers
async function handleLogin(form) {
    const submitBtn = form.querySelector('.submit-btn');
    const errorDiv = form.querySelector('.error-message');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `${loadingSpinner}Signing in...`;
    errorDiv.classList.add('hidden');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ route("login") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password')
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Invalid credentials');
        }

        // Save auth data
        saveUserData(data.token, data.user);
        return data;

    } catch (error) {
        throw error;
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Sign In';
    }
}

async function handleRegister(form) {
    const submitBtn = form.querySelector('.submit-btn');
    const errorDiv = form.querySelector('.error-message');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = `${loadingSpinner}Creating account...`;
    errorDiv.classList.add('hidden');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ route("register") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: `${formData.get('first_name')} ${formData.get('last_name')}`,
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                email: formData.get('email'),
                phone_number: formData.get('phone_number'),
                password: formData.get('password'),
                password_confirmation: formData.get('password')
            })
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                throw new Error(Object.values(data.errors).flat().join(', '));
            }
            throw new Error(data.message || 'Registration failed');
        }

        return data;
    } catch (error) {
        throw error;
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create Account';
    }
}

// Submit survey data
async function submitSurveyData(token) {
    showLoader();
    
    try {
        // Create custom product
        const response = await fetch('{{ route("api.custom-products.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                base_product_id: surveyData.base_product_id,
                profile: {
                    skin_type: surveyData.profile.skin_type,
                    skin_concerns: [
                        surveyData.profile.primary_skin_concerns,
                        ...surveyData.profile.secondary_skin_concerns
                    ].filter(Boolean),
                    environmental_factors: surveyData.profile.environmental_factors,
                    allergies: surveyData.profile.allergies || []
                }
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to create custom product');
        }

        const productData = await response.json();

        if (productData.success && productData.data?.product_id) {
            // Store product data
            localStorage.setItem('currentProductId', productData.data.product_id);
            localStorage.setItem('customProductData', JSON.stringify(productData.data));

            // Create user profile in background
            fetch('{{ route("api.profiles.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    skin_type: surveyData.profile.skin_type,
                    primary_skin_concerns: surveyData.profile.primary_skin_concerns,
                    secondary_skin_concerns: surveyData.profile.secondary_skin_concerns,
                    environmental_factors: surveyData.profile.environmental_factors,
                    allergies: surveyData.profile.allergies || []
                })
            });

            // Redirect to custom product page
            window.location.href = `{{ url('/custom-products') }}/${productData.data.product_id}`;
        } else {
            throw new Error('Invalid product data received');
        }
    } catch (error) {
        console.error('Survey submission error:', error);
        showNotification(error.message, 'error');
    } finally {
        hideLoader();
    }
}

// Event Listeners - wrapped in DOMContentLoaded to ensure elements exist
document.addEventListener('DOMContentLoaded', function() {
    // Initialize allergy dropdown
    initializeAllergyDropdown();

    // Modal event listeners
    if (loginTabBtn) loginTabBtn.addEventListener('click', showLoginTab);
    if (registerTabBtn) registerTabBtn.addEventListener('click', showRegisterTab);
    if (closeAuthModal) closeAuthModal.addEventListener('click', hideAuthModal);
    if (authModal) {
        authModal.addEventListener('click', (e) => {
            if (e.target === authModal) hideAuthModal();
        });
    }

    // Clear errors on input
    document.querySelectorAll('.auth-form input').forEach(input => {
        input.addEventListener('input', () => {
            const errorDiv = input.closest('form').querySelector('.error-message');
            if (errorDiv) errorDiv.classList.add('hidden');
        });
    });

    // Form submission handlers
    [loginForm, registerForm].forEach(form => {
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const errorDiv = form.querySelector('.error-message');

                try {
                    if (form.id === 'loginForm') {
                        const authData = await handleLogin(form);
                        hideAuthModal();
                        await submitSurveyData(authData.token);
                    } else {
                        await handleRegister(form);
                        showNotification('Account created successfully! Please log in.', 'success');
                        showLoginTab();
                        const emailInput = loginForm.querySelector('[name="email"]');
                        const registerEmail = form.querySelector('[name="email"]');
                        if (emailInput && registerEmail) {
                            emailInput.value = registerEmail.value;
                        }
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    if (errorDiv) {
                        errorDiv.textContent = error.message;
                        errorDiv.classList.remove('hidden');
                    }
                }
            });
        }
    });

    // Navigation event listeners
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) {
        prevBtn.addEventListener('click', async () => {
            if (currentStep > 1) {
                currentStep--;
                await showStep(currentStep);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    await showStep(currentStep);
                } else {
                    showAuthModal();
                }
            }
        });
    }

    // Initialize on page load
    showStep(1);
    console.log('Survey initialized');
});
</script>