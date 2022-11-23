import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from 'react';

/**
 * A React hook for working with taxonomy configuration data from the WordPress
 * REST API. Caches responses for future use.
 * @returns {object} An object with taxonomy slugs as keys and responses as objects.
 */
export default function useTaxonomies() {
  const [taxonomies, setTaxonomies] = useState({});

  useEffect(() => (async () => {
    setTaxonomies(await apiFetch({ path: '/wp/v2/taxonomies' }));
  })(), []);

  return taxonomies;
}
