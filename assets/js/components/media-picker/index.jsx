import PropTypes from 'prop-types';
import React from 'react';
import { Button, Spinner } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

// Services.
import getMediaUrl from '../../services/media/get-media-url';

const MediaPicker = ({
  allowedTypes,
  className,
  value,
  imageSize,
  onReset,
  onUpdate,
}) => {
  // Get the media object given the media ID.
  const {
    media = null,
  } = useSelect((select) => ({
    media: select('core').getMedia(value),
  }), [value]);

  // getEntityRecord returns `null` if the load is in progress.
  if (value !== 0 && media === null) {
    return (
      <Spinner />
    );
  }

  return (
    <div
      className={className}
      style={{
        backgroundColor: '#007CBA',
        display: 'inline-block',
        position: 'relative',
      }}
    >
      <MediaUploadCheck>
        <MediaUpload
          title={__('Select/add File', 'apple-news')}
          onSelect={onUpdate}
          allowedTypes={allowedTypes}
          value={value}
          render={({ open }) => (
            <>
              {value !== 0 && media !== null ? (
                <div>
                  <img
                    alt=""
                    src={getMediaUrl(media, imageSize)}
                  />
                  <div
                    style={{
                      background: 'white',
                      left: '50%',
                      padding: 5,
                      position: 'absolute',
                      top: '50%',
                      transform: 'translate(-50%, -50%)',
                      zIndex: 10,
                    }}
                  >
                    <Button
                      isPrimary
                      isLarge
                      onClick={open}
                      style={{ marginBottom: 0 }}
                    >
                      { __('Replace File', 'apple-news')}
                    </Button>
                    <Button
                      isLink
                      isDestructive
                      onClick={onReset}
                      style={{ marginBottom: 0 }}
                    >
                      { __('Remove File', 'apple-news')}
                    </Button>
                  </div>
                </div>
              ) : null}
              {value === 0 ? (
                <div
                  style={{
                    background: 'white',
                    padding: 5,
                  }}
                >
                  <Button
                    isPrimary
                    onClick={open}
                  >
                    { __('Select/add File', 'apple-news')}
                  </Button>
                </div>
              ) : null}
            </>
          )}
        />
      </MediaUploadCheck>
    </div>
  );
};

MediaPicker.defaultProps = {
  allowedTypes: [],
  className: '',
  imageSize: 'thumbnail',
};

MediaPicker.propTypes = {
  allowedTypes: PropTypes.arrayOf([PropTypes.string]),
  className: PropTypes.string,
  value: PropTypes.number.isRequired,
  imageSize: PropTypes.string,
  onReset: PropTypes.func.isRequired,
  onUpdate: PropTypes.func.isRequired,
};

export default MediaPicker;
