/* global localizedData */
import {
  Button,
  SelectControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import React, { useState } from 'react';
import useSiteOptions from '../services/hooks/use-site-options';


const AdminSettings = () => {
  const [{ loading, saving, settings }, setOptions] = useSiteOptions();
  const [rule, setRule] = useState({
    taxonomy: '',
    termId: 0,
    field: '',
    fieldValue: '',
  });

  // TODO, set correct default values based on incoming settings.

  const {
    taxonomies,
    fields
  } = localizedData;

  console.log(rule);

  /**
   * Helper function for saving the in-memory settings to the server.
   */
   const saveSettings = () => {
    // Remove old rule.
    const newVal = settings.apple_news_automation.filter((x) => x.taxonomy !== rule.taxonomy);
    // Add updated rule.
    newVal.push(rule);
    const next = { ...settings, apple_news_automation: newVal };

    // Enforce some defaults prior to save.
    next.site_logo = next.site_logo ?? 0;
    console.log('settings-pre-save', next);

    // Kick off the save to the server.
    setOptions(next);
  };

  console.log(settings);
  return (
    <div className="apple-news-options__wrapper">
      <SelectControl
        disabled={loading || saving}
        label={__('Taxonomy', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, taxonomy: next})}
        options={Object.keys(taxonomies).map((tax) => ({ value: tax, label: tax }))}
        value={rule.taxonomy}
      />
      <TextControl
        disabled={loading || saving}
        label={__('Term ID', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, termId: next})}
        type="number"
        value={rule.termId}
      />
      <SelectControl
        disabled={loading || saving}
        label={__('Field', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, field: next})}
        options={Object.keys(fields).map((field) => ({ value: field, label: field }))}
        value={rule.field}
      />
      <TextControl
        disabled={loading || saving}
        label={__('Field Value', 'apple-news-plugin')}
        onChange={(next) => setRule({...rule, fieldValue: next})}
        value={rule.fieldValue}
      />

      <Button
        disabled={loading || saving}
        isPrimary
        onClick={saveSettings}
      >
        {__('Save Settings', 'apple-news-plugin')}
      </Button>
    </div>
  );
};

export default AdminSettings;
