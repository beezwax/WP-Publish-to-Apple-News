import safeJsonParse from './safe-json-parse';

/**
 * Given a value, run JSON.parse on it, but if parsing fails, or if
 * what results from the parse is not a standard object, return an empty
 * object rather than a syntax error or a value of another type.
 * @param {*} value - The value to attempt to parse.
 * @returns {object} - The parsed value, or an empty object on failure.
 */
const safeJsonParseObject = (value) => {
  const parsedValue = safeJsonParse(value);

  // Make absolutely sure that the object is a standard object.
  if (parsedValue === null
    || typeof parsedValue !== 'object'
    || Array.isArray(parsedValue)
    || JSON.stringify(parsedValue).indexOf('{') !== 0
  ) {
    return {};
  }

  return parsedValue;
};

export default safeJsonParseObject;
