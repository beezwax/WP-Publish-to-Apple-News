import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from 'react';

export default function useTaxonomies() {
  const [taxonomies, setTaxonomies] = useState({});

  useEffect(() => (async () => {
    setTaxonomies(await apiFetch({ path: '/wp/v2/taxonomies' }));
  })(), []);

  return taxonomies;
}
