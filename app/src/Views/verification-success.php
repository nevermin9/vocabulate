
<div class="auth-status-page">
    <div class="auth-status-page__box">
        <div class="auth-status-page__icon-box auth-status-page__icon-box--success">
            <svg class="auth-status-page__icon icon">
                <use href="#check" />
            </svg>
        </div>

        <h1 class="auth-status-page__title">
            Verification Complete!
        </h1>

        <p class="auth-status-page__text">
            Your email address has been successfully verified. You now have full access to **Vocabulate**!
        </p>

        <p class="auth-status-page__text">
            You will be automatically redirected to the main page in <span id="timer">5</span> seconds.
        </p>

        <p class="auth-status-page__text">
            If you are not redirected, or don't want to wait, click the link below:
        </p>

        <a href="/login" 
            class="app-link auth-status-page__text"
        >
            Go to the Main Page
        </a>
    </div>
</div>

<script>
    const REDIRECT_URL = "/";
    let countdown = 5;
    const timerElement = document.getElementById('timer');

    function updateTimer() {
        if (countdown === 0) {
            window.location.href = REDIRECT_URL;
        } else {
            timerElement.textContent = countdown;
            countdown--;
            setTimeout(updateTimer, 1000);
        }
    }

    updateTimer();
</script>
