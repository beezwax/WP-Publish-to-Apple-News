import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

const Metadata = ({
  isHidden,
  isPaid,
  isPreview,
  isSponsored,
  onChangeIsHidden,
  onChangeIsPaid,
  onChangeIsPreview,
  onChangeIsSponsored,
}) => (
  <PanelBody
    initialOpen={false}
    title={__('Metadata', 'apple-news')}
  >
    <CheckboxControl
      checked={isPaid}
      help={__('Check this to indicate that viewing the article requires a paid subscription. Note that Apple must approve your channel for paid content before using this feature.', 'apple-news')}
      label={__('Paid Article', 'apple-news')}
      onChange={onChangeIsPaid}
    />
    <CheckboxControl
      checked={isPreview}
      help={__('Check this to publish the article as a draft.', 'apple-news')}
      label={__('Preview Article', 'apple-news')}
      onChange={onChangeIsPreview}
    />
    <CheckboxControl
      checked={isHidden}
      help={__('Check this to publish the article as a hidden article. Hidden articles are visible to users who have a link to the article, but do not appear in feeds.', 'apple-news')}
      label={__('Hidden Article', 'apple-news')}
      onChange={onChangeIsHidden}
    />
    <CheckboxControl
      checked={isSponsored}
      help={__('Check this to indicate this article is sponsored content.', 'apple-news')}
      label={__('Sponsored Article', 'apple-news')}
      onChange={onChangeIsSponsored}
    />
  </PanelBody>
);

Metadata.propTypes = {
  isHidden: PropTypes.bool.isRequired,
  isPaid: PropTypes.bool.isRequired,
  isPreview: PropTypes.bool.isRequired,
  isSponsored: PropTypes.bool.isRequired,
  onChangeIsHidden: PropTypes.func.isRequired,
  onChangeIsPaid: PropTypes.func.isRequired,
  onChangeIsPreview: PropTypes.func.isRequired,
  onChangeIsSponsored: PropTypes.func.isRequired,
};

export default Metadata;
