<form
    class="auth-form"
    method="POST"
    action="/reset-password"
>
    <h3 class="auth-page__title">
        Change password
    </h3>

    <label class="auth-form__field  app-field">
        <span class="app-field__label">
            Your current password*
        </span>

            <!-- class="app-field__input  <?php echo ( ($this->params['errors']['credentials'] ?? null ) !== null ? 'app-field__input--invalid' : '' ) ?>" -->
            <!-- value="<?php echo htmlspecialchars($this->params['model']['password'] ?? '')  ?>" -->
        <input 
            class="app-field__input  "
            id="password-input"
            type="password"
            name="password"
            value=""
            maxlength="50"
            required
        />
    </label>

    <label class="auth-form__field  app-field">
        <span class="app-field__label">
            New Password*
        </span>

            <!-- class="app-field__input <php echo ( $this->params['errors']['password'] ?? null ? 'app-field__input--invalid' : '' ) ?>" -->
            <!-- value="<php echo htmlspecialchars($this->params['model']['password'] ?? '')  ?>" -->
        <input 
            class="app-field__input "
            type="password"
            name="password"
            minlength="8"
            maxlength="50"
            value=""
            required
            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" 
            title="Password must contain: at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long."
        />

        <!-- <ul class="auth-form__field-rules"> -->
        <!--     <php foreach ($this->params['password_rules'] as $rule): ?> -->
        <!--     <php if (in_array($rule, $this->params['errors']['password'] ?? [])): ?> -->
        <!--     <li class="auth-form__field-rule  auth-form__field-rule--invalid"> -->
        <!--     <php else: ?> -->
        <!--     <li class="auth-form__field-rule"> -->
        <!--     <php endif; ?> -->
        <!--         <span> -->
        <!--             <php echo htmlspecialchars($rule); ?> -->
        <!--         </span> -->
        <!--     </li> -->
        <!--     <php endforeach; ?> -->
        <!-- </ul> -->
    </label>

    <label class="auth-form__field  app-field">
        <span class="app-field__label">
            Confirm new password*
        </span>

            <!-- class="app-field__input <php echo ( $this->params['errors']['confirmPassword'] ?? null ? 'app-field__input--invalid' : '' ) ?>" -->
            <!-- value="<php echo htmlspecialchars($this->params['model']['confirmPassword'] ?? '')  ?>" -->
        <input 
            class="app-field__input "
            type="password"
            name="confirmPassword"
            value=""
            minlength="8"
            maxlength="50"
            required
            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" 
            title="Password must contain: at least one lowercase letter, one uppercase letter, one number, one special character, and be at least 8 characters long."
        />
        <!-- <php if (( $this->params['errors']['confirmPassword'] ?? null ) !== null ): ?> -->
        <!-- <span class="app-field__error-txt"> -->
        <!--     <php echo htmlspecialchars($this->params['errors']['confirmPassword'][0]); ?> -->
        <!-- </span> -->
        <!-- <php endif; ?> -->
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

    <!-- <input type="hidden" name="csrf_token" value="<php echo htmlspecialchars($this->params['csrf_token']) ?>" /> -->

    <button 
        class="auth-form__btn  app-btn app-btn--primary"
    >
        Update password
    </button>
</form>

