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
  const [test, setTest] = useState('');

  /**
   * Helper function for saving the in-memory settings to the server.
   */
   const saveSettings = () => {
    const next = { ...settings, apple_news_automation: test };

    // Enforce some defaults prior to save.
    next.site_logo = next.site_logo ?? 0;
    console.log('settings-pre-save', next);
    console.log('test');

    // Kick off the save to the server.
    setOptions(next);
  };

  console.log(settings);
  return (
    <div className="apple-news-options__wrapper">
      <TextControl
        disabled={loading || saving}
        label={__('Test', 'bassmaster-plugin')}
        onChange={(next) => setTest(next)}
        type="text"
        value={test}
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
