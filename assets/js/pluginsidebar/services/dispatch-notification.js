
/**
 * Given a `dispatch` object and a notification object, dispatches a notification.
 * @param {object} dispatch - A `dispatch` object created from `withDispatch`.
 * @param {object} notification - The notification object.
 * @param {string} notification.message - The notification message.
 * @param {string} notification.type - Optional. The notification type. Defaults to success.
 * @returns {object} The dispatched action.
 */
const dispatchNotification = (
  dispatch,
  {
    message = '',
    type = 'success',
  }
) => (type === 'success'
  ? dispatch('core/notices').createInfoNotice(DOMPurify.sanitize(message), { type: 'snackbar' })
  : dispatch('core/notices').createErrorNotice(DOMPurify.sanitize(message))
);

export default dispatchNotification;
