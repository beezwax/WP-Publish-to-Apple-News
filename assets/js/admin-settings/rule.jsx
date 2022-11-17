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
  field,
  loading,
  newRule,
  onAdd,
  onDelete,
  onUpdate,
  reorderRule,
  ruleIndex,
  saving,
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
    field: Object.keys(fields)[0],
    taxonomy: Object.keys(taxonomies)[0],
    term_id: 0,
    value: 'false',
  });

  // If existing rule, sync local state with incoming settings.
  // Else set some default values.
  useEffect(() => {
    if(!newRule) {
      setRule({
        field: field,
        taxonomy: taxonomy,
        term_id: term_id,
        value: value,
      })
    } else {
      setRule({
        field: Object.keys(fields)[0],
        taxonomy: Object.keys(taxonomies)[0],
        term_id: 0,
        value: 'false',
      })
    }
  },[taxonomy, term_id, field, value, newRule])

  // Ensures rule.value state is in sync with forms fields.
  // Form inputs for rule.value change conditionally depending on rule.field's value.
  useEffect(() => {
    let defaultValues = {
      Section: sections[0].id,
      Slug: '',
      Theme: 'Default',
    };
    setRule({...rule, value: defaultValues[rule.field] !== undefined ? defaultValues[rule.field] : 'false'});
  }, [rule.field])

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
      draggable={!newRule}
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
        disabled={loading || saving}
        label={__('Taxonomy', 'apple-news')}
        onChange={(next) => setRule({...rule, taxonomy: next})}
        options={Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))}
        value={rule.taxonomy}
      />
      <TextControl
        disabled={loading || saving}
        label={__('Term ID', 'apple-news')}
        onChange={(next) => setRule({...rule, term_id: next})}
        type="number"
        value={rule.term_id}
      />
      <SelectControl
        disabled={loading || saving}
        label={__('Field', 'apple-news')}
        onChange={(next) => setRule({...rule, field: next})}
        options={Object.keys(fields).map((field) => ({ value: field, label: field }))}
        value={rule.field}
      />
      {rule.field === 'Section' ? (
        <SelectControl
          disabled={loading || saving}
          label={__('Sections', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          options={sections.map((sect) => ({ value: sect.id, label: sect.name }))}
          value={rule.value}
        />
      ):null}
      {fields[rule.field] && fields[rule.field].type === 'boolean' ? (
        <ToggleControl
          checked={rule.value === 'true'}
          disabled={loading || saving}
          label={__('True or False', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next.toString()})}
        />
      ):null}
      {rule.field === 'Slug' ? (
        <TextControl
          disabled={loading || saving}
          label={__('Slug', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          value={rule.value}
        />
      ):null}
      {rule.field === 'Theme' ? (
        <SelectControl
          disabled={loading || saving}
          label={__('Themes', 'apple-news')}
          onChange={(next) => setRule({...rule, value: next})}
          options={themes.map((name) => ({ value: name, label: name }))}
          value={rule.value}
        />
      ):null}
      <Button
        disabled={loading || saving}
        isPrimary
        onClick={newRule ? () => onAdd(rule) : () => onUpdate(ruleIndex, rule)}
      >
        {newRule ? __('Create New Rule', 'apple-news') : __('Update Rule', 'apple-news')}
      </Button>
      {!newRule ? (
        <Button
          disabled={loading || saving}
          isDestructive
          onClick={()=> onDelete(ruleIndex)}
        >
          {__('Delete Rule', 'apple-news')}
        </Button>
      ):null}
    </div>
  );
};

export default Rule;
