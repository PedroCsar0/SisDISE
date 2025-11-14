import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {

  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: {
          DEFAULT: '#4f46e5', // Cor principal (indigo-600)
          light: '#6366f1',   // Cor mais clara (indigo-500)
          dark: '#4338ca',    // Cor mais escura (indigo-700)
        },
      },
    },
  },
  plugins: [],
}
