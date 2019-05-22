/* global React, wp */

import PropTypes from 'prop-types';
import ImagePicker from '../imagePicker';

const {
  apiFetch,
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
    meta: PropTypes.shape({
      isPreview: PropTypes.bool,
      isHidden: PropTypes.bool,
      isSponsored: PropTypes.bool,
      maturityRating: PropTypes.string,
      pullquoteText: PropTypes.string,
      pullquotePosition: PropTypes.string,
      selectedSections: PropTypes.string,
      coverArtOrientation: PropTypes.string,
      coverArtIpadPro: PropTypes.string,
      coverArtIpad: PropTypes.string,
      coverArtIphoneXL: PropTypes.string,
      coverArtIphone: PropTypes.string,
      coverArtIphoneSE: PropTypes.string,
    }).isRequired,
    onUpdate: PropTypes.func.isRequired,
    post: PropTypes.shape({}).isRequired,
  };

  constructor(props) {
    super(props);

    this.state = {
      sections: [],
      enableCoverArt: false,
    };

    this.updateSelectedSections = this.updateSelectedSections.bind(this);
  }

  componentDidMount() {
    this.fetchSections();
    this.fetchSettings();
  }

  /**
   * Fetch Apple News Settings
   */
  fetchSettings() {
    const path = '/apple-news/v1/get-settings';

    apiFetch({ path })
      .then((settings) => (this.setState({ ...settings })))
      .catch((error) => console.error(error)); /* eslint-disable-line no-console */
  }

  /**
   * Fetch Apple News Sections
   */
  fetchSections() {
    const path = '/apple-news/v1/sections';

    apiFetch({ path })
      .then((sections) => (this.setState({ sections })))
      .catch((error) => console.error(error)); /* eslint-disable-line no-console */
  }

  updateSelectedSections(checked, name) {
    const { onUpdate, meta: { selectedSections } } = this.props;
    // Need to default to [], else JSON parse fails
    const selectedSectionsArray = JSON.parse(selectedSections || '[]');

    onUpdate(
      'apple_news_selected_sections',
      JSON.stringify(
        checked
          ? [...selectedSectionsArray, name]
          : selectedSectionsArray.filter((section) => section !== name)
      )
    );
  }

  /**
   * Renders the PluginSidebar.
   * @returns {object} JSX component markup.
   */
  render() {
    const {
      meta: {
        isPreview = false,
        isHidden = false,
        isSponsored = false,
        maturityRating = '',
        pullquoteText = '',
        pullquotePosition = '',
        selectedSections = '',
        coverArtOrientation = '',
        coverArtIpadPro = '',
        coverArtIpad = '',
        coverArtIphoneXL = '',
        coverArtIphone = '',
        coverArtIphoneSE = '',
      },
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

    const { sections, enableCoverArt, adminUrl } = this.state;
    const selectedSectionsArray = JSON.parse(selectedSections || '[]');

    return (
      <PluginSidebar
        name="publish-to-apple-news"
        title={__('Publish to Apple News Options', 'apple-news')}
      >
        <div className="components-panel__body is-opened">
          <h3>Sections</h3>
          {
            sections.map(({ id, name }) => (
              <CheckboxControl
                key={id}
                label={name}
                checked={- 1 !== selectedSectionsArray.indexOf(name)}
                onChange={
                  (checked) => this.updateSelectedSections(checked, name)
                }
              />
            ))
          }
          <p>
            <em>
              Select the sections in which to publish this article.&nbsp;
              If none are selected, it will be published to the default section.
            </em>
          </p>
          <h3>Preview Article</h3>
          <CheckboxControl
            label="Check this to publish the article as a draft."
            onChange={(value) => onUpdate(
              'apple_news_is_preview',
              value
            )}
            checked={isPreview}
          />
          <h3>Hidden Article</h3>
          <CheckboxControl
            // eslint-disable-next-line max-len
            label="Hidden articles are visible to users who have a link to the article, but do not appear in feeds."
            onChange={(value) => onUpdate(
              'apple_news_is_hidden',
              value
            )}
            checked={isHidden}
          />
          <h3>Sponsored Article</h3>
          <CheckboxControl
            label="Check this to indicate this article is sponsored content."
            onChange={(value) => onUpdate(
              'apple_news_is_sponsored',
              value
            )}
            checked={isSponsored}
          />
        </div>
        <PanelBody
          initialOpen={false}
          title={__('Maturity Rating', 'apple_news')}
        >
          <SelectControl
            label="Select Maturity Rating"
            value={maturityRating}
            options={[
              { label: '', value: '' },
              { label: 'Kids', value: 'KIDS' },
              { label: 'Mature', value: 'MATURE' },
              { label: 'General', value: 'GENERAL' },
            ]}
            onChange={(value) => onUpdate(
              'apple_news_maturity_rating',
              value
            )}
          />
          <p>
            <em>
              Select the optional maturity rating for this post.
            </em>
          </p>
        </PanelBody>
        <PanelBody
          initialOpen={false}
          title={__('Pull Quote', 'apple_news')}
        >
          <TextareaControl
            label={__('Description', 'apple_news')}
            value={pullquoteText}
            onChange={(value) => onUpdate(
              'apple_news_pullquote',
              value
            )}
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
            value={pullquotePosition || 'middle'}
            options={[
              { label: 'top', value: 'top' },
              { label: 'middle', value: 'middle' },
              { label: 'bottom', value: 'bottom' },
            ]}
            onChange={(value) => onUpdate(
              'apple_news_pullquote_position',
              value
            )}
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
          {
            enableCoverArt ? (
              <div>
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
                  value={coverArtOrientation}
                  options={[
                    { label: 'Landscape (4:3)', value: 'landscape' },
                    { label: 'Portrait (3:4)', value: 'portrait' },
                    { label: 'Square (1:1)', value: 'square' },
                  ]}
                  onChange={(value) => onUpdate(
                    'apple_news_cover_art_orientation',
                    value
                  )}
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
                    metaKey="apple_news_ca_orientation_12_9"
                    onUpdate={onUpdate}
                    value={coverArtIpadPro}
                  />
                  <h4>
                    iPad (7.9/9.7 in): 1376 x 1032 px
                  </h4>
                  <ImagePicker
                    metaKey="apple_news_ca_orientation_9_7"
                    onUpdate={onUpdate}
                    value={coverArtIpad}
                  />
                  <h4>
                    iPhone (5.5 in): 1044 x 783 px
                  </h4>
                  <ImagePicker
                    metaKey="apple_news_ca_orientation_5_5"
                    onUpdate={onUpdate}
                    value={coverArtIphoneXL}
                  />
                  <h4>
                    iPhone (4.7 in): 632 x 474 px
                  </h4>
                  <ImagePicker
                    metaKey="apple_news_ca_orientation_4_7"
                    onUpdate={onUpdate}
                    value={coverArtIphone}
                  />
                  <h4>
                    iPhone (4 in): 536 x 402 px
                  </h4>
                  <ImagePicker
                    metaKey="apple_news_ca_orientation_4_0"
                    onUpdate={onUpdate}
                    value={coverArtIphoneSE}
                  />
                </div>
              </div>
            ) : (
              <p>
                <em>
                  Cover Art must be enabled on the&nbsp;
                  <a href={adminUrl}>settings page</a>
                </em>
              </p>
            )
          }
        </PanelBody>
      </PluginSidebar>
    );
  }
}

export default compose([
  withSelect((select) => {
    const editor = select('core/editor');
    const {
      apple_news_is_preview: isPreview,
      apple_news_is_hidden: isHidden,
      apple_news_is_sponsored: isSponsored,
      apple_news_maturity_rating: maturityRating,
      apple_news_pullquote: pullquoteText,
      apple_news_pullquote_position: pullquotePosition,
      apple_news_selected_sections: selectedSections,
      apple_news_cover_art_orientation: coverArtOrientation,
      apple_news_ca_orientation_12_9: coverArtIpadPro,
      apple_news_ca_orientation_9_7: coverArtIpad,
      apple_news_ca_orientation_5_5: coverArtIphoneXL,
      apple_news_ca_orientation_4_7: coverArtIphone,
      apple_news_ca_orientation_4_0: coverArtIphoneSE,
    } = editor.getEditedPostAttribute('meta');

    return {
      meta: {
        isPreview,
        isHidden,
        isSponsored,
        maturityRating,
        pullquoteText,
        pullquotePosition,
        selectedSections,
        coverArtOrientation,
        coverArtIpadPro,
        coverArtIpad,
        coverArtIphoneXL,
        coverArtIphone,
        coverArtIphoneSE,
      },
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
