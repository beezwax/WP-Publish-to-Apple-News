<?php
/**
 * Publish to Apple News tests: Gallery_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use \Apple_Exporter\Components\Gallery;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Gallery class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Gallery_Test extends Component_TestCase {

	/**
	 * A data provider that includes various ways of making galleries.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_gallery_content() {
		$content = [];

		// Gutenberg, standard gallery, three images.
		$content[][] = <<<HTML
<!-- wp:gallery {"ids":[%1\$d,%4\$d,%7\$d]} -->
<figure class="wp-block-gallery columns-3 is-cropped">
	<ul class="blocks-gallery-grid">
		<li class="blocks-gallery-item">
			<figure>
				<img src="%2\$s" alt="Alt Text 1" data-id="%1\$d" data-full-url="%2\$s" data-link="%3\$s" class="wp-image-%1\$d"/>
				<figcaption class="blocks-gallery-item__caption">Test Caption 1</figcaption>
			</figure>
		</li>
		<li class="blocks-gallery-item">
			<figure>
				<img src="%5\$s" alt="Alt Text 2" data-id="%4\$d" data-full-url="%5\$s" data-link="%6\$s" class="wp-image-%4\$d"/>
				<figcaption class="blocks-gallery-item__caption">Test Caption 2</figcaption>
			</figure>
		</li>
		<li class="blocks-gallery-item">
			<figure>
				<img src="%8\$s" alt="Alt Text 3" data-id="%7\$d" data-full-url="%8\$s" data-link="%9\$s" class="wp-image-%7\$d"/>
				<figcaption class="blocks-gallery-item__caption">Test Caption 3</figcaption>
			</figure>
		</li>
	</ul>
</figure>
<!-- /wp:gallery -->
HTML;

		// Gutenberg, Jetpack slideshow, three images.
		$content[][] = <<<HTML
<!-- wp:jetpack/slideshow {"ids":[%1\$d,%4\$d,%7\$d],"sizeSlug":"large"} -->
<div class="wp-block-jetpack-slideshow aligncenter" data-effect="slide">
	<div class="wp-block-jetpack-slideshow_container swiper-container">
		<ul class="wp-block-jetpack-slideshow_swiper-wrapper swiper-wrapper">
			<li class="wp-block-jetpack-slideshow_slide swiper-slide">
				<figure>
					<img alt="Alt Text 1" class="wp-block-jetpack-slideshow_image wp-image-%1\$d" data-id="%1\$d" src="%2\$s"/>
					<figcaption class="wp-block-jetpack-slideshow_caption gallery-caption">Test Caption 1</figcaption>
				</figure>
			</li>
			<li class="wp-block-jetpack-slideshow_slide swiper-slide">
				<figure>
					<img alt="Alt Text 2" class="wp-block-jetpack-slideshow_image wp-image-%4\$d" data-id="%4\$d" src="%5\$s"/>
					<figcaption class="wp-block-jetpack-slideshow_caption gallery-caption">Test Caption 2</figcaption>
				</figure>
			</li>
			<li class="wp-block-jetpack-slideshow_slide swiper-slide">
				<figure>
					<img alt="Alt Text 3" class="wp-block-jetpack-slideshow_image wp-image-%7\$d" data-id="%7\$d" src="%8\$s"/>
					<figcaption class="wp-block-jetpack-slideshow_caption gallery-caption">Test Caption 3</figcaption>
				</figure>
			</li>
		</ul>
		<a class="wp-block-jetpack-slideshow_button-prev swiper-button-prev swiper-button-white" role="button"></a>
		<a class="wp-block-jetpack-slideshow_button-next swiper-button-next swiper-button-white" role="button"></a>
		<a aria-label="Pause Slideshow" class="wp-block-jetpack-slideshow_button-pause" role="button"></a>
		<div class="wp-block-jetpack-slideshow_pagination swiper-pagination swiper-pagination-white"></div>
	</div>
</div>
<!-- /wp:jetpack/slideshow -->
HTML;

		// Classic editor, gallery, three images.
		$content[][] = '[gallery ids="%1$d,%4$d,%7$d"]';

		return $content;
	}

	/**
	 * Given post content, ensures that the gallery is properly converted to
	 * Apple News Format.
	 *
	 * @dataProvider data_gallery_content
	 *
	 * @param string $post_content The post content to load into the example post.
	 */
	public function test_component( $post_content ) {
		// Create three new images for testing.
		$images = [
			$this->get_new_attachment( 0, 'Test Caption 1', 'Alt Text 1' ),
			$this->get_new_attachment( 0, 'Test Caption 2', 'Alt Text 2' ),
			$this->get_new_attachment( 0, 'Test Caption 3', 'Alt Text 3' ),
		];

		// Replace the tokens in post_content using real values from the attachments.
		$post_content = sprintf(
			$post_content,
			$images[0],
			wp_get_attachment_image_url( $images[0] ),
			get_permalink( $images[0] ),
			$images[1],
			wp_get_attachment_image_url( $images[1] ),
			get_permalink( $images[1] ),
			$images[2],
			wp_get_attachment_image_url( $images[2] ),
			get_permalink( $images[2] )
		);

		// Create the test post with the remapped post content.
		$post_id = self::factory()->post->create( [ 'post_content' => $post_content ] );

		// Create a new attachment and assign it as the featured image for the cover component.
		set_post_thumbnail(
			$post_id,
			$this->get_new_attachment( 0 )
		);

		// Get the JSON for the article and test the gallery output.
		$json    = $this->get_json_for_post( $post_id );
		$gallery = $json['components'][1]['components'][2];
		$this->assertEquals( 'gallery', $gallery['role'] );
		$this->assertEquals( 3, count( $gallery['items'] ) );
		$this->assertEquals( wp_get_attachment_image_url( $images[0] ), $gallery['items'][0]['URL'] );
		$this->assertEquals( 'Alt Text 1', $gallery['items'][0]['accessibilityCaption'] );
		$this->assertEquals( 'Test Caption 1', $gallery['items'][0]['caption']['text'] );
		$this->assertEquals( wp_get_attachment_image_url( $images[1] ), $gallery['items'][1]['URL'] );
		$this->assertEquals( 'Alt Text 2', $gallery['items'][1]['accessibilityCaption'] );
		$this->assertEquals( 'Test Caption 2', $gallery['items'][1]['caption']['text'] );
		$this->assertEquals( wp_get_attachment_image_url( $images[2] ), $gallery['items'][2]['URL'] );
		$this->assertEquals( 'Alt Text 3', $gallery['items'][2]['accessibilityCaption'] );
		$this->assertEquals( 'Test Caption 3', $gallery['items'][2]['caption']['text'] );
	}
}
