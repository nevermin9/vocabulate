<div 
    class="app-overlay"
    id="app-overlay"
>
    <p>I am system overlay</p>

    <div
        id="app-overlay-handle"
        class="app-overlay__handle"
    ></div>
</div>

<script>
const appOverlayHandle = document.getElementById("app-overlay-handle");
const appOverlay = document.getElementById("app-overlay");
const INIT_TRANSLATE = -100;
let currentTranslate = INIT_TRANSLATE;
let startY = 0, startTranslate = -100, isDragging = false;
const overlayHeight = appOverlay.offsetHeight;

appOverlay.style.transform = `translateY(${currentTranslate}%)`;

appOverlayHandle.addEventListener("pointerdown", (e) => {
    isDragging = true;
    startY = e.clientY;
    startTranslate = currentTranslate;
    appOverlayHandle.setPointerCapture(e.pointerId);
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
appOverlayHandle.addEventListener("pointerup", (e) => {
    isDragging = false;
    currentTranslate = currentTranslate > -50 ? 0 : INIT_TRANSLATE;
    appOverlay.style.transform = `translateY(${currentTranslate}%)`;
    appOverlayHandle.releasePointerCapture(e.pointerId);
});

appOverlay.addEventListener("pointerdown", (e) => {
    if (e.target === appOverlay) {
        appOverlay.style.transform = `translateY(${INIT_TRANSLATE}%)`;
    }
});

</script>


