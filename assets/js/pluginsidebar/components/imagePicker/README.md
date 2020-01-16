# ImagePicker

Allows a user to select or remove an image using the media modal. This component is (currently) intended to save to postmeta.

## Development Guidelines

### Usage

Render an image picker, complete with image preview and remove button:

    <ImagePicker
      metaKey="kauffman_open_graph_image"
      onUpdate={onUpdate}
      value={image}
    /> 

The `onUpdate` function takes two parameters, a `key` and a `value`, intended to be used with saving to a meta key/value pair.

The `value` is the ID of the attachment image.

### Future Work

Extend this component to be more easily used with blocks, rather than assuming that it will be used exclusively with postmeta.
 
