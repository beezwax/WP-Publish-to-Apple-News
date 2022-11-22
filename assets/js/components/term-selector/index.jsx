/* eslint-disable react/jsx-props-no-spreading */
import { useDebounce } from '@alleyinteractive/block-editor-tools';
import apiFetch from '@wordpress/api-fetch';
import { SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React, { useCallback, useEffect, useState } from 'react';

// Hooks.
import useTaxonomies from '../../services/hooks/use-taxonomies';

export default function TermSelector({
  onChange,
  taxonomy,
  termId,
  ...rest
}) {
  const taxonomies = useTaxonomies();
  const [searchResults, setSearchResults] = useState([]);
  const [searchTerm, setSearchTerm] = useState(null);
  const [termName, setTermName] = useState('');
  const debouncedSearchTerm = useDebounce(searchTerm, 500);

  /**
   * A helper to get the base URL for a taxonomy, e.g., /wp/v2/categories.
   * @returns {string}
   */
  const getApiBaseUrl = useCallback(
    () => `/${taxonomies[taxonomy].rest_namespace}/${taxonomies[taxonomy].rest_base}`,
    [taxonomies, taxonomy],
  );

  // Load term name via REST API on initial load of field.
  useEffect(() => {
    if (taxonomies[taxonomy] && termId && !termName) {
      (async () => {
        const term = await apiFetch({ path: `${getApiBaseUrl()}/${termId}` });
        if (term.name) {
          setTermName(term.name);
        }
      })();
    }
  }, [getApiBaseUrl, taxonomies, taxonomy, termId, termName]);

  // If the debounced search term changes, search for results from the API.
  useEffect(() => {
    if (debouncedSearchTerm) {
      (async () => setSearchResults(await apiFetch({ path: `${getApiBaseUrl()}?search=${debouncedSearchTerm}` })))();
    }
  }, [debouncedSearchTerm, getApiBaseUrl]);

  return (
    <div>
      <TextControl
        onChange={setSearchTerm}
        value={searchTerm !== null ? searchTerm : termName}
        {...rest}
      />
      {searchResults.length ? (
        <SelectControl
          label={__('Choose a term', 'apple-news')}
          onChange={(next) => {
            const nextTermId = parseInt(next, 10);
            setTermName((searchResults.find(({ id }) => id === nextTermId)).name ?? '');
            setSearchResults([]);
            setSearchTerm(null);
            onChange(nextTermId);
          }}
          options={[
            { label: '', value: '' },
            ...searchResults.map(({ name: label, id: value }) => ({ label, value })),
          ]}
        />
      ) : null}
    </div>
  );
}

TermSelector.propTypes = {
  onChange: PropTypes.func.isRequired,
  taxonomy: PropTypes.string.isRequired,
  termId: PropTypes.number.isRequired,
};
