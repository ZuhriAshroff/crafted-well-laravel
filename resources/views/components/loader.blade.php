<div id="loader" class="hidden fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50">
    <div class="text-center">
        <!-- Custom animated loader -->
        <div class="🤚 mx-auto mb-6">
            <div class="👉"></div>
            <div class="👉"></div>
            <div class="👉"></div>
            <div class="👉"></div>
            <div class="🌴"></div>
            <div class="👍"></div>
        </div>
        
        <!-- Loading text -->
        <div class="max-w-md mx-auto">
            <h3 class="text-xl font-semibold text-gray-800 mb-2" id="loadingText">Processing your skin profile...</h3>
            <p class="text-gray-600 text-sm" id="loadingSubtext">Creating your personalized skincare formulation</p>
            
            <!-- Progress bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                <div class="bg-pink-600 h-2 rounded-full transition-all duration-1000 ease-out" id="progressBar" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let progressInterval;

function showLoader() {
    document.getElementById('loader').classList.remove('hidden');
    
    // Reset progress
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = '0%';
    
    // Animate progress
    let progress = 0;
    progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90; // Don't complete until hideLoader is called
        progressBar.style.width = progress + '%';
    }, 200);
    
    // Change loading text after some time
    setTimeout(() => {
        const loadingText = document.getElementById('loadingText');
        const loadingSubtext = document.getElementById('loadingSubtext');
        if (loadingText && loadingSubtext) {
            loadingText.textContent = 'Analyzing your preferences...';
            loadingSubtext.textContent = 'Almost ready with your custom formulation';
        }
    }, 2000);
}

function hideLoader() {
    // Complete the progress bar
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = '100%';
    
    // Clear interval
    if (progressInterval) {
        clearInterval(progressInterval);
    }
    
    // Hide after a short delay to show completion
    setTimeout(() => {
        document.getElementById('loader').classList.add('hidden');
        
        // Reset text for next time
        const loadingText = document.getElementById('loadingText');
        const loadingSubtext = document.getElementById('loadingSubtext');
        if (loadingText && loadingSubtext) {
            loadingText.textContent = 'Processing your skin profile...';
            loadingSubtext.textContent = 'Creating your personalized skincare formulation';
        }
    }, 500);
}
</script>
@endpush