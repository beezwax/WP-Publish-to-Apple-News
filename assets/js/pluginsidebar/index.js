/* global React, wp */

import Icon from './components/icon';
import Sidebar from './components/sidebar';

const {
  plugins: {
    registerPlugin,
  },
} = wp;

registerPlugin('publish-to-apple-news', {
  icon: <Icon />,
  render: Sidebar,
});
