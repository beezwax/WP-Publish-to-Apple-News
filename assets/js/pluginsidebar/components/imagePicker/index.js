/* global React, wp */

import PropTypes from 'prop-types';

import getImageThumbnail from '../../services/media/getImageThumbnail';

const {
  components: {
    Button,
  },
  data: {
    withSelect,
  },
  editor: {
    MediaPlaceholder,
  },
  i18n: {
    __,
  },
} = wp;

/**
 * A component representing the Image Picker sidebar control.
 */
class ImagePicker extends React.PureComponent {
  // Define PropTypes for this component.
  static propTypes = {
    media: PropTypes.shape({}).isRequired,
    metaKey: PropTypes.string.isRequired,
    onUpdate: PropTypes.func.isRequired,
    value: PropTypes.number.isRequired,
  };

  /**
   * Renders the image picker.
   * @returns {object} JSX component markup.
   */
  render() {
    const {
      media,
      metaKey,
      onUpdate,
      value,
    } = this.props;

    // If we found media, show it and give the user an option to remove it.
    if (value && media && media.id) {
      return (
        <div>
          <p>
            <img
              alt=""
              src={getImageThumbnail(media)}
            />
          </p>
          <p>
            <Button
              isPrimary
              onClick={() => {
                onUpdate(metaKey, 0);
              }}
            >
              {__('Remove image', 'kauffman')}
            </Button>
          </p>
        </div>
      );
    }

    // Show the image picker and give the user an option to add it.
    return (
      <MediaPlaceholder
        accept="image/*"
        allowedTypes={['image']}
        icon="format-image"
        labels={{
          title: __('Select Image', 'kauffman'),
        }}
        onSelect={({ id }) => {
          onUpdate(metaKey, id);
        }}
      />
    );
  }
}

export default withSelect((select, ownProps) => {
  const {
    value,
  } = ownProps;
  const {
    getMedia,
  } = select('core');

  return {
    media: value ? getMedia(value) || {} : {},
  };
})(ImagePicker);
