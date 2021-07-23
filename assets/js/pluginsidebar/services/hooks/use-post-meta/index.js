import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

/**
 * A custom React hook that wraps useEntityProp for working with postmeta. It
 * returns an array that contains a copy of postmeta as well as a helper
 * function that sets a meta value for a given key. This hook is intended to
 * reduce boilerplate code in components that need to read and write postmeta.
 * By default, it operates on postmeta for the current post, but you can
 * optionally pass a post type and post ID in order to get and set post meta
 * for an arbitrary post.
 * @param {string} postType - Optional. The post type to get and set meta for.
 *                            Defaults to the post type of the current post.
 * @param {number} postId - Optional. The post ID to get and set meta for.
 *                          Defaults to the ID of the current post.
 * @returns {array} An array containing an object representing postmeta and an update function.
 */
const usePostMeta = (postType = null, postId = null) => {
  // Ensures that we have a post type, since we need it as an argument to useEntityProp.
  const type = useSelect((select) => postType || select('core/editor').getCurrentPostType(), []);

  // Get the value of meta and a function for updating meta from useEntityProp.
  const [meta, setMeta] = useEntityProp('postType', type, 'meta', postId);

  /**
   * A helper function for updating postmeta that accepts a meta key and meta
   * value rather than entirely new meta object.
   * @param {string} key - The meta key to update.
   * @param {*} value - The meta value to update.
   */
  const setPostMeta = (key, value) => setMeta({
    ...meta,
    [key]: value,
  });

  return [meta, setPostMeta];
};

export default usePostMeta;
