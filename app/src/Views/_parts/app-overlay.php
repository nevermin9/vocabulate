<div 
    class="app-overlay"
    id="app-overlay"
>
    <div class="app-overlay__inner">
        <div class="app-overlay__theme-switcher">
            <?php require "theme-switcher.php"; ?>
        </div>

        <div class="app-overlay__grid">
            <menu class="app-overlay__menu-btns">
                <li>
                    <button
                        class="app-overlay__btn"
                        data-section="profile"
                        type="button"
                    >
                        Profile
                    </button>
                </li>

                <li>
                    <button
                        class="app-overlay__btn"
                        data-section="security"
                        type="button"
                    >
                        Security
                    </button>
                </li>

                <li>
                    <button
                        class="app-overlay__btn"
                        data-section="premium"
                        type="button"
                    >
                        Premium
                    </button>
                </li>

                <li>
                    <a 
                        class="app-overlay__btn  app-overlay__btn--logout"
                        href="/logout"
                    >
                        Logout
                    </a>
                </li>
            </menu>

            <div class="app-overlay__menu">
                <input type="text" />

            </div>
        </div>
    </div>

    <div
        id="app-overlay-handle"
        class="app-overlay__handle"
    ></div>
</div>

<script>
const appOverlayHandle = document.getElementById("app-overlay-handle");
const appOverlay = document.getElementById("app-overlay");
// const INIT_TRANSLATE = -100;
const INIT_TRANSLATE = 0;
let currentTranslate = INIT_TRANSLATE;
let startY = 0, startTranslate = -100, isDragging = false;
const overlayHeight = appOverlay.offsetHeight;

appOverlay.style.transform = `translateY(${currentTranslate}%)`;

function hideOverlay(e) {
    if (e.target === appOverlay) {
        appOverlay.style.transform = `translateY(${INIT_TRANSLATE}%)`;
        currentTranslate = INIT_TRANSLATE;
        appOverlay.removeEventListener("pointerdown", hideOverlay);
    }
}

function handlePointerUp(e) {
    isDragging = false;
    currentTranslate = currentTranslate > -50 ? 0 : INIT_TRANSLATE;
    appOverlay.style.transform = `translateY(${currentTranslate}%)`;
    appOverlayHandle.releasePointerCapture(e.pointerId);

    window.removeEventListener("pointerup", handlePointerUp);

    appOverlay.addEventListener("pointerdown", hideOverlay);
}

appOverlayHandle.addEventListener("pointerdown", (e) => {
    isDragging = true;
    startY = e.clientY;
    startTranslate = currentTranslate;
    appOverlayHandle.setPointerCapture(e.pointerId);
    window.addEventListener("pointerup", handlePointerUp);
});

appOverlayHandle.addEventListener("pointermove", (e) => {
    if (!isDragging) return;
    const deltaY = e.clientY - startY;
    const percentDelta = (deltaY / overlayHeight) * 100;
    let newTranslate = startTranslate + percentDelta;
    newTranslate = Math.min(0, Math.max(newTranslate, -100));
    appOverlay.style.transform = `translateY(${newTranslate}%)`;
    currentTranslate = newTranslate;
});
</script>


