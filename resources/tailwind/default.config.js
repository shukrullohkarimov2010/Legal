// Страницы без собственного tailwind.config (раньше — cdn.tailwindcss.com).
export default {
    content: [
        './resources/views/welcome.blade.php',
        './resources/views/contract/compare.blade.php',
        './resources/views/admin/pages/tasks/index.blade.php',
        './resources/views/admin/pages/tasks/chat.blade.php',
        './resources/views/admin/pages/tasks/create.blade.php',
        // tasks/index и tasks/create подключают admin.partials.header, а тот —
        // <x-language-switcher />: их классы должны попасть в CSS.
        './resources/views/admin/partials/*.blade.php',
        './resources/views/components/**/*.blade.php',
    ],
    theme: { extend: {} },
    plugins: [],
};
