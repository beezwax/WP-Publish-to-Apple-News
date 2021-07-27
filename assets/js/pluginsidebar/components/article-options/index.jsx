import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

// Config.
import { SECTION_SHAPE } from '../../../config/prop-types';

const ArticleOptions = ({
  autoAssignCategories,
  automaticAssignment,
  isHidden,
  isPaid,
  isPreview,
  isSponsored,
  onChangeAutoAssignCategories,
  onChangeIsHidden,
  onChangeIsPaid,
  onChangeIsPreview,
  onChangeIsSponsored,
  onChangeSelectedSections,
  sections,
  selectedSections,
}) => (
  <PanelBody title={__('General Settings', 'apple-news')}>
    <h3>{__('Sections', 'apple-news')}</h3>
    {automaticAssignment ? (
      <>
        <CheckboxControl
          checked={autoAssignCategories}
          label={__('Assign sections by category', 'apple-news')}
          onChange={onChangeAutoAssignCategories}
        />
        <hr />
      </>
    ) : null}
    {(!autoAssignCategories || !automaticAssignment) && sections && sections.length > 0 ? (
      <>
        <h4>Manual Section Selection</h4>
        <ul className="apple-news-sections">
          {sections.map(({ id, name }) => (
            <li key={id}>
              <CheckboxControl
                checked={selectedSections.includes(id)}
                label={name}
                onChange={onChangeSelectedSections}
              />
            </li>
          ))}
        </ul>
        <p>
          <em>
            {__('Select the sections in which to publish this article. If none are selected, it will be published to the default section.', 'apple-news')}
          </em>
        </p>
      </>
    ) : null}
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
    <h3>Hidden Article</h3>
    <CheckboxControl
      checked={isHidden}
      label={__('Hidden articles are visible to users who have a link to the article, but do not appear in feeds.', 'apple-news')}
      onChange={onChangeIsHidden}
    />
    <h3>Sponsored Article</h3>
    <CheckboxControl
      checked={isSponsored}
      label={__('Check this to indicate this article is sponsored content.', 'apple-news')}
      onChange={onChangeIsSponsored}
    />
  </PanelBody>
);

ArticleOptions.propTypes = {
  autoAssignCategories: PropTypes.bool.isRequired,
  automaticAssignment: PropTypes.bool.isRequired,
  isHidden: PropTypes.bool.isRequired,
  isPaid: PropTypes.bool.isRequired,
  isPreview: PropTypes.bool.isRequired,
  isSponsored: PropTypes.bool.isRequired,
  onChangeAutoAssignCategories: PropTypes.func.isRequired,
  onChangeIsHidden: PropTypes.func.isRequired,
  onChangeIsPaid: PropTypes.func.isRequired,
  onChangeIsPreview: PropTypes.func.isRequired,
  onChangeIsSponsored: PropTypes.func.isRequired,
  onChangeSelectedSections: PropTypes.func.isRequired,
  sections: PropTypes.arrayOf(PropTypes.shape(SECTION_SHAPE)).isRequired,
  selectedSections: PropTypes.arrayOf(PropTypes.string).isRequired,
};

export default ArticleOptions;
