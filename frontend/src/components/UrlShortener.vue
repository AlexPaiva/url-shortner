<template>
  <div class="url-shortener">
    <h1>My Awesome URL Shortener</h1>
    <form @submit.prevent="shortenUrl">
      <div>
        <label for="url">Enter URL:</label>
        <input type="url" v-model="url" required>
      </div>
      <button type="submit">Shorten URL</button>
    </form>
    <div v-if="shortenedUrl" class="result">
      <h2>Shortened URL:</h2>
      <p>
        <a :href="shortenedUrl" target="_blank">{{ shortenedUrl }}</a>
        <button class="copy-btn" @click="copyToClipboard(shortenedUrl)">Copy to Clipboard</button>
      </p>
    </div>
    <div v-if="error" class="error">
      <h2>Error:</h2>
      <p>{{ error }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Clipboard from 'clipboard';

export default {
  data() {
    return {
      url: '',
      shortenedUrl: null,
      error: null
    };
  },
  mounted() {
    new Clipboard('.copy-btn');
  },
  methods: {
    async shortenUrl() {
      try {
        this.error = null;
        const response = await axios.post(`${process.env.VUE_APP_API_URL}/shorten`, { url: this.url });
        this.shortenedUrl = response.data.shortUrl;
      } catch (error) {
        this.error = error.response?.data?.error || 'An error occurred';
      }
    },
    copyToClipboard(url) {
      const el = document.createElement('textarea');
      el.value = url;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      alert('Copied to clipboard!');
    }
  }
};
</script>

<style scoped>
.url-shortener {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
  font-family: Arial, sans-serif;
}

form {
  margin-bottom: 20px;
}

input[type="url"] {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
}

button {
  padding: 10px 20px;
  margin: 5px;
  background-color: #42b983;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #36a172;
}

.result {
  margin-top: 20px;
}

.error {
  color: red;
  margin-top: 20px;
}
</style>

