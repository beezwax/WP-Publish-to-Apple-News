/* global wp, AppleNewsAutomationConfig */
import {
  Button,
  SelectControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';
// import { useSelect } from '@wordpress/data';


const Rule = ({
  busy,
  field,
  onDelete,
  onDragEnd,
  onUpdate,
  taxonomy,
  termId,
  value,
}) => {
  const {
    fields,
    sections,
    taxonomies,
    themes,
  } = AppleNewsAutomationConfig;

  // const { loadingTerms, taxTerms } = useSelect((select) => ({
  //   loadingTerms: select('core/data').isResolving('core', 'getEntityRecords', ['taxonomy', 'category']),
  //   taxTerms: select('core').getEntityRecords('taxonomy', 'category') || [],
  // }));
  // if(!loadingTerms) {
  //   console.log(taxTerms)
  // }

  return (
    <tr
      className="apple-news-automation-row"
      draggable
      onDragEnd={onDragEnd}
    >
      <td>
        <SelectControl
          aria-labelledby="apple-news-automation-column-taxonomy"
          disabled={busy}
          onChange={(next) => onUpdate('taxonomy', next)}
          options={[
            { value: '', label: __('Select Taxonomy', 'apple-news') },
            ...Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))
          ]}
          value={taxonomy}
        />
      </td>
      <td>
        <TextControl
          aria-labelledby="apple-news-automation-column-term"
          disabled={busy}
          onChange={(next) => onUpdate('term_id', next)}
          type="number"
          value={termId}
        />
      </td>
      <td>
        <SelectControl
          aria-labelledby="apple-news-automation-column-field"
          disabled={busy}
          onChange={(next) => onUpdate('field', next)}
          options={[
            { value: '', label: __('Select Field', 'apple-news') },
            ...Object.keys(fields).map((field) => ({ value: field, label: fields[field].label }))
          ]}
          value={field}
        />
      </td>
      <td>
        {fields[field]?.label === 'Section' ? (
          <SelectControl
            aria-labelledby="apple-news-automation-column-value"
            disabled={busy}
            onChange={(next) => onUpdate('value', next)}
            options={[
              { value: '', label: __('Select Section', 'apple-news') },
              ...sections.map((sect) => ({ value: sect.id, label: sect.name }))
            ]}
            value={value}
          />
        ):null}
        {fields[field]?.type === 'boolean' ? (
          <ToggleControl
            aria-labelledby="apple-news-automation-column-value"
            checked={value === 'true'}
            disabled={busy}
            onChange={(next) => onUpdate('value', next.toString())}
          />
        ):null}
        {fields[field]?.label === 'Slug' ? (
          <TextControl
            aria-labelledby="apple-news-automation-column-value"
            disabled={busy}
            onChange={(next) => onUpdate('value', next)}
            value={value}
          />
        ):null}
        {fields[field]?.label === 'Theme' ? (
          <SelectControl
            aria-labelledby="apple-news-automation-column-value"
            disabled={busy}
            onChange={(next) => onUpdate('value', next)}
            options={[
              { value: '', label: __('Select Theme', 'apple-news') },
              ...themes.map((name) => ({ value: name, label: name }))
            ]}
            value={value}
          />
        ):null}
      </td>
      <td>
        <Button
          disabled={busy}
          isDestructive
          onClick={onDelete}
        >
          {__('Delete Rule', 'apple-news')}
        </Button>
      </td>
    </tr>
  );
};

Rule.propTypes = {
  busy: PropTypes.bool.isRequired,
  field: PropTypes.string.isRequired,
  onDelete: PropTypes.func.isRequired,
  onDragEnd: PropTypes.func.isRequired,
  onUpdate: PropTypes.func.isRequired,
  taxonomy: PropTypes.string.isRequired,
  termId: PropTypes.number.isRequired,
  value: PropTypes.string.isRequired,
};

export default Rule;
