import apiFetch from '@wordpress/api-fetch';
import {
  Button,
  PanelBody,
  SelectControl,
  Spinner,
  TextareaControl,
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import {
  PluginSidebar,
  PluginSidebarMoreMenuItem,
} from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import DOMPurify from 'dompurify';
import React, { useCallback, useEffect, useState } from 'react';

// Components.
import ImagePicker from '../../../components/image-picker';

// Hooks.
import usePostMeta from '../../../services/hooks/use-post-meta';

// Panels.
import ArticleOptions from '../article-options';
import MaturityRating from '../maturity-rating';
import PullQuote from '../pull-quote';

// Utils.
import safeJsonParseArray from '../../../util/safe-json-parse-array';

const Sidebar = () => {
  const [state, setState] = useState({
    autoAssignCategories: false,
    loading: false,
    publishState: '',
    sections: [],
    settings: {
      apiAutosync: false,
      apiAutosyncDelete: false,
      apiAutosyncUpdate: false,
      automaticAssignment: false,
    },
    userCanPublish: false,
  });

  // Destructure values out of state for easier access.
  const {
    autoAssignCategories,
    loading,
    publishState,
    sections,
    settings: {
      apiAutosync,
      apiAutosyncDelete,
      apiAutosyncUpdate,
      automaticAssignment,
    },
    userCanPublish,
  } = state;

  // Get a reference to the dispatch function for notices for use later.
  const dispatchNotice = useDispatch('core/notices');

  // Get the current post ID.
  const {
    notices,
    postId,
    postIsDirty,
    postStatus,
  } = useSelect((select) => {
    const editor = select('core/editor');
    return {
      notices: editor.getEditedPostAttribute('apple_news_notices'),
      postId: editor.getCurrentPostId(),
      postIsDirty: editor.isEditedPostDirty(),
      postStatus: editor.getEditedPostAttribute('status'),
    };
  });

  // Getter/setter for postmeta managed by this PluginSidebar.
  const [{
    apple_news_api_created_at: dateCreated = '',
    apple_news_api_id: apiId = '',
    apple_news_api_modified_at: dateModified = '',
    apple_news_api_revision: revision = '',
    apple_news_api_share_url: shareUrl = '',
    apple_news_coverimage: coverImageId = 0,
    apple_news_coverimage_caption: coverImageCaption = '',
    apple_news_is_hidden: isHidden = false,
    apple_news_is_paid: isPaid = false,
    apple_news_is_preview: isPreview = false,
    apple_news_is_sponsored: isSponsored = false,
    apple_news_maturity_rating: maturityRating = '',
    apple_news_pullquote: pullquoteText = '',
    apple_news_pullquote_position: pullquotePosition = '',
    apple_news_sections: selectedSectionsRaw = '',
  }, setMeta] = usePostMeta();

  // Decode selected sections.
  const selectedSections = safeJsonParseArray(selectedSectionsRaw);

  /**
   * A helper function for displaying a notification to the user.
   * @param {string} message - The notification message displayed to the user.
   * @param {string} type - Optional. The type of message to display. Defaults to success.
   */
  const displayNotification = useCallback((message, type = 'success') => (type === 'success'
    ? dispatchNotice.createInfoNotice(DOMPurify.sanitize(message), { type: 'snackbar' })
    : dispatchNotice.createErrorNotice(DOMPurify.sanitize(message))
  ), [dispatchNotice]);

  /**
   * Sends a request to the REST API to modify the post.
   * @param {string} operation - One of delete, publish, update.
   */
  const modifyPost = async (operation) => {
    setState({
      ...state,
      loading: true,
    });

    try {
      const {
        notifications = [],
        publishState: nextPublishState = '',
      } = await apiFetch({
        data: {
          postId,
        },
        method: 'POST',
        path: `/apple-news/v1/${operation}`,
      });
      notifications.forEach((notification) => displayNotification(
        notification.message,
        notification.type,
      ));
      setState({
        ...state,
        loading: false,
        publishState: nextPublishState,
      });
    } catch (error) {
      displayNotification(error.message, 'error');
      setState({
        ...state,
        loading: false,
      });
    }
  };

  /**
   * A helper function to update which sections are selected.
   * @param {string} name - The name of the section to toggle.
   */
  const toggleSelectedSection = (name) => setMeta('apple_news_sections',
    selectedSections.includes(name)
      ? JSON.stringify(selectedSections.filter((section) => section !== name))
      : JSON.stringify([...selectedSections, name]));

  // On initial load, fetch info from the API into state.
  useEffect(() => {
    (async () => {
      const fetches = [
        async () => ({ publishState: await apiFetch({ path: `/apple-news/v1/get-published-state/${postId}` }) }),
        async () => ({ sections: await apiFetch({ path: '/apple-news/v1/sections' }) }),
        async () => ({ settings: await apiFetch({ path: '/apple-news/v1/get-settings' }) }),
        async () => ({ userCanPublish: await apiFetch({ path: `/apple-news/v1/user-can-publish/${postId}` }) }),
      ];

      // Wait for everything to load, update state, and handle errors.
      try {
        const newState = (await Promise.all(fetches)).reduce((acc, item) => ({
          ...acc,
          ...item,
        }), { ...state });
        newState.autoAssignCategories = (selectedSections === null
          || selectedSections.length === 0)
            && newState.settings.automaticAssignment === true;
        setState(newState);
      } catch (error) {
        displayNotification(error.message, 'error');
      }
    })();
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  // Display notices whenever they change.
  useEffect(() => {
    notices.forEach((notice) => displayNotification(notice.message, notice.type));
  }, [displayNotification, notices]);

  return (
    <>
      <PluginSidebarMoreMenuItem target="publish-to-apple-news">
        {__('Apple News Options', 'apple-news')}
      </PluginSidebarMoreMenuItem>
      <PluginSidebar
        name="publish-to-apple-news"
        title={__('Publish to Apple News Options', 'apple-news')}
      >
        <ArticleOptions
          autoAssignCategories={autoAssignCategories}
          automaticAssignment={automaticAssignment}
          isHidden={isHidden}
          isPaid={isPaid}
          isPreview={isPreview}
          isSponsored={isSponsored}
          onChangeAutoAssignCategories={(next) => {
            setState({
              ...state,
              autoAssignCategories: next,
            });
            setMeta('apple_news_sections', '');
          }}
          onChangeIsHidden={(next) => setMeta('apple_news_is_hidden', next)}
          onChangeIsPaid={(next) => setMeta('apple_news_is_paid', next)}
          onChangeIsPreview={(next) => setMeta('apple_news_is_preview', next)}
          onChangeIsSponsored={(next) => setMeta('apple_news_is_sponsored', next)}
          onChangeSelectedSections={toggleSelectedSection}
          sections={sections}
          selectedSections={selectedSections}
        />
        <MaturityRating
          maturityRating={maturityRating}
          onChangeMaturityRating={(next) => setMeta('apple_news_maturity_rating', next)}
        />
        <PullQuote
          onUpdatePullquotePosition={(next) => setMeta('apple_news_pullquote_position', next)}
          onUpdatePullquoteText={(next) => setMeta('apple_news_pullquote', next)}
          pullquotePosition={pullquotePosition}
          pullquoteText={pullquoteText}
        />
        <PanelBody
          initialOpen={false}
          title={__('Cover Image', 'apple_news')}
        >
          <ImagePicker
            metaKey='apple_news_coverimage'
            onUpdate={onUpdate}
            value={coverImageId}
          />
          <TextareaControl
            label={__('Caption', 'apple_news')}
            value={coverImageCaption}
            onChange={(value) => onUpdate(
              'apple_news_coverimage_caption',
              value
            )}
            placeholder="Add an image caption here."
          />
          <p>
            <em>
              This is optional and can be left blank.
            </em>
          </p>
        </PanelBody>
        <PanelBody
          initialOpen={false}
          title={__('Apple News Publish Information', 'apple-news')}
        >
          {'' !== publishState && 'N/A' !== publishState && (
            <Fragment>
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
            </Fragment>
          )}
        </PanelBody>
        {'publish' === postStatus && userCanPublish && (
          <Fragment>
            {loading ? (
              <Spinner />
            ) : (
              <Fragment>
                {'' !== publishState && 'N/A' !== publishState ? (
                  <Fragment>
                    {! apiAutosyncUpdate && (
                      <Button
                        isPrimary
                        onClick={this.updatePost}
                        style={{ margin: '1em' }}
                      >
                        {__('Update', 'apple-news')}
                      </Button>
                    )}
                    {! apiAutosyncDelete && (
                      <Button
                        isDefault
                        onClick={this.deletePost}
                        style={{ margin: '1em' }}
                      >
                        {__('Delete', 'apple-news')}
                      </Button>
                    )}
                  </Fragment>
                ) : (
                  <Fragment>
                    {
                      postIsDirty && (
                        <div className="components-notice is-warning">
                          <strong>
                            {__(
                              'Please click the Update button above to ensure that all changes are saved before publishing to Apple News.',
                              'apple-news'
                            )}
                          </strong>
                        </div>
                      )
                    }
                    {! apiAutosync && (
                      <Button
                        isPrimary
                        onClick={this.publishPost}
                        style={{ margin: '1em' }}
                      >
                        {__('Publish', 'apple-news')}
                      </Button>
                    )}
                  </Fragment>
                )}
              </Fragment>
            )}
          </Fragment>
        )}
      </PluginSidebar>
    </>
  );
};

export default Sidebar;
