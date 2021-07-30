import deleteAtIndex from './delete-at-index';

test('deleteAtIndex should properly delete items at a given index.', () => {
  const values = ['a', 'b', 'c'];
  expect(deleteAtIndex(values, 0)).toEqual(['b', 'c']);
  expect(deleteAtIndex(values, 1)).toEqual(['a', 'c']);
  expect(deleteAtIndex(values, 2)).toEqual(['a', 'b']);
});
