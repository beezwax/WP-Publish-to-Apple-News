import safeJsonParseObject from './safe-json-parse-object';

test('safeJsonParseObject should properly return a parsed object.', () => {
  expect(safeJsonParseObject('{}')).toEqual({});
  expect(safeJsonParseObject('{"a": "b"}')).toEqual({ a: 'b' });
});

test('safeJsonParseObject should return an empty object for any non-object types.', () => {
  expect(safeJsonParseObject('true')).toEqual({});
  expect(safeJsonParseObject('"foo"')).toEqual({});
  expect(safeJsonParseObject('[1, 5, "false"]')).toEqual({});
  expect(safeJsonParseObject('null')).toEqual({});
  expect(safeJsonParseObject('["a", "b", "c"]')).toEqual({});
  expect(safeJsonParseObject('')).toEqual({});
  expect(safeJsonParseObject(undefined)).toEqual({});
});
