/* global React, wp */

import PropTypes from 'prop-types';

const {
  compose: {
    compose,
  },
  components: {
    PanelBody,
    CheckboxControl,
    SelectControl,
    TextareaControl,
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
        <div className="components-panel__body is-opened">
          <h3>Sections</h3>
          <CheckboxControl
            label="Main"
          />
          <CheckboxControl
            label="Animals"
          />
          <CheckboxControl
            label="Food"
          />
          <p>
            <em>
              Select the sections in which to publish this article.&nbsp;
              If none are selected, it will be published to the default section.
            </em>
          </p>
          <h3>Preview Article</h3>
          <CheckboxControl
            label="Check this to publish the article as a draft."
          />
          <h3>Hidden Article</h3>
          <CheckboxControl
            // eslint-disable-next-line max-len
            label="Hidden articles are visible to users who have a link to the article, but do not appear in feeds."
          />
          <h3>Sponsored Article</h3>
          <CheckboxControl
            label="Check this to indicate this article is sponsored content."
          />
        </div>
        <PanelBody
          initialOpen={false}
          title={__('Maturity Rating', 'kauffman')}
        >
          <SelectControl
            label="Select Maturity Rating"
            value=""
            options={[
              { label: '', value: '' },
              { label: 'Kids', value: 'KIDS' },
              { label: 'Mature', value: 'MATURE' },
              { label: 'General', value: 'GENERAL' },
            ]}
          />
          <p>
            <em>
              Select the optional maturity rating for this post.
            </em>
          </p>
        </PanelBody>
        <PanelBody
          initialOpen={false}
          title={__('Pull Quote', 'kauffman')}
        >
          <TextareaControl
            label={__('Description', 'kauffman')}
            // eslint-disable-next-line max-len
            placeholder="A pull quote is a key phrase, quotation, or excerpt that has been pulled from an article and used as a graphic element, serving to entice readers into the article or to highlight a key topic."
          />
          <p>
            <em>
              This is optional and can be left blank.
            </em>
          </p>
          <SelectControl
            label="Pull Quote Position"
            value="middle"
            options={[
              { label: 'top', value: 'top' },
              { label: 'middle', value: 'middle' },
              { label: 'bottom', value: 'bottom' },
            ]}
          />
          <p>
            <em>
              The position in the article where the pull quote will appear.
            </em>
          </p>
        </PanelBody>
        <PanelBody
          initialOpen={false}
          title={__('Cover Art', 'kauffman')}
        >
          Hi
        </PanelBody>
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
