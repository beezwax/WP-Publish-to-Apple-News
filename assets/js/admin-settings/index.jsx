/* global localizedData */
import {
  Button,
  SelectControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React, { useEffect, useState } from 'react';
import useSiteOptions from '../services/hooks/use-site-options';
import Rule from './rule';


const AdminSettings = () => {
  const [{ loading, saving, settings }, setOptions] = useSiteOptions();

  /**
   * Helper function for saving the in-memory settings to the server.
   */
   const saveSettings = (rule, ruleIndex) => {
    // Remove old rule.
    let newVal = settings.apple_news_automation ?? [];
    if (ruleIndex) {
      newVal = settings.apple_news_automation.filter((x, index) => index !== ruleIndex);
    }
    // Add updated rule.
    newVal.push(rule);
    const next = { ...settings, apple_news_automation: newVal };

    // Enforce some defaults prior to save.
    // Request will 500 when site_logo === null.
    next.site_logo = next.site_logo ?? 0;
    console.log('settings-pre-save', next);

    // Kick off the save to the server.
    setOptions(next);
  };

  // Eventually fold this into saveSettings method, only difference is pushing new rule value.
  const deleteRule = (ruleIndex) => {
    // Remove rule.
    const newVal = settings.apple_news_automation.filter((x, index) => index !== ruleIndex);
    console.log(newVal);
    const next = { ...settings, apple_news_automation: newVal };

    // Enforce some defaults prior to save.
    // Request will 500 when site_logo === null.
    next.site_logo = next.site_logo ?? 0;

    // Kick off the save to the server.
    setOptions(next);
  }

  console.log(settings);
  return (
    <div className="apple-news-options__wrapper">
      <h2>Add New Rule</h2>
      <Rule
        newRule={true}
        saving={saving}
        loading={loading}
        ruleIndex={null}
        saveSettings={saveSettings}
      />
      <h2>Edit Existing Rules</h2>
      {!loading && settings.apple_news_automation ? (
        settings.apple_news_automation.map((item, index) => (
          <Rule
            deleteRule={deleteRule}
            field={item.field}
            fieldValue={item.fieldValue}
            ruleIndex={index}
            loading={loading}
            newRule={false}
            saveSettings={saveSettings}
            saving={saving}
            taxonomy={item.taxonomy}
            termId={item.termId}
          />
        ))
      ):null}
    </div>
  );
};

export default AdminSettings;
