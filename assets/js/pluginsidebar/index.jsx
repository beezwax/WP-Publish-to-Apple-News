import { registerPlugin } from '@wordpress/plugins';
import React from 'react';

// Components.
import Icon from '../components/icon';
import Sidebar from './sidebar';

registerPlugin('publish-to-apple-news', {
  icon: <Icon />,
  render: Sidebar,
});
