/* global localizedData */
import {
  Button,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import useSiteOptions from '../services/hooks/use-site-options';
import Rule from './rule';
import { ruleCorral } from './styles';


const AdminSettings = () => {
  const [{ loading, setSettings, saving, settings }, saveSettings] = useSiteOptions();
  const busy = loading || saving;
  const [originIndex, setOriginIndex] = useState(null);
  const [targetIndex, setTargetIndex] = useState(null);
  const { apple_news_automation: ruleList } = settings;
  const { fields } = wpLocalizedData;

  /**
   * Helper function for pushing to in-memory settings inside the useSiteOptions hook.
   */
   const updateSettings = (updatedRules) => {
    const next = { ...settings, apple_news_automation: updatedRules };

    // Enforce some defaults prior to save.
    // Request will 500 when site_logo === null.
    next.site_logo = next.site_logo ?? 0;

    // Trigger state hook from useSiteOptions.
    setSettings(next);
  };

  const addRule = () => {
    const updatedRules = [...(ruleList ?? [])];
    updatedRules.unshift({
      field: '',
      taxonomy: '',
      term_id: 0,
      value: '',
    });
    updateSettings(updatedRules);
  }

  const deleteRule = (ruleIndex) => {
    const updatedRules = [...(ruleList ?? [])];
    updatedRules.splice(ruleIndex, 1);
    updateSettings(updatedRules);
  }

  /**
   * Drag and drop logic/re-indexing for Rules.
   */
  const reorderRule = () => {
    // Do nothing if the rule it dropped into its own slot.
    if (originIndex === targetIndex) {
      return;
    }
    const updatedRules = [...(ruleList ?? [])];
    // Destructures and reassigns indexed values, effectively swapping them.
    [updatedRules[originIndex], updatedRules[targetIndex]] = [updatedRules[targetIndex], updatedRules[originIndex]]
    // Reset draggable indexes.
    setOriginIndex(null);
    setTargetIndex(null);
    updateSettings(updatedRules);
  }

  /**
   * Manages Rule component's form state.
   */
  const updateRule = (index, key, value) => {
    const updatedRules = [...(ruleList ?? [])];
    updatedRules[index] = {...updatedRules[index], [key]: value};
    // Need to reset value state in case field changes the resulting value's type.
    if (key === 'field') {
      updatedRules[index] = {
        ...updatedRules[index],
        value: fields[value]?.type === 'boolean' ? 'false' : ''
      };
    }
    updateSettings(updatedRules);
  }

  return (
    <div className="apple-news-options__wrapper">
      <h1>{__('Automation Rules', 'apple-news')}</h1>
      <Button
        disabled={busy}
        isPrimary
        onClick={saveSettings}
        style={{ marginRight: '10px' }}
      >
        {__('Save Settings', 'apple-news')}
      </Button>
      <Button
        disabled={busy}
        isSecondary
        onClick={addRule}
      >
        {__('Create New Rule', 'apple-news')}
      </Button>
      <div style={ruleCorral} className="rule-corral">
        {!loading && ruleList ? (
          ruleList.map((item, index) => (
            <Rule
              busy={busy}
              key={index}
              field={item.field}
              onDelete={deleteRule}
              onUpdate={updateRule}
              reorderRule={reorderRule}
              ruleIndex={index}
              ruleList={ruleList}
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
