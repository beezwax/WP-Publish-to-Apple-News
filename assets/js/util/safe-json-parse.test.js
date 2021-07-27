import safeJsonParse from './safe-json-parse';

test('safeJsonParse should properly decode valid JSON.', () => {
  expect(safeJsonParse('{}')).toEqual({});
  expect(safeJsonParse('true')).toEqual(true);
  expect(safeJsonParse('"foo"')).toEqual('foo');
  expect(safeJsonParse('[1, 5, "false"]')).toEqual([1, 5, 'false']);
  expect(safeJsonParse('null')).toEqual(null);
  expect(safeJsonParse('["a", "b", "c"]')).toEqual(['a', 'b', 'c']);
  expect(safeJsonParse('{"a": "b"}')).toEqual({ a: 'b' });
});

test('Should not choke on invalid JSON.', () => {
  expect(safeJsonParse('')).toEqual(null);
  expect(safeJsonParse(undefined)).toEqual(null);
});
