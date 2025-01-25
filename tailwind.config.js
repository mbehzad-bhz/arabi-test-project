/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.html", // Include all HTML files in the root directory
    "./*.php", // Include all PHP files in the root directory
    "./**/*.html", // Include all HTML files in subdirectories
    "./**/*.php", // Include all PHP files in subdirectories
  ],
  theme: {
    extend: {
      fontFamily: {
        Noto: ["Noto", "sans-serif"], // Register Cairo font
      },
    },
  },
  plugins: [],
};
