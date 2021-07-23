# MediaPicker
Allows for a simple media upload/replace/remove feature for media for blocks.

## Usage
Render a media picker, complete with URL preview, update, and remove button:

``` js
<MediaPicker
  allowedTypes={['image']}
  className="media-picker"
  imageSize="thumbnail"
  onReset={() => setAttributes({ imageId: 0 })},
  onUpdate={({ id }) => setAttributes({ imageId: id })}
  value={imageId}
/>
```

The value of `mediaId` is the ID of the media element, and is destructured from
`props.attributes`.

## Props
| Prop         | Default     | Required | Type     | Description                                                                |
|--------------|-------------|----------|----------|----------------------------------------------------------------------------|
| allowedTypes | all types   | No       | array    | Array with the types of the media to upload/select from the media library. |
| className    |             | No       | string   | Class name.                                                                |
| imageSize    | 'thumbnail' | No       | string   | Image size to fetch url for previewing.                                    |
| onReset      |             | Yes      | function | Function to reset imageId to 0.                                            |
| onUpdate     |             | Yes      | function | Function to set imageId value on image selection/upload.                   |
| value        |             | Yes      | integer  | Media id or 0                                                              |
