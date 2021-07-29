/**
 * Given an array of values, returns a copy of the array with the value at the
 * given index removed.
 * @param {Array} values - The array of values to modify.
 * @param {number} index - The index to remove.
 * @returns {Array} A copy of the values array with the value at the specified index removed.
 */
const deleteAtIndex = (values, index) => values.filter((value, idx) => index !== idx);

export default deleteAtIndex;
