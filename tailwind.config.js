/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.html",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        'terdav-blue': '#4a7c2c',
        'terdav-orange': '#8b4513',
        'text-dark': '#2d1a10',
      },
      fontFamily: {
        'serif': ['Montserrat', 'serif'],
      },
    },
  },
  plugins: [],
}