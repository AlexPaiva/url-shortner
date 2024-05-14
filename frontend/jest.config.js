module.exports = {
  preset: '@vue/cli-plugin-unit-jest',
  testMatch: ['**/tests/unit/**/*.spec.[jt]s?(x)', '**/tests/integration/**/*.spec.[jt]s?(x)'],
  transform: {
    '^.+\\.vue$': '@vue/vue3-jest',
    '^.+\\.jsx?$': 'babel-jest',
  },
  "moduleNameMapper": {
    "^@/(.*)$": "<rootDir>/src/$1"
  },
  transformIgnorePatterns: [
    '/node_modules/(?!axios)',
  ],
};

