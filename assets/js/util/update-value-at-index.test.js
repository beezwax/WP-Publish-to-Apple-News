import updateValueAtIndex from './update-value-at-index';

test('updateValueAtIndex should properly update values at indices.', () => {
  const values = [
    { a: 'b', c: 'd' },
    { e: 'f', g: 'h' },
    { i: 'j', k: 'l' },
  ];
  expect(updateValueAtIndex(values, 'a', 'x', 0)).toEqual([
    { a: 'x', c: 'd' },
    { e: 'f', g: 'h' },
    { i: 'j', k: 'l' },
  ]);
  expect(updateValueAtIndex(values, 'c', 'x', 0)).toEqual([
    { a: 'b', c: 'x' },
    { e: 'f', g: 'h' },
    { i: 'j', k: 'l' },
  ]);
  expect(updateValueAtIndex(values, 'e', 'x', 1)).toEqual([
    { a: 'b', c: 'd' },
    { e: 'x', g: 'h' },
    { i: 'j', k: 'l' },
  ]);
  expect(updateValueAtIndex(values, 'g', 'x', 1)).toEqual([
    { a: 'b', c: 'd' },
    { e: 'f', g: 'x' },
    { i: 'j', k: 'l' },
  ]);
  expect(updateValueAtIndex(values, 'i', 'x', 2)).toEqual([
    { a: 'b', c: 'd' },
    { e: 'f', g: 'h' },
    { i: 'x', k: 'l' },
  ]);
  expect(updateValueAtIndex(values, 'k', 'x', 2)).toEqual([
    { a: 'b', c: 'd' },
    { e: 'f', g: 'h' },
    { i: 'j', k: 'x' },
  ]);
});
