/* global wp, wpLocalizedData */
import {
  Button,
  SelectControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React, { useEffect, useState } from 'react';
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

  const [rule, setRule] = useState({
    field: field,
    taxonomy: taxonomy,
    term_id: term_id,
    value: value,
  });

  useEffect(() => {
    setRule({
      field: field,
      taxonomy: taxonomy,
      term_id: term_id,
      value: value,
    })
  }, [field, taxonomy, term_id, value]);

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
        if (targetEl.closest('.rule-corral')) {
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
        onChange={(next) => setRule({...rule, taxonomy: next})}
        options={[
          { value: '', label: 'Select Taxonomy' },
          ...Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))
        ]}
        value={rule.taxonomy}
      />
      <TextControl
        disabled={busy}
        label={__('Term ID', 'apple-news')}
        onChange={(next) => setRule({...rule, term_id: next})}
        type="number"
        value={rule.term_id}
      />
      <SelectControl
        disabled={busy}
        label={__('Field', 'apple-news')}
        onChange={(next) => {
          setRule({
            ...rule,
            field: next,
            // Need to reset value state in case field changes the resulting value's type.
            value: fields[next]?.type === 'boolean' ? 'false' : '',
          });
        }}
        options={[
          { value: '', label: 'Select Field' },
          ...Object.keys(fields).map((field) => ({ value: field, label: field }))
        ]}
        value={rule.field}
      />
      {rule.field === 'Section' ? (
        <SelectControl
          disabled={busy}
          label={__('Sections', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          options={[
            { value: '', label: 'Select Section' },
            ...sections.map((sect) => ({ value: sect.id, label: sect.name }))
          ]}
          value={rule.value}
        />
      ):null}
      {fields[rule.field] && fields[rule.field].type === 'boolean' ? (
        <ToggleControl
          checked={rule.value === 'true'}
          disabled={busy}
          label={__('True or False', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next.toString()})}
        />
      ):null}
      {rule.field === 'Slug' ? (
        <TextControl
          disabled={busy}
          label={__('Slug', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          value={rule.value}
        />
      ):null}
      {rule.field === 'Theme' ? (
        <SelectControl
          disabled={busy}
          label={__('Themes', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          options={[
            { value: '', label: 'Select Theme' },
            ...themes.map((name) => ({ value: name, label: name }))
          ]}
          value={rule.value}
        />
      ):null}
      <Button
        disabled={busy}
        isPrimary
        onClick={() => onUpdate(ruleIndex, rule)}
      >
        {__('Update Rule', 'apple-news')}
      </Button>
      <Button
        disabled={busy}
        isDestructive
        onClick={()=> onDelete(ruleIndex)}
      >
        {__('Delete Rule', 'apple-news')}
      </Button>
    </div>
  );
};

export default Rule;
