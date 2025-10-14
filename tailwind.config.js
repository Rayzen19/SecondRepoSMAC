/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/css/**/*.css",
        "./public/**/*.html",
    ],
    theme: {
        extend: {
            colors: {
                primary: '#2470b7',
                secondary: '#fbf004',
            },
        },
    },
    plugins: [],
};
