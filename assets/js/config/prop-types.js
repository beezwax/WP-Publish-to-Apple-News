/* eslint-disable import/prefer-default-export */

import PropTypes from 'prop-types';

export const METADATA_SHAPE = {
  key: PropTypes.string.isRequired,
  type: PropTypes.oneOf([
    'array',
    'boolean',
    'number',
    'string',
  ]),
  value: PropTypes.oneOfType([
    PropTypes.arrayOf(PropTypes.string),
    PropTypes.bool,
    PropTypes.number,
    PropTypes.string,
  ]),
};

export const SECTION_SHAPE = {
  id: PropTypes.string.isRequired,
  name: PropTypes.string.isRequired,
};
