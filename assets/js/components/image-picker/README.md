# ImagePicker

Allows a user to select or remove an image using the media modal or via direct
URL entry. This component is a thin wrapper around `MediaPicker` and sets the
allowed types for the `MediaPicker` to `image` as well as provides a custom
preview component that embeds the selected image in the editor.

For more information on how to use this component, see
[MediaPicker](../media-picker/README.md).

## Usage

``` js
<ImagePicker
  className="image-picker"
  imageSize="thumbnail"
  onReset={() => setAttributes({ imageId: 0 })}
  onUpdate={({ id }) => setAttributes({ imageId: id })}
  value={imageId}
/>
```

## Props

| Prop         | Default     | Required | Type     | Description                                                                                              |
|--------------|-------------|----------|----------|----------------------------------------------------------------------------------------------------------|
| className    | ''          | No       | string   | Class name for the media picker container.                                                               |
| imageSize    | 'thumbnail' | No       | string   | The size to display in the preview.                                                                      |
| onReset      |             | Yes      | function | Function to reset the image ID to 0 and/or the image URL to an empty string.                             |
| onUpdate     |             | Yes      | function | Function to set the image ID on image selection/upload.                                                  |
| onUpdateURL  | null        | No       | function | Function to set the image URL on entry. If not set, the button to enter a URL manually will not display. |
| value        |             | Yes      | integer  | The ID of the selected image. 0 represents no selection.                                                 |
| valueURL     | ''          | No       | string   | The URL of the image. An empty string represents no selection.                                           |
