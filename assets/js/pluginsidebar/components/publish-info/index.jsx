import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import PropTypes from 'prop-types';
import React from 'react';

const PublishInfo = ({
  apiId,
  dateCreated,
  dateModified,
  revision,
  shareUrl,
  publishState,
}) => (
  <PanelBody
    initialOpen={false}
    title={__('Apple News Publish Information', 'apple-news')}
  >
    {publishState !== 'N/A' ? (
      <>
        <h4>{__('API Id', 'apple-news')}</h4>
        <p>{apiId}</p>
        <h4>{__('Created On', 'apple-news')}</h4>
        <p>{dateCreated}</p>
        <h4>{__('Last Updated On', 'apple-news')}</h4>
        <p>{dateModified}</p>
        <h4>{__('Share URL', 'apple-news')}</h4>
        <p>{shareUrl}</p>
        <h4>{__('Revision', 'apple-news')}</h4>
        <p>{revision}</p>
        <h4>{__('Publish State', 'apple-news')}</h4>
        <p>{publishState}</p>
      </>
    ) : null}
  </PanelBody>
);

PublishInfo.propTypes = {
  apiId: PropTypes.string.isRequired,
  dateCreated: PropTypes.string.isRequired,
  dateModified: PropTypes.string.isRequired,
  revision: PropTypes.string.isRequired,
  shareUrl: PropTypes.string.isRequired,
  publishState: PropTypes.string.isRequired,
};

export default PublishInfo;
