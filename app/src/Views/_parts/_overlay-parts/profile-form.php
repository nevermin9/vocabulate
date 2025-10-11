<form>
    <label class="app-field">
        <span class="app-field__label">
            Your username
        </span>

        <input
            class="app-field__input"
            type="text"
            pattern="[A-Za-z0-9]+"
            title="Letters and numbers only."
            maxlength="50"
            required
        >
    </label>

    <label class="app-field">
        <span
            class="app-field__label"
        >
            AI Provider
        </span>

        <div
            class="app-field__select-box"
        >
            <select 
                class="app-field__select"
                name="stack-language"
                required
            >
                <option
                    value=""
                    disabled
                    selected
                >Select AI Provider</option>
                <option
                    value="gemini">
                    Gemini
                </option>
                <option
                    value="openai">
                    Openai
                </option>
                <option
                    value="anthropic">
                    Anthropic
                </option>
            </select>
        </div>
    </label>

    <label class="app-field">
        <span class="app-field__label">
            Your Personal AI API Key
        </span>

        <input
            class="app-field__input"
            placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
            type="text"
            pattern="[A-Za-z0-9]+"
            title="Letters and numbers only."
            maxlength="50"
            required
        >
    </label>

    <button
        class="app-btn  app-btn--primary"
    >
        Save
    </button>
</form>

