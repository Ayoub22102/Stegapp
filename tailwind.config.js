module.exports = {
  // Enable dark mode and apply it based on the 'dark' class
  darkMode: 'class',

  // Specify the paths to all templates and JS files to purge unused styles
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
    'node_modules/flowbite/**/*.js',
  ],

  // Extend the default theme
  theme: {
    extend: {
      // Custom colors for dark mode
      colors: {
        darkBackground: '#1a202c', // Dark background for dark mode
        darkHover: '#2d3748',      // Hover color in dark mode
      },
    },
  },

  // Add Flowbite as a plugin
  plugins: [
    require('flowbite/plugin'),  // Ensure Flowbite integration
  ],
}
