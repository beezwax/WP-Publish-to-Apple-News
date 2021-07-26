import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

const MaturityRating = ({
  maturityRating,
  onChangeMaturityRating,
}) => (
  <PanelBody
    initialOpen={false}
    title={__('Maturity Rating', 'apple-news')}
  >
    <SelectControl
      label={__('Select Maturity Rating', 'apple-news')}
      onChange={onChangeMaturityRating}
      options={[
        { label: '', value: '' },
        { label: __('Kids', 'apple-news'), value: 'KIDS' },
        { label: __('Mature', 'apple-news'), value: 'MATURE' },
        { label: __('General', 'apple-news'), value: 'GENERAL' },
      ]}
      value={maturityRating}
    />
    <p>
      <em>
        {__('Select the optional maturity rating for this post.', 'apple-news')}
      </em>
    </p>
  </PanelBody>
);

MaturityRating.propTypes = {
  maturityRating: PropTypes.string.isRequired,
  onChangeMaturityRating: PropTypes.func.isRequired,
};

export default MaturityRating;
