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
  <PanelBody title={__('Metadata', 'apple-news')}>
    <h3>{__('Paid Article', 'apple-news')}</h3>
    <CheckboxControl
      checked={isPaid}
      label={__('Check this to indicate that viewing the article requires a paid subscription. Note that Apple must approve your channel for paid content before using this feature.', 'apple-news')}
      onChange={onChangeIsPaid}
    />
    <h3>{__('Preview Article', 'apple-news')}</h3>
    <CheckboxControl
      checked={isPreview}
      label={__('Check this to publish the article as a draft.', 'apple-news')}
      onChange={onChangeIsPreview}
    />
    <h3>{__('Hidden Article', 'apple-news')}</h3>
    <CheckboxControl
      checked={isHidden}
      label={__('Hidden articles are visible to users who have a link to the article, but do not appear in feeds.', 'apple-news')}
      onChange={onChangeIsHidden}
    />
    <h3>{__('Sponsored Article', 'apple-news')}</h3>
    <CheckboxControl
      checked={isSponsored}
      label={__('Check this to indicate this article is sponsored content.', 'apple-news')}
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
