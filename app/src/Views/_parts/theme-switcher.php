<fieldset class="theme-switcher">
    <div class="theme-switcher__inner">
        <label class="theme-switcher__label">
            <svg class="icon">
                <use href="#laptop-ico" />
            </svg>

            <input
                id="system-theme-radio"
                type="radio"
                name="theme"
                value="system"
            />
        </label>

        <label class="theme-switcher__label">
            <svg class="icon">
                <use href="#moon-waning-crescent" />
            </svg>

            <input
                id="dark-theme-radio"
                type="radio"
                name="theme"
                value="dark"
            />
        </label>

        <label class="theme-switcher__label">
            <svg class="icon">
                <use href="#white-balance-sunny" />
            </svg>

            <input
                id="light-theme-radio"
                type="radio"
                name="theme"
                value="light"
            />
        </label>
    </div>

    <div class="theme-switcher__inner  theme-switcher__inner--clipped">
        <div class="theme-switcher__label  theme-switcher__label--shadow">
            <svg class="icon">
                <use href="#laptop-ico" />
            </svg>
        </div>

        <div class="theme-switcher__label theme-switcher__label--shadow">
            <svg class="icon">
                <use href="#moon-waning-crescent" />
            </svg>
        </div>

        <div class="theme-switcher__label theme-switcher__label--shadow">
            <svg class="icon">
                <use href="#white-balance-sunny" />
            </svg>
        </div>
    </div>
</fieldset>

<script>
const THEME_KEY = "app-theme";
const init = () => {
    const themeFromStorage = localStorage.getItem(THEME_KEY);
    if (themeFromStorage) {
        const radio = document.getElementById(`${themeFromStorage}-theme-radio`);
        radio.checked = true;
    }
};

const initThemeContols = () => {
    const radios = document.querySelectorAll("input[name='theme']");
    const createListener = (theme) => (e) => {
        if (e.target.checked) {
            localStorage.setItem(THEME_KEY, theme);
        }
    };
    Array.from(radios).forEach(r => {
        r.addEventListener("change", createListener(r.value));
    });
};

init();
initThemeContols();
</script>

