
<main>
    <!-- Success Card Container -->
    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8 sm:p-10 text-center transition-all duration-300">
        <!-- Success Icon (Lucide check circle equivalent using inline SVG) -->
        <div class="mx-auto w-16 h-16 flex items-center justify-center bg-green-100 rounded-full mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-3">
            Success!
        </h1>

        <p class="text-lg text-gray-600 mb-8">
            Your password has been successfully reset.
        </p>

        <p class="text-gray-700 mb-8">
            You can now use your **new password** to log in.
        </p>

        <a href="/login" 
            class="app-link"
        >
            Go to Login
        </a>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>
