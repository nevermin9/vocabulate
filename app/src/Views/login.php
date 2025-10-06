<?php 
$email_errors_present = !empty($this->params['errors']['email'] ?? []);
$credentials_errors_present = !empty($this->params['errors']['credentials'] ?? []);
$is_email_input_invalid = $email_errors_present || $credentials_errors_present;

$error_message_to_display = $email_errors_present 
? ($this->params['errors']['email'][0] ?? null) 
: ($credentials_errors_present ? ($this->params['errors']['credentials'][0] ?? null) : null);
?>

<div class="auth-page">
    <h1 class="">
        Vocabulate
    </h1>

    <div class="auth-page__auth-box auth-box">
        <h3 class="auth-page__title">
            Log In
        </h3>

        <form
            class="auth-form"
            action="/login"
            method="post"
        >
            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Email
                </span>

                <input 
                    class="app-field__input  <?php echo ( $is_email_input_invalid ? 'app-field__input--invalid' : '' ) ?>"
                    type="email"
                    name="email"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($this->params['model']['email'] ?? '')  ?>"
                    required
                    placeholder="example@domain.com"
                />
                <?php if ($error_message_to_display !== null ): ?>
                    <span class="app-field__error-txt">
                    <?php echo htmlspecialchars($error_message_to_display) ?>
                    </span>
                <?php endif; ?>
            </label>

            <label class="auth-form__field  app-field">
                <span class="app-field__label">
                    Password
                </span>

                <input 
                    class="app-field__input  <?php echo ( ($this->params['errors']['credentials'] ?? null ) !== null ? 'app-field__input--invalid' : '' ) ?>"
                    id="password-input"
                    type="password"
                    name="password"
                    value="<?php echo htmlspecialchars($this->params['model']['password'] ?? '')  ?>"
                    maxlength="50"
                    required
                />
            </label>

            <label class="app-checkbox">
                <input 
                    id="show-pass-checkbox"
                    class="app-checkbox__input"
                    type="checkbox"
                    title="show passwords"
                />

                <div class="app-checkbox__custom">
                    <svg 
                        class="app-checkbox__svg"
                        viewBox="0 0 24 24"
                    >
                        <path d="M5 12l5 5l10-10"></path>
                    </svg>
                </div>

                <span class="app-checkbox__label">Show password </span>
            </label>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($this->params['csrf_token']) ?>" />

            <button
                class="auth-form__btn  app-btn app-btn--primary"
                value="login"
            >
                Log In
            </button>
        </form>

        <p class="auth-page__sub-txt">
            Don't have an account yet?
            You can

            <a 
                class="app-link"
                href="/registration"
            >
                sign up here.
            </a>

            <a 
                class="app-link"
                href="/forgot-password"
            >
                Forgot password?
            </a>
        </p>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const CLASS = 'app-field__input--invalid';
    const invalidInputs = document.querySelectorAll(`.${CLASS}`);

    for (const input of Array.from(invalidInputs)) {
        input.addEventListener('input', function() {
            this.classList.remove(CLASS);

            const errorMessage = this.nextElementSibling;

            if (errorMessage && errorMessage.classList.contains('app-field__error-txt')) {
                errorMessage.style.display = 'none';
            }
        });
    }

    const showPassInput = document.getElementById("show-pass-checkbox");
    const passwordInput = document.getElementById("password-input");
    showPassInput.addEventListener("change", (e) => {
        if (e.target.checked) {
            passwordInput.type = "text";
            return;
        }
        passwordInput.type = "password";
    });
});
</script>

