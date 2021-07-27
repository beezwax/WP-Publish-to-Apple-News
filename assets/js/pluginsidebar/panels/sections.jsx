import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

// Config.
import { SECTION_SHAPE } from '../../config/prop-types';

const Sections = ({
  autoAssignCategories,
  automaticAssignment,
  onChangeAutoAssignCategories,
  onChangeSelectedSections,
  sections,
  selectedSections,
}) => (
  <PanelBody title={__('Sections', 'apple-news')}>
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
        <h4>{__('Manual Section Selection', 'apple-news')}</h4>
        <ul className="apple-news-sections">
          {sections.map(({ id, name }) => (
            <li key={id}>
              <CheckboxControl
                checked={selectedSections.includes(id)}
                label={name}
                onChange={() => onChangeSelectedSections(id)}
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
  </PanelBody>
);

Sections.propTypes = {
  autoAssignCategories: PropTypes.bool.isRequired,
  automaticAssignment: PropTypes.bool.isRequired,
  onChangeAutoAssignCategories: PropTypes.func.isRequired,
  onChangeSelectedSections: PropTypes.func.isRequired,
  sections: PropTypes.arrayOf(PropTypes.shape(SECTION_SHAPE)).isRequired,
  selectedSections: PropTypes.arrayOf(PropTypes.string).isRequired,
};

export default Sections;
