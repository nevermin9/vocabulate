
<div class="auth-status-page">
    <div class="auth-status-page__box">

        <div class="auth-status-page__icon-box auth-status-page__icon-box--invalid">
            <svg class="auth-status-page__icon icon">
                <use href="#cancel" />
            </svg>
        </div>

        <h1 class="auth-status-page__title">
            Verification Error
        </h1>

        <p class="auth-status-page__text">
            The verification link you clicked is either **invalid** or has **expired**.
        </p>

        <p class="auth-status-page__text">
            Verification links are typically valid for a limited time (e.g., 24 hours) for security reasons.
        </p>

        <?php if ($this->params['is-auth']): ?>
        <form
            class="auth-status-page__form"
            method="post"
            action="/verifiction/send"
        >
            <button class="app-btn app-btn--primary">Resend Verification Link</button>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($this->params['csrf_token']) ?>" />
            <input type="hidden" name="redirect_back" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
        </form>

        <a 
            class="app-link auth-status-page__text"
            href="/logout"
        >
            Logout
        </a>
        <?php else: ?>
        <p class="auth-status-page__text">
            Please log in to your account to request a new verification link.
        </p>

        <a 
            class="app-link auth-status-page__text"
            href="/logout"
        >
            Go to Login Page
        </a>
        <?php endif; ?>
    </div>
</div>
