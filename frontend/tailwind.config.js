/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          900: '#0A192F',
          800: '#112240',
        },
        primary: {
          600: '#1E40AF',
          700: '#1D4ED8',
        },
        tealAccent: '#0D9488',
      }
    },
  },
  plugins: [],
}