import { mount } from '@vue/test-utils';
import axios from 'axios';
import UrlShortener from '@/components/UrlShortener.vue';

jest.mock('axios');

describe('UrlShortener.vue Integration Test', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = mount(UrlShortener, {
      global: {
        mocks: {
          $axios: axios
        }
      }
    });
  });

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount();
    }
  });

  it('renders the form correctly', () => {
    expect(wrapper.find('label[for="url"]').exists()).toBe(true);
    expect(wrapper.find('input[type="url"]').exists()).toBe(true);
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true);
  });

  it('displays shortened URL after successful API call', async () => {
    const mockResponse = { data: { shortUrl: 'http://localhost/shortUrl' } };
    axios.post.mockResolvedValue(mockResponse);

    wrapper.setData({ url: 'https://example.com' });
    await wrapper.find('form').trigger('submit.prevent');

    await wrapper.vm.$nextTick();
    expect(wrapper.find('.result').exists()).toBe(true);
    expect(wrapper.find('.result a').text()).toBe(mockResponse.data.shortUrl);
    expect(wrapper.find('.result a').attributes('href')).toBe(mockResponse.data.shortUrl);
  });

  it('displays an error message when API call fails', async () => {
    const mockError = {
      response: {
        data: {
          error: 'Invalid URL',
        },
      },
    };
    axios.post.mockRejectedValue(mockError);

    wrapper.setData({ url: 'invalid-url' });
    await wrapper.find('form').trigger('submit.prevent');

    await wrapper.vm.$nextTick();
    expect(wrapper.find('.error').exists()).toBe(true);
    expect(wrapper.find('.error').text()).toBe('Error:Invalid URL');
  });

  it('clears the error message when a new valid URL is entered', async () => {
    const mockError = {
      response: {
        data: {
          error: 'Invalid URL',
        },
      },
    };
    axios.post.mockRejectedValue(mockError);

    wrapper.setData({ url: 'invalid-url' });
    await wrapper.find('form').trigger('submit.prevent');

    await wrapper.vm.$nextTick();
    let errorMessage = wrapper.find('.error');
    expect(errorMessage.exists()).toBe(true);
    expect(errorMessage.text()).toBe('Error:Invalid URL');

    const mockSuccessResponse = { data: { shortUrl: 'http://localhost/shortUrl' } };
    axios.post.mockResolvedValue(mockSuccessResponse);

    wrapper.setData({ url: 'https://example.com' });
    await wrapper.find('form').trigger('submit.prevent');

    await wrapper.vm.$nextTick();
    errorMessage = wrapper.find('.error');
    expect(errorMessage.exists()).toBe(false);
    const result = wrapper.find('.result');
    expect(result.exists()).toBe(true);
    expect(result.find('a').text()).toBe(mockSuccessResponse.data.shortUrl);
    expect(result.find('a').attributes('href')).toBe(mockSuccessResponse.data.shortUrl);
  });
});

