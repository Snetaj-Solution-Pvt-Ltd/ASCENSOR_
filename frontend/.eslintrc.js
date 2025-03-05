module.exports = {
    root: true,
    env: {
      browser: true,
      es2021: true,
    },
    extends: [
      "eslint:recommended",
      "plugin:vue/vue3-essential" // Use "vue/vue3-strongly-recommended" for stricter rules
    ],
    parserOptions: {
        ecmaVersion: 2021, // Ensure ES6+ features are supported
        sourceType: "module", // Treat files as ES modules
    },
    rules: {
      "vue/multi-word-component-names": "off", // Disable multi-word component name rule
    },
  };
  