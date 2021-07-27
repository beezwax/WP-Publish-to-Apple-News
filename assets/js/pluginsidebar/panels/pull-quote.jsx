import {
  PanelBody,
  SelectControl,
  TextareaControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

const PullQuote = ({
  onUpdatePullquotePosition,
  onUpdatePullquoteText,
  pullquotePosition,
  pullquoteText,
}) => (
  <PanelBody
    initialOpen={false}
    title={__('Pull Quote', 'apple-news')}
  >
    <TextareaControl
      help={__('A pull quote is a key phrase, quotation, or excerpt that has been pulled from an article and used as a graphic element, serving to entice readers into the article or to highlight a key topic.', 'apple-news')}
      label={__('Pull Quote Text', 'apple-news')}
      onChange={onUpdatePullquoteText}
      value={pullquoteText}
    />
    <p>
      <em>
        {__('This is optional and can be left blank.', 'apple-news')}
      </em>
    </p>
    <SelectControl
      label={__('Pull Quote Position', 'apple-news')}
      onChange={onUpdatePullquotePosition}
      options={[
        { label: __('top', 'apple-news'), value: 'top' },
        { label: __('middle', 'apple-news'), value: 'middle' },
        { label: __('bottom', 'apple-news'), value: 'bottom' },
      ]}
      value={pullquotePosition || 'middle'}
    />
    <p>
      <em>
        {__('The position in the article where the pull quote will appear.', 'apple-news')}
      </em>
    </p>
  </PanelBody>
);

PullQuote.propTypes = {
  onUpdatePullquotePosition: PropTypes.func.isRequired,
  onUpdatePullquoteText: PropTypes.func.isRequired,
  pullquotePosition: PropTypes.string.isRequired,
  pullquoteText: PropTypes.string.isRequired,
};

export default PullQuote;
