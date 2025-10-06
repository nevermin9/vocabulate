<div class="auth-status-page">
    <div class="auth-status-page__box">
        <div class="auth-status-page__icon-box">
            <svg class="auth-status-page__icon icon">
                <use href="#shield-check" />
            </svg>
        </div>

        <h1 class="auth-status-page__title">
            Verification Pending
        </h1>

        <p class="auth-status-page__text">
            Thanks for registering! To activate your account and access all features, you must **verify your email address**.
        </p>

        <p class="auth-status-page__sub-text">
            <b>Didn't receive the email?</b> <br>
            Please check your spam or junk folder. The link will expire shortly.
        </p>

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
    </div>
</div>
