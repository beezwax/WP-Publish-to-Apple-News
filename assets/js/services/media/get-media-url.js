/**
 * Given a media object returned from the WordPress REST API, extracts the
 * URL for the media item at the requested size if it exists, or the full
 * size if it does not. Returns an empty string if unable to find either.
 * @param {object} media - A media object returned by the WordPress API.
 * @param {string} size - Media size to request. Default: full
 * @returns {string} - The URL to the asset, or an empty string on failure.
 */
const getMediaUrl = (media, size = 'full') => {
  const {
    media_details: {
      sizes: {
        [size]: {
          source_url: firstChoice = '',
        } = {},
        full: {
          source_url: thirdChoice = '',
        } = {},
      } = {},
    } = {},
    sizes: {
      [size]: {
        url: secondChoice = '',
      } = {},
      full: {
        url: fourthChoice = '',
      } = {},
    } = {},
    source_url: fifthChoice = '',
    url: sixthChoice = '',
  } = media;

  return firstChoice
    || secondChoice
    || thirdChoice
    || fourthChoice
    || fifthChoice
    || sixthChoice
    || '';
};

export default getMediaUrl;
