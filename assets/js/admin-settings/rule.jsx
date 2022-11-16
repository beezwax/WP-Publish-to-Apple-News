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


const Rule = ({
  deleteRule,
  taxonomy,
  termId,
  field,
  fieldValue,
  loading,
  newRule,
  ruleIndex,
  saving,
  saveSettings,
}) => {
  const [rule, setRule] = useState({
    locTaxonomy: '',
    locTermId: 0,
    locField: '',
    locFieldValue: '',
  });

  // If existing rule, sync local state with incoming settings.
  // Else set some default values.
  useEffect(() => {
    if(!newRule) {
      console.log(taxonomy, termId, field, fieldValue);
      setRule({
        locTaxonomy: taxonomy,
        locTermId: termId,
        locField: field,
        locFieldValue: fieldValue,
      })
    } else {
      setRule({
        locTaxonomy: '',
        locTermId: 0,
        locField: '',
        locFieldValue: '',
      })
    }
  },[taxonomy, termId, field, fieldValue, newRule])

  const {
    taxonomies,
    fields
  } = wpLocalizedData;

  // const { loadingTerms, taxTerms } = useSelect((select) => ({
  //   loadingTerms: select('core/data').isResolving('core', 'getEntityRecords', ['taxonomy', 'category']),
  //   taxTerms: select('core').getEntityRecords('taxonomy', 'category') || [],
  // }));
  // if(!loadingTerms) {
  //   console.log(taxTerms)
  // }

  return (
    <div className="apple-news-options__wrapper">
      <SelectControl
        disabled={loading || saving}
        label={__('Taxonomy', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, locTaxonomy: next})}
        options={Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))}
        value={rule.locTaxonomy}
      />
      <TextControl
        disabled={loading || saving}
        label={__('Term ID', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, locTermId: next})}
        type="number"
        value={rule.locTermId}
      />
      <SelectControl
        disabled={loading || saving}
        label={__('Field', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, locField: next})}
        options={Object.keys(fields).map((field) => ({ value: field, label: field }))}
        value={rule.locField}
      />
      {fields[rule.locField] && fields[rule.locField].type === 'boolean' ? (
        <ToggleControl
          checked={rule.locFieldValue}
          disabled={loading || saving}
          label={__('True or False', 'apple-news-plugin')}
          onChange={(next) => setRule({...rule, locFieldValue: next})}
        />
      ):(
        <TextControl
          disabled={loading || saving}
          label={__('Field Value', 'apple-news-plugin')}
          onChange={(next) => setRule({...rule, locFieldValue: next})}
          value={rule.locFieldValue}
        />
      )}

      <Button
        disabled={loading || saving}
        isPrimary
        onClick={()=> saveSettings({
          taxonomy: rule.locTaxonomy,
          termId: rule.locTermId,
          field: rule.locField,
          fieldValue: rule.locFieldValue,
        }, ruleIndex)}
      >
        {newRule ? __('Create New Rule', 'apple-news-plugin') : __('Save Settings', 'apple-news-plugin')}
      </Button>
      {!newRule ? (
        <Button
          disabled={loading || saving}
          isDestructive
          onClick={()=> deleteRule(ruleIndex)}
        >
          {__('Delete Rule', 'apple-news-plugin')}
        </Button>
      ):null}
    </div>
  );
};

export default Rule;
