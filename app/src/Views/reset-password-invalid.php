
<main class="auth-status-page">
    <div class="auth-status-page__box">

        <div class="auth-status-page__icon-box auth-status-page__icon-box--invalid">
            <svg class="auth-status-page__icon icon">
                <use href="#cancel" />
            </svg>
        </div>

        <h1 class="auth-status-page__title">
            Invalid or Expired Link
        </h1>

        <p class="auth-status-page__text">
            We were unable to verify your password reset request. Please check the potential issues below.
        </p>

        <ol class="auth-status-page__ordered-list">
            <li>
                <p>The link has <strong>expired</strong> (reset links are typically only valid for 30–60 minutes).</p>
            </li>
            <li>
                <p>The link has <strong>already been used</strong> to successfully reset your password.</p>
            </li>
            <li>
                <p>The link contains an <strong>error</strong> or has been modified.</p>
            </li>
        </ol>

        <div>
            <h2 class="auth-status-page__sub-title">What to do next?</h2>

            <p class="auth-status-page__text">
                Please return to the Password Request Page and submit your email again to receive a new, valid password reset link.
            </p>

            <a href="/forgot-password" class="app-link auth-status-page__text">
                Get a New Reset Link
            </a>
        </div>
    </div>

    <aside class="auth-page__theme-switcher  theme-switcher-box  theme-switcher-box--center">
        <?php require "_parts/theme-switcher.php"; ?>
    </aside>
</main>
