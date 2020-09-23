/* global React, wp */

import dompurify from 'dompurify';
import PropTypes from 'prop-types';

const {
  data: {
    dispatch,
  },
  element: {
    Fragment,
  },
} = wp;

/**
 * A component to render Apple News notifications.
 */
export default class Notifications extends React.PureComponent {
  // Define PropTypes for this component.
  static propTypes = {
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
      notifications,
    } = this.props;

    return (
      <Fragment>
        {notifications.map((notification) => {
          const type = notification.type === 'success' ? 'snackbar' : 'default';
          dispatch('core/notices').createNotice(
            notification.type,
            dompurify.sanitize(notification.message),
            {
              type,
            }
          );
        })}
      </Fragment>
    );
  }
}
