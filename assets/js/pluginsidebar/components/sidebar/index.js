/* global React, wp */

import PropTypes from 'prop-types';

const {
  compose: {
    compose,
  },
  data: {
    withDispatch,
    withSelect,
  },
  editPost: {
    PluginSidebar,
  },
  i18n: {
    __,
  },
} = wp;

/**
 * A component to render a PluginSidebar for the WP Starter Plugin site.
 */
class Sidebar extends React.PureComponent {
  // Define PropTypes for this component.
  static propTypes = {
    meta: PropTypes.shape({}).isRequired,
    onUpdate: PropTypes.func.isRequired,
    post: PropTypes.shape({}).isRequired,
  };

  /**
   * Renders the PluginSidebar.
   * @returns {object} JSX component markup.
   */
  render() {
    console.log('version 4');
    const {
      onUpdate, // eslint-disable-line no-unused-vars
      post: {
        /**
         * Link is permalink, type is post type. Can be used to make decisions
         * about which components to load in the sidebar based on URL of the
         * post (e.g., the home or front page is '/') or the post type.
         */
        link = '', // eslint-disable-line no-unused-vars
        type = '', // eslint-disable-line no-unused-vars
      },
    } = this.props;

    return (
      <PluginSidebar
        name="publish-to-apple-news"
        title={__('Publish to Apple News Options', 'publish-to-apple-news')}
      >
        <p>Content here!</p>
      </PluginSidebar>
    );
  }
}

export default compose([
  withSelect((select) => {
    const editor = select('core/editor');
    const meta = editor.getEditedPostAttribute('meta');

    return {
      meta,
      post: editor.getCurrentPost(),
    };
  }),
  withDispatch((dispatch) => ({
    onUpdate: (metaKey, metaValue) => {
      dispatch('core/editor').editPost({
        meta: {
          [metaKey]: metaValue,
        },
      });
    },
  })),
])(Sidebar);
