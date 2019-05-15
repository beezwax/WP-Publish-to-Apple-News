/* global React, wp */

import PropTypes from 'prop-types';
import ImagePicker from '../imagePicker';

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
          <p>
            <em>
              <a href="https://developer.apple.com/library/content/documentation/General/Conceptual/Apple_News_Format_Ref/CoverArt.html">
                Cover Art
              </a>
              {/* eslint-disable-next-line max-len */}
              &nbsp;will represent your article if editorially chosen for Featured Stories. Cover Art must include your channel logo with text at 24 pt minimum that is related to the headline. The image provided must match the dimensions listed. Limit submissions to 1-3 articles per day.
            </em>
          </p>
          <SelectControl
            label="Orientation"
            value="landscape"
            options={[
              { label: 'Landscape (4:3)', value: 'landscape' },
              { label: 'Portrait (3:4)', value: 'portrait' },
              { label: 'Square (1:1)', value: 'square' },
            ]}
          />
          <p>
            <em>
              {/* eslint-disable-next-line max-len */}
              Note: You must provide the largest size (iPad Pro 12.9 in) in order for your submission to be considered.
            </em>
          </p>
          <div>
            <h4>
              iPad Pro (12.9 in): 1832 x 1374 px
            </h4>
            <ImagePicker
              metaKey="kauffman_open_graph_image"
            />
            <h4>
              iPad (7.9/9.7 in): 1376 x 1032 px
            </h4>
            <ImagePicker
              metaKey="kauffman_open_graph_image"
            />
            <h4>
              iPhone (5.5 in): 1044 x 783 px
            </h4>
            <ImagePicker
              metaKey="kauffman_open_graph_image"
            />
            <h4>
              iPhone (4.7 in): 632 x 474 px
            </h4>
            <ImagePicker
              metaKey="kauffman_open_graph_image"
            />
            <h4>
              iPhone (4 in): 536 x 402 px
            </h4>
            <ImagePicker
              metaKey="kauffman_open_graph_image"
            />
          </div>
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
