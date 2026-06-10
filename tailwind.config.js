/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./app/Http/Livewire/**/*.php",
        "./app/Livewire/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                primary:   '#F27B20',
                secondary: '#2DB667',
                accent:    '#FFB347',
                dark:      '#2D2D2D',
                light:     '#F8F9FC',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}