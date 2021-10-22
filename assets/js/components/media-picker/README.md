# MediaPicker

Allows for a simple media upload/replace/remove feature for media for blocks.

## Usage

``` js
<MediaPicker
  allowedTypes={['application/pdf']}
  className="media-picker"
  onReset={() => setAttributes({ mediaId: 0 })}
  onUpdate={({ id }) => setAttributes({ mediaId: id })}
  value={mediaId}
/>
```

The value of `mediaId` is the ID of the media element, and is destructured from
`props.attributes`.

There are additional options for the MediaPicker that can be configured via the
props listed below. For example, the MediaPicker also supports URL entry as well
as custom preview components, which is useful when you want to render an image
or an embed instead of just a text link to the selected asset.

## Props

| Prop         | Default        | Required | Type     | Description                                                                                                     |
|--------------|----------------|----------|----------|-----------------------------------------------------------------------------------------------------------------|
| allowedTypes | []             | No       | array    | Array with the types of the media to upload/select from the media library. Defaults to empty array (all types). |
| className    | ''             | No       | string   | Class name for the media picker container.                                                                      |
| icon         | 'format-aside' | No       | string   | The name of the Dashicon to use next to the title when no selection has been made yet.                          |
| imageSize    | 'thumbnail'    | No       | string   | If the selected item is an image, the size to display in the preview.                                           |
| onReset      |                | Yes      | function | Function to reset the attachment ID to 0 and/or the attachment URL to an empty string.                          |
| onUpdate     |                | Yes      | function | Function to set the attachment ID on attachment selection/upload.                                               |
| onUpdateURL  | null           | No       | function | Function to set the attachment URL on entry. If not set, the button to enter a URL manually will not display.   |
| preview      | null           | No       | element  | An optional JSX component that accepts an `src` prop as a string to render the preview upon selection.          |
| value        |                | Yes      | integer  | The ID of the selected attachment. 0 represents no selection.                                                   |
| valueURL     | ''             | No       | string   | The URL of the attachment. An empty string represents no selection.                                             |
