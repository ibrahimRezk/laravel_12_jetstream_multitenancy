import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',

            "./src/**/*.{html,js}",
    "./node_modules/tw-elements/js/**/*.js"
    ],

    theme: {
        extend: {
            colors : {
                skyOrange : '#464b3f',
                skyYellow : '#554c34'
            },
            fontFamily: {
                // sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                sans: ['Cairo',  ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                progress: {
                    '0%': {
                        width: '0%',
                        marginLeft: '0%'
                    },
                    '20%': {
                        marginLeft: '0%'
                    },
                    '100%': {
                        width: '50%',
                        marginLeft: '100%'
                    },
                    '100%': {
                        width: '50%',
                        marginRight: '100%'
                    },
                },
                "reverse-spin": {
                    from: {
                      transform: 'rotate(360deg)'
                    },
                }
            },

            animation: {
                progress: 'progress 1s ease-in-out infinite  ',
                'reverse-spin': 'reverse-spin 1s linear infinite'
            },
            transitionProperty: {
                height: 'height'
            }
        },
    },

    plugins: [
        forms, typography,
        require("tw-elements/plugin.cjs"), // this is differen from previous one
        plugin(function ({ addUtilities }) {
            addUtilities({
                '.hide-scrollbar': {
                    /* IE and Edge */
                    '-ms-overflow-style': 'none',
                    /* Firefox */
                    'scrollbar-width': 'none',
                    /* Safari and Chrome */
                    '&::-webkit-scrollbar': {
                        display: 'none'
                    }
                },
            })
        })
    ],

    darkMode: 'selector',

    
};
