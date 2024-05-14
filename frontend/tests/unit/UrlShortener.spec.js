import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import UrlShortener from '@/components/UrlShortener.vue';

jest.mock('axios');

describe('UrlShortener.vue', () => {
  let wrapper;

  beforeAll(() => {
    // Mock window.alert
    global.alert = jest.fn();
  });

  beforeEach(() => {
    wrapper = shallowMount(UrlShortener);
  });

  afterEach(() => {
    wrapper.unmount();
  });

  it('renders the form correctly', () => {
    expect(wrapper.find('h1').text()).toBe('My Awesome URL Shortener');
    expect(wrapper.find('label[for="url"]').text()).toBe('Enter URL:');
    expect(wrapper.find('input[type="url"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').text()).toBe('Shorten URL');
  });

  it('has the correct initial data', () => {
    expect(wrapper.vm.url).toBe('');
    expect(wrapper.vm.shortenedUrl).toBe(null);
    expect(wrapper.vm.error).toBe(null);
  });

  it('shortens URL successfully', async () => {
    const mockResponse = {
      data: {
        shortUrl: 'http://localhost/shortUrl',
      },
    };
    axios.post.mockResolvedValue(mockResponse);

    wrapper.setData({ url: 'https://example.com' });
    await wrapper.vm.shortenUrl();

    expect(wrapper.vm.shortenedUrl).toBe(mockResponse.data.shortUrl);
    expect(wrapper.vm.error).toBe(null);
    expect(wrapper.find('.result').exists()).toBe(true);
    expect(wrapper.find('.result a').attributes('href')).toBe(mockResponse.data.shortUrl);
    expect(wrapper.find('.result a').text()).toBe(mockResponse.data.shortUrl);
  });

  it('handles error correctly', async () => {
    const mockError = {
      response: {
        data: {
          error: 'Invalid URL',
        },
      },
    };
    axios.post.mockRejectedValue(mockError);

    wrapper.setData({ url: 'invalid-url' });
    await wrapper.vm.shortenUrl();

    expect(wrapper.vm.shortenedUrl).toBe(null);
    expect(wrapper.vm.error).toBe(mockError.response.data.error.trim());
    expect(wrapper.find('.error').exists()).toBe(true);
    expect(wrapper.find('.error').text()).toBe('Error:Invalid URL');
  });

  it('displays error when URL is empty', async () => {
    wrapper.setData({ url: '' });
    await wrapper.vm.shortenUrl();

    expect(wrapper.vm.shortenedUrl).toBe(null);
    expect(wrapper.vm.error).toBe('Invalid URL');
    expect(wrapper.find('.error').exists()).toBe(true);
    expect(wrapper.find('.error').text()).toBe('Error:Invalid URL');
  });

  it('copies shortened URL to clipboard', () => {
    document.execCommand = jest.fn();
    const mockResponse = {
      data: {
        shortUrl: 'http://localhost/shortUrl',
      },
    };

    wrapper.setData({ shortenedUrl: mockResponse.data.shortUrl });
    wrapper.vm.copyToClipboard(mockResponse.data.shortUrl);

    expect(document.execCommand).toHaveBeenCalledWith('copy');
    expect(global.alert).toHaveBeenCalledWith('Copied to clipboard!');
  });
});

