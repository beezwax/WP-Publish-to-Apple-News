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
import { ruleCorral } from './styles';


const AdminSettings = () => {
  const [{ loading, saving, settings }, setOptions] = useSiteOptions();
  const [originIndex, setOriginIndex] = useState(null);
  const [targetIndex, setTargetIndex] = useState(null);

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
  
  // useEffect(() => {
  //   cleanData();
  // },[]);

  const addRule = () => {
    const updatedRules = settings.apple_news_automation ?? [];
    updatedRules.unshift({
      field: '',
      taxonomy: '',
      term_id: 0,
      value: '',
    });
    saveSettings(updatedRules);
  }

  const deleteRule = (ruleIndex) => {
    const oldRules = settings.apple_news_automation ?? [];
    const updatedRules = oldRules.filter((x, index) => index !== ruleIndex);
    saveSettings(updatedRules);
  }

  const reorderRule = () => {
    // Do nothing if the rule it dropped into its own slot.
    if (originIndex === targetIndex) {
      return;
    }
    const updatedRules = settings.apple_news_automation ?? [];
    // Destructures and reassigns indexed values, effectively swapping them.
    [updatedRules[originIndex], updatedRules[targetIndex]] = [updatedRules[targetIndex], updatedRules[originIndex]]
    // Reset draggable indexes.
    setOriginIndex(null);
    setTargetIndex(null);
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
        <Button
          disabled={loading || saving}
          isPrimary
          onClick={addRule}
        >
          {__('Create New Rule', 'apple-news')}
        </Button>
      <h2>Edit Existing Rules</h2>
      <div style={ruleCorral} className="rule-corral">
        {!loading && settings.apple_news_automation ? (
          settings.apple_news_automation.map((item, index) => (
            <Rule
              field={item.field}
              loading={loading}
              newRule={false}
              onDelete={deleteRule}
              onUpdate={updateRule}
              reorderRule={reorderRule}
              ruleIndex={index}
              saving={saving}
              setOriginIndex={setOriginIndex}
              setTargetIndex={setTargetIndex}
              taxonomy={item.taxonomy}
              term_id={item.term_id}
              value={item.value}
            />
          ))
        ):null}
      </div>
    </div>
  );
};

export default AdminSettings;
