
<main>
    <div>
        <h1 class="text-3xl font-extrabold text-red-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            Invalid or Expired Link
        </h1>

        <p class="text-gray-600 mb-6 border-b pb-4">
            We were unable to verify your password reset request. Please check the potential issues below.
        </p>

        <!-- Reason List -->
        <ol class="space-y-4 text-gray-700 list-none pl-0">
            <li class="flex items-start">
                <p>The link has <strong class="text-red-600">expired</strong> (reset links are typically only valid for 30–60 minutes).</p>
            </li>
            <li class="flex items-start">
                <p>The link has <strong class="text-red-600">already been used</strong> to successfully reset your password.</p>
            </li>
            <li class="flex items-start">
                <p>The link contains an <strong class="text-red-600">error</strong> or has been modified.</p>
            </li>
        </ol>

        <!-- Next Steps Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">What to do next?</h2>

            <p class="text-gray-700 mb-6">
                Please return to the Password Request Page and submit your email again to receive a new, valid password reset link.
            </p>

            <!-- Action Button -->
            <!-- You will need to replace {{forgot_password_url}} with your actual PHP variable/path -->
            <a href="/forgot-password" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                Get a New Reset Link
            </a>
        </div>

        <!-- Support Message -->
        <p class="mt-6 text-sm text-gray-500 text-center">
            If you continue to have trouble, please contact support.
        </p>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>
