# ImagePicker
Allows a user to select or remove an image using the media modal. This component
is a thin wrapper around `MediaPicker` and simply sets the allowed types for the
`MediaPicker` to `image`.

For more information on how to use this component, see
[MediaPicker](../media-picker/README.md).


## Usage
Render a simple media upload/replace/remove feature for media for blocks.

``` js
<ImagePicker
  className="image-picker"
  imageSize="thumbnail"
  onReset={() => setAttributes({ imageId: 0 })},
  onUpdate={({ id }) => setAttributes({ imageId: id })}
  value={imageId}
/>
```

## Props
| Prop         | Default     | Required | Type     | Description                                                                |
|--------------|-------------|----------|----------|----------------------------------------------------------------------------|
| className    |             | No       | string   | Class name.                                                                |
| imageSize    | 'thumbnail' | No       | string   | Image size to fetch url for previewing.                                    |
| onReset      |             | Yes      | function | Function to reset imageId to 0.                                            |
| onUpdate     |             | Yes      | function | Function to set imageId value on image selection/upload.                   |
| value        |             | Yes      | integer  | Image id or 0                                                              |
