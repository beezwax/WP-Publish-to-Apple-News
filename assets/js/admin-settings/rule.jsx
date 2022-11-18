/* global wp, wpLocalizedData */
import {
  Button,
  SelectControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';
import { useSelect } from '@wordpress/data';
import { ruleCard } from './styles';


const Rule = ({
  busy,
  field,
  onDelete,
  onUpdate,
  reorderRule,
  ruleIndex,
  setOriginIndex,
  setTargetIndex,
  taxonomy,
  term_id,
  value,
}) => {
  const {
    fields,
    sections,
    taxonomies,
    themes,
  } = wpLocalizedData;

  // const { loadingTerms, taxTerms } = useSelect((select) => ({
  //   loadingTerms: select('core/data').isResolving('core', 'getEntityRecords', ['taxonomy', 'category']),
  //   taxTerms: select('core').getEntityRecords('taxonomy', 'category') || [],
  // }));
  // if(!loadingTerms) {
  //   console.log(taxTerms)
  // }

  return (
    <div
      className="rule-wrapper"
      draggable
      style={ruleCard}
      onDragEnd={(e) => {
        const targetEl = document.elementFromPoint(e.clientX, e.clientY);
        // Only reorder if the target element is inside rule flex container.
        if (targetEl.closest('.rule-wrapper')) {
          reorderRule();
        }
      }}
      onDragStart={() => setOriginIndex(ruleIndex)}
      onDragOver={(e) => {
        e.preventDefault();
        setTargetIndex(ruleIndex)
      }}
    >
      <SelectControl
        disabled={busy}
        label={__('Taxonomy', 'apple-news')}
        onChange={(next) => onUpdate('taxonomy', next)}
        options={[
          { value: '', label: __('Select Taxonomy', 'apple-news') },
          ...Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))
        ]}
        value={taxonomy}
      />
      <TextControl
        disabled={busy}
        label={__('Term ID', 'apple-news')}
        onChange={(next) => onUpdate('term_id', next)}
        type="number"
        value={term_id}
      />
      <SelectControl
        disabled={busy}
        label={__('Field', 'apple-news')}
        onChange={(next) => onUpdate('field', next)}
        options={[
          { value: '', label: __('Select Field', 'apple-news') },
          ...Object.keys(fields).map((field) => ({ value: field, label: fields[field].label }))
        ]}
        value={field}
      />
      {fields[field]?.label === 'Section' ? (
        <SelectControl
          disabled={busy}
          label={__('Sections', 'apple-news')}
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
          checked={value === 'true'}
          disabled={busy}
          label={__('True or False', 'apple-news')}
          onChange={(next) => onUpdate('value', next.toString())}
        />
      ):null}
      {fields[field]?.label === 'Slug' ? (
        <TextControl
          disabled={busy}
          label={__('Slug', 'apple-news')}
          onChange={(next) => onUpdate('value', next)}
          value={value}
        />
      ):null}
      {fields[field]?.label === 'Theme' ? (
        <SelectControl
          disabled={busy}
          label={__('Themes', 'apple-news')}
          onChange={(next) => onUpdate('value', next)}
          options={[
            { value: '', label: __('Select Theme', 'apple-news') },
            ...themes.map((name) => ({ value: name, label: name }))
          ]}
          value={value}
        />
      ):null}
      <Button
        disabled={busy}
        isDestructive
        onClick={onDelete}
      >
        {__('Delete Rule', 'apple-news')}
      </Button>
    </div>
  );
};

Rule.propTypes = {
  busy: PropTypes.bool,
  field: PropTypes.string,
  onDelete: PropTypes.func,
  onUpdate: PropTypes.func,
  reorderRule: PropTypes.func,
  ruleIndex: PropTypes.number,
  setOriginIndex: PropTypes.func,
  setTargetIndex: PropTypes.func,
  taxonomy: PropTypes.string,
  term_id: PropTypes.number,
  value: PropTypes.string,
};

export default Rule;
