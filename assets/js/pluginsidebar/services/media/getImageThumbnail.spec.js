/* global describe, expect, it */

import getImageThumbnail from './getImageThumbnail';

describe('getImageThumbnail', () => {
  it('Should properly get an image thumbnail from a media object.', () => {
    expect(getImageThumbnail({
      media_details: {
        sizes: {
          'post-thumbnail': {
            source_url: '/source/url/1',
          },
        },
      },
      source_url: '/source/url/2',
    })).toEqual('/source/url/1');
  });

  it('Should fall back to full size image if no thumbnail is present.', () => {
    expect(getImageThumbnail({
      source_url: '/source/url/2',
    })).toEqual('/source/url/2');
  });

  it('Should return an empty string if there is no image URL present.', () => {
    expect(getImageThumbnail({})).toEqual('');
  });
});
