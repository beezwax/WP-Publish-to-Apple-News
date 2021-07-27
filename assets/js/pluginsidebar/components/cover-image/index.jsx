import { PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

// Components.
import ImagePicker from '../../../components/image-picker';

const CoverImage = ({
  coverImageCaption,
  coverImageId,
  onUpdateCoverImageCaption,
  onUpdateCoverImageId,
}) => (
  <PanelBody
    initialOpen={false}
    title={__('Cover Image', 'apple-news')}
  >
    <ImagePicker
      onReset={() => onUpdateCoverImageId(0)}
      onUpdate={({ id }) => onUpdateCoverImageId(id)}
      value={coverImageId}
    />
    <TextareaControl
      label={__('Caption', 'apple-news')}
      onChange={onUpdateCoverImageCaption}
      placeholder={__('Add an image caption here.', 'apple-news')}
      value={coverImageCaption}
    />
    <p>
      <em>
        {__('This is optional and can be left blank.', 'apple-news')}
      </em>
    </p>
  </PanelBody>
);

CoverImage.propTypes = {
  coverImageCaption: PropTypes.string.isRequired,
  coverImageId: PropTypes.number.isRequired,
  onUpdateCoverImageCaption: PropTypes.func.isRequired,
  onUpdateCoverImageId: PropTypes.func.isRequired,
};

export default CoverImage;
