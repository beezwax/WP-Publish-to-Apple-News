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
   const saveSettings = (updatedRules) => {
    const next = { ...settings, apple_news_automation: updatedRules };

    // Enforce some defaults prior to save.
    // Request will 500 when site_logo === null.
    next.site_logo = next.site_logo ?? 0;
    console.log('settings-pre-save', next);

    // Kick off the save to the server.
    setOptions(next);
  };

  const cleanData = () => {
    saveSettings([]);
  }

  const addRule = (newRule) => {
    const updatedRules = settings.apple_news_automation ?? [];
    updatedRules.push(newRule);
    saveSettings(updatedRules);
  }

  const deleteRule = (ruleIndex) => {
    const oldRules = settings.apple_news_automation ?? [];
    const updatedRules = oldRules.filter((x, index) => index !== ruleIndex);
    saveSettings(updatedRules);
  }

  const reorderRule = (ruleIndex) => {
    const oldRules = settings.apple_news_automation ?? [];
    saveSettings(updatedRules);
  }

  const updateRule = (ruleIndex, updatedRule) => {
    const updatedRules = settings.apple_news_automation ?? [];
    updatedRules[ruleIndex] = updatedRule;
    saveSettings(updatedRules);
  }

  console.log(settings);
  return (
    <div className="apple-news-options__wrapper">
      <Button
        disabled={loading || saving}
        isPrimary
        onClick={() => cleanData()}
      >
        {'Scrub Data'}
      </Button>
      <h2>Add New Rule</h2>
      <Rule
        onAdd={addRule}
        newRule={true}
        saving={saving}
        loading={loading}
      />
      <h2>Edit Existing Rules</h2>
      {!loading && settings.apple_news_automation ? (
        settings.apple_news_automation.map((item, index) => (
          <Rule
            onDelete={deleteRule}
            onUpdate={updateRule}
            field={item.field}
            value={item.value}
            ruleIndex={index}
            loading={loading}
            newRule={false}
            saving={saving}
            taxonomy={item.taxonomy}
            term_id={item.term_id}
          />
        ))
      ):null}
    </div>
  );
};

export default AdminSettings;
