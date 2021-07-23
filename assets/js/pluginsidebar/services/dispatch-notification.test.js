import DOMPurify from 'dompurify';
import dispatchNotification from './dispatch-notification';

// DOMPurify only works in the browser, so we have to mock its implementation here.
jest.mock('dompurify');
DOMPurify.sanitize = jest.fn((value) => value);

// Mock dispatch.
const dispatch = (type) => {
  switch (type) {
    case 'core/notices':
      return {
        createErrorNotice: (content, options) => ({
          type: 'CREATE_NOTICE',
          context: 'global',
          notice: {
            status: 'error',
            content,
            isDismissible: true,
            id: 'test-error-id',
            type: 'default',
            ...options,
          },
        }),
        createInfoNotice: (content, options) => ({
          type: 'CREATE_NOTICE',
          context: 'global',
          notice: {
            status: 'info',
            content,
            isDismissible: true,
            id: 'test-info-id',
            type: 'default',
            ...options,
          },
        }),
      };
  }
}

test('dispatchNotification should dispatch a success message.', () => {
  const notification = {
    dismissed: false,
    dismissible: false,
    message: 'Test Success Message',
    timestamp: Math.ceil(Date.now() / 1000),
    type: 'success',
  };
  expect(dispatchNotification(dispatch, notification)).toStrictEqual({
    type: 'CREATE_NOTICE',
    context: 'global',
    notice: {
      status: 'info',
      content: 'Test Success Message',
      isDismissible: true,
      id: 'test-info-id',
      type: 'snackbar',
    },
  });
});

test('dispatchNotification should dispatch an error message.', () => {
  const notification = {
    dismissed: false,
    dismissible: false,
    message: 'Test Error Message',
    timestamp: Math.ceil(Date.now() / 1000),
    type: 'error',
  };
  expect(dispatchNotification(dispatch, notification)).toStrictEqual({
    type: 'CREATE_NOTICE',
    context: 'global',
    notice: {
      status: 'error',
      content: 'Test Error Message',
      isDismissible: true,
      id: 'test-error-id',
      type: 'default',
    },
  });
});
