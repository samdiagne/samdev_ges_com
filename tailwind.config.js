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
                primary:   '#1E6FBF',
                secondary: '#F5A800',
                accent:    '#4DB8FF',
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