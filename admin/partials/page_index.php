<h1>Apple Export</h1>

<div class="tablenav">
    <div class="actions">
        <button class="button">Export All</button>
    </div>
</div>

<table class="wp-list-table widefat fixed stripped posts">
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach( get_posts() as $post ): ?>
        <tr>
            <td><?php echo $post->post_title; ?></td>
            <td><?php echo $post->post_author; ?></td>
            <td><?php echo date( get_option( 'date_format', $post->post_date ) ); ?></td>
            <td><a href="<?php echo admin_url( 'admin.php?page=apple_export_index&amp;post_id=' . $post->ID ) ?>" class="button">Export</button></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="tablenav">
    <div class="actions">
        <button class="button">Export All</button>
    </div>
</div>

