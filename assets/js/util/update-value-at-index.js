/**
 * Given an array of objects, a key, and a value, returns a copy of the array
 * with the value for the key set at the given index.
 * @param {Array} values - An array of objects.
 * @param {string} key - The object key to update.
 * @param {*} value - The value to set for the key.
 * @param {number} index - The index to set the value on.
 * @returns {Array} A copy of the array with the value set for the key at the given index.
 */
const updateValueAtIndex = (values, key, value, index) => {
  const valuesCopy = values.map((item) => ({ ...item }));
  valuesCopy[index][key] = value;
  return valuesCopy;
};

export default updateValueAtIndex;
