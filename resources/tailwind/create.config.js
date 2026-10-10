export default {
    darkMode: 'class',
    content: ['./resources/views/contract/create.blade.php', './resources/views/components/**/*.blade.php'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },
            animation: {
                'slide-up': 'slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                'toast-in': 'toastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1)',
                'toast-out': 'toastOut 0.25s ease-in',
                'fade-in-up': 'fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
                'float': 'float 6s ease-in-out infinite',
                'glow-pulse': 'glowPulse 2.5s ease-in-out infinite',
                'shimmer': 'shimmer 2.2s linear infinite',
                'gradient-shift': 'gradientShift 8s ease infinite',
                'mesh-move': 'meshMove 20s ease infinite',
            },
            keyframes: {
                slideUp: { '0%': { opacity: '0', transform: 'translateY(12px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                toastIn: { '0%': { opacity: '0', transform: 'translateX(24px) scale(0.96)' }, '100%': { opacity: '1', transform: 'translateX(0) scale(1)' } },
                toastOut: { '0%': { opacity: '1' }, '100%': { opacity: '0', transform: 'translateX(24px) scale(0.96)' } },
                fadeInUp: { '0%': { opacity: '0', transform: 'translateY(16px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                float: { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-4px)' } },
                glowPulse: { '0%,100%': { boxShadow: '0 0 0 0 rgba(99, 102, 241, 0.4)' }, '50%': { boxShadow: '0 0 28px 4px rgba(99, 102, 241, 0.22)' } },
                shimmer: { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
                gradientShift: { '0%,100%': { backgroundPosition: '0% 50%' }, '50%': { backgroundPosition: '100% 50%' } },
                meshMove: { '0%,100%': { transform: 'translate(0,0) scale(1)' }, '33%': { transform: 'translate(30px,-20px) scale(1.08)' }, '66%': { transform: 'translate(-20px,30px) scale(0.95)' } },
            },
        },
    },
    plugins: [],
};
