/**
 * Given a value, run JSON.parse on it, but if parsing fails, return null
 * instead of throwing a SyntaxError.
 * @param {*} value - The value to attempt to parse.
 * @returns {*} - The parsed value, or null on failure.
 */
const safeJsonParse = (value) => {
  try {
    return JSON.parse(value);
  } catch (e) {
    return null;
  }
};

export default safeJsonParse;
