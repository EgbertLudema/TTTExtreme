document.addEventListener('DOMContentLoaded', (event) => {
    const themeSelect = document.getElementById('themeSelect');
    const themeLabel = document.getElementById('themeLabel');

    themeSelect.addEventListener('change', (event) => {
        const selectedTheme = event.target.value;
        const element = document.body;

        // Remove all existing theme classes
        element.classList.remove("dark-mode", "purple-mode", "oldSchool-mode");

        // Add new theme class
        if (selectedTheme) {
            element.classList.add(`${selectedTheme}-mode`);
        }
    });
});