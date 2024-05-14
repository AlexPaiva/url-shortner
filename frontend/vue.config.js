const dotenv = require('dotenv');

dotenv.config();

module.exports = {
  devServer: {
    proxy: {
      '/api': {
        target: process.env.VUE_APP_API_URL,
        changeOrigin: true,
        pathRewrite: { '^/api': '' },
      },
    },
  },
};

