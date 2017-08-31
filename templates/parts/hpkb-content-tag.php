<?php hpkb_get_template_part( 'parts/hpkb-breadcrumb', 'tag' ); ?>

<?php if ( have_posts() ) : ?>
	<ul class="hpkb-article-list">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php hpkb_get_template_part( 'parts/hpkb-article-item', 'tag' ); ?>
		<?php endwhile; ?>
	</ul>
<?php endif; ?>

<?php hpkb_get_template_part( 'parts/hpkb-post-navigation', 'tag' );
<?php hpkb_get_template_part( 'parts/hpkb-searchform', 'tag' );
