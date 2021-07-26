import getMediaUrl from './get-media-url';

test('getMediaUrl should properly get an image thumbnail from an upload object.', () => {
  expect(getMediaUrl({
    media_details: {
      sizes: {
        thumbnail: {
          source_url: '/source/url/1',
        },
      },
    },
    source_url: '/source/url/2',
  }, 'thumbnail')).toEqual('/source/url/1');
});

test('getMediaUrl should fall back to full size image if no thumbnail is present.', () => {
  expect(getMediaUrl({
    source_url: '/source/url/2',
  })).toEqual('/source/url/2');
});

test('getMediaUrl should default to getting the full size media object.', () => {
  expect(getMediaUrl({
    sizes: {
      thumbnail: {
        url: '/url/1',
      },
      full: {
        url: '/url/2',
      },
    },
  })).toEqual('/url/2');
});

test('getMediaUrl should fall back to the full size if the specified size was not found.', () => {
  expect(getMediaUrl({
    media_details: {
      sizes: {
        thumbnail: {
          source_url: '/source/url/1',
        },
      },
    },
    source_url: '/source/url/2',
  }, 'test-thumb-size')).toEqual('/source/url/2');
});

test('getMediaUrl should fall back to the original URL if the size was not found in a media object.', () => {
  expect(getMediaUrl({
    sizes: {
      thumbnail: {
        url: '/url/1',
      },
    },
    url: '/url/3',
  }, 'test-thumb-size')).toEqual('/url/3');
});

test('getMediaUrl should return an empty string if there is no media URL present.', () => {
  expect(getMediaUrl({})).toEqual('');
});
