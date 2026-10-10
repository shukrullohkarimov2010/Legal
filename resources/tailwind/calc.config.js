export default {
    darkMode: 'class',
    content: ['./resources/views/admin/pages/tasks/calc.blade.php'],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                },
                slateplus: {
                    950: '#09111f',
                },
                dark: {
                    bg: '#0f172a',
                    card: '#1e293b',
                    input: '#334155',
                    border: '#475569',
                },
            },
            boxShadow: {
                soft: '0 20px 50px rgba(15, 23, 42, 0.12)',
            },
        },
    },
    plugins: [],
};
