import PropTypes from 'prop-types';
import React from 'react';

// Components.
import MediaPicker from '../media-picker';

const ImagePicker = ({
  className,
  imageSize,
  onReset,
  onUpdate,
  value,
}) => (
  <MediaPicker
    className={className}
    allowedTypes={['image']}
    imageSize={imageSize}
    onUpdate={onUpdate}
    onReset={onReset}
    value={value}
  />
);

ImagePicker.defaultProps = {
  className: '',
  imageSize: 'thumbnail',
};

ImagePicker.propTypes = {
  className: PropTypes.string,
  imageSize: PropTypes.string,
  onReset: PropTypes.func.isRequired,
  onUpdate: PropTypes.func.isRequired,
  value: PropTypes.number.isRequired,
};

export default ImagePicker;
