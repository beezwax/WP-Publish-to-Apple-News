/* global React, wp */

import dompurify from 'dompurify';
import PropTypes from 'prop-types';

const {
  element: {
    Fragment,
  },
  data: {
    dispatch,
  }
} = wp;

/**
 * A component to render Apple News notifications.
 */
export default class Notifications extends React.PureComponent {
  // Define PropTypes for this component.
  static propTypes = {
    setNotifications: PropTypes.func.isRequired,
    notifications: PropTypes.arrayOf(PropTypes.shape({
      dismissed: PropTypes.bool,
      dismissible: PropTypes.bool,
      message: PropTypes.string,
      type: PropTypes.string,
    })).isRequired,
  };

  /**
   * Renders this component.
   *
   * @returns object JSX for this component.
   */
  render() {
    const {
      setNotifications,
      notifications,
    } = this.props;

    while(notifications.length) {
      const notification = notifications.shift();
      const type = notification.type === 'success' ? 'snackbar' : 'default';
      dispatch('core/notices').createNotice(
        notification.type,
        dompurify.sanitize(notification.message),
        {
          type,
        });
        setNotifications(notifications);
    };
    return ('');
  }
}
