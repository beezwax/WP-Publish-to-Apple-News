import apiFetch from '@wordpress/api-fetch';
import { useDispatch, useSelect } from '@wordpress/data';
import {
  PluginSidebar,
  PluginSidebarMoreMenuItem,
} from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import DOMPurify from 'dompurify';
import React, { useCallback, useEffect, useState } from 'react';

// Hooks.
import usePostMeta from '../services/hooks/use-post-meta';

// Panels.
import CoverImage from './panels/cover-image';
import MaturityRating from './panels/maturity-rating';
import Metadata from './panels/metadata';
import PublishControls from './panels/publish-controls';
import PublishInfo from './panels/publish-info';
import PullQuote from './panels/pull-quote';
import Sections from './panels/sections';
import Slug from './panels/slug';

// Utils.
import safeJsonParseArray from '../util/safe-json-parse-array';

const Sidebar = () => {
  const [state, setState] = useState({
    autoAssignCategories: false,
    loading: false,
    publishState: 'N/A',
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
    apple_news_metadata: metadataRaw = '',
    apple_news_pullquote: pullquoteText = '',
    apple_news_pullquote_position: pullquotePosition = '',
    apple_news_sections: selectedSectionsRaw = '',
    apple_news_slug: slug = '',
  }, setMeta] = usePostMeta();

  // Decode selected sections.
  const metadata = safeJsonParseArray(metadataRaw);
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
          id: postId,
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
   * @param {string} id - The id of the section to toggle.
   */
  const toggleSelectedSection = (id) => setMeta('apple_news_sections',
    selectedSections.includes(id)
      ? JSON.stringify(selectedSections.filter((section) => section !== id))
      : JSON.stringify([...selectedSections, id]));

  // On initial load, fetch info from the API into state.
  useEffect(() => {
    (async () => {
      const fetches = [
        await apiFetch({ path: `/apple-news/v1/get-published-state/${postId}` }),
        await apiFetch({ path: '/apple-news/v1/sections' }),
        await apiFetch({ path: '/apple-news/v1/get-settings' }),
        await apiFetch({ path: `/apple-news/v1/user-can-publish/${postId}` }),
      ];

      // Wait for everything to load, update state, and handle errors.
      try {
        const data = await Promise.all(fetches);
        setState({
          ...state,
          autoAssignCategories: (selectedSections === null || selectedSections.length === 0)
            && data[2].automaticAssignment === true,
          ...data[0],
          sections: data[1],
          settings: data[2],
          ...data[3],
        });
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
        <Sections
          autoAssignCategories={autoAssignCategories}
          automaticAssignment={automaticAssignment}
          onChangeAutoAssignCategories={(next) => {
            setState({
              ...state,
              autoAssignCategories: next,
            });
            setMeta('apple_news_sections', '');
          }}
          onChangeSelectedSections={toggleSelectedSection}
          sections={sections}
          selectedSections={selectedSections}
        />
        <Metadata
          isHidden={isHidden}
          isPaid={isPaid}
          isPreview={isPreview}
          isSponsored={isSponsored}
          metadata={metadata}
          onChangeIsHidden={(next) => setMeta('apple_news_is_hidden', next)}
          onChangeIsPaid={(next) => setMeta('apple_news_is_paid', next)}
          onChangeIsPreview={(next) => setMeta('apple_news_is_preview', next)}
          onChangeIsSponsored={(next) => setMeta('apple_news_is_sponsored', next)}
          onChangeMetadata={(next) => setMeta('apple_news_metadata', JSON.stringify(next))}
        />
        <MaturityRating
          maturityRating={maturityRating}
          onChangeMaturityRating={(next) => setMeta('apple_news_maturity_rating', next)}
        />
        <Slug
          onChangeSlug={(next) => setMeta('apple_news_slug', next)}
          slug={slug}
        />
        <PullQuote
          onChangePullquotePosition={(next) => setMeta('apple_news_pullquote_position', next)}
          onChangePullquoteText={(next) => setMeta('apple_news_pullquote', next)}
          pullquotePosition={pullquotePosition}
          pullquoteText={pullquoteText}
        />
        <CoverImage
          coverImageCaption={coverImageCaption}
          coverImageId={coverImageId}
          onChangeCoverImageCaption={(next) => setMeta('apple_news_coverimage_caption', next)}
          onChangeCoverImageId={(next) => setMeta('apple_news_coverimage', next)}
        />
        {publishState !== 'N/A' ? (
          <PublishInfo
            apiId={apiId}
            dateCreated={dateCreated}
            dateModified={dateModified}
            publishState={publishState}
            revision={revision}
            shareUrl={shareUrl}
          />
        ) : null}
        <PublishControls
          apiAutosync={apiAutosync}
          apiAutosyncDelete={apiAutosyncDelete}
          apiAutosyncUpdate={apiAutosyncUpdate}
          deletePost={() => modifyPost('delete')}
          loading={loading}
          postIsDirty={postIsDirty}
          postStatus={postStatus}
          publishPost={() => modifyPost('publish')}
          publishState={publishState}
          updatePost={() => modifyPost('update')}
          userCanPublish={userCanPublish}
        />
      </PluginSidebar>
    </>
  );
};

export default Sidebar;
