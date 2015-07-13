<div class="wrap">
	<h1>Bulk Export Articles</h1>
	<p>The following articles will be published to Apple News. Once started, it
	might take a while, please don't close the browser window.</p>

	<ul class="bulk-export-list">
		<?php foreach ( $articles as $post ): ?>
		<li class="bulk-export-list-item" data-post-id="<?php echo $post->ID ?>">
			<span class="bulk-export-list-item-title">
				<?php echo $post->post_title ?>
			</span>
			<span class="bulk-export-list-item-status pending">
				Pending
			</span>
		</li>
		<?php endforeach; ?>
	</ul>

	<a class="button" href="#">Back</a>
	<a class="button button-primary bulk-export-submit" href="#">Publish All</a>
</div>
