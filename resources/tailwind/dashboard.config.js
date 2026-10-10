export default {
    // Компоненты (<x-language-switcher /> и др.) рендерятся внутрь страницы,
    // поэтому их классы тоже нужны в собранном CSS.
    content: ['./resources/views/dashboard.blade.php', './resources/views/components/**/*.blade.php'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                display: ['Inter', 'system-ui', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },
        },
    },
    plugins: [],
};
