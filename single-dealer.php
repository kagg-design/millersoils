<?php
/**
 * The template for displaying dealer posts. Derived from template for all pages.
 *
 * This is the template that displays all dealer posts.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package millersoils
 */

get_header(); ?>

<?php
global $post;

// Save current post and replace it by template.
$saved_post = $post;
$template   = get_page_by_title( 'Dealer Template' );

// Add new property to template with the id of original post.
$template                = (array) $template;
$template['original_id'] = $post->ID;
$template                = (object) $template;
$post                    = $template;
?>

<?php do_action( 'rockthemes_pb_frontend_before_page' ); ?>

<?php
// Restore current post.
$post = $saved_post;
?>

<?php
if ( function_exists( 'rockthemes_pb_frontend_sidebar_before_content' ) ) {
	rockthemes_pb_frontend_sidebar_before_content();
}
?>

	<div
			id="primary"
			class="content-area large-<?php echo esc_html( rockthemes_pb_frontend_get_content_columns_after_sidebars() ); ?> column">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() && get_post_meta( $post->ID, '_builder_in_use', true ) !== 'true' ) : ?>
						<header class="entry-header">

							<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
								<?php if ( get_post_meta( $post->ID, '_builder_in_use', true ) !== 'true' ) : ?>
									<div class="entry-thumbnail">
										<?php quasar_get_featured_image(); ?>
									</div>
								<?php else : ?>
									<?php if ( rockthemes_pb_featured_in_builder() !== 'true' ) : ?>
										<div class="entry-thumbnail">
											<?php quasar_get_featured_image(); ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>

							<?php endif; ?>

							<?php
							global $quasar_disable_regular_title;
							if ( ! $quasar_disable_regular_title ) :
								?>
								<h2 class="entry-title"><?php the_title(); ?></h2>
							<?php
							endif;
							?>
						</header><!-- .entry-header -->
					<?php endif; ?>

					<div class="entry-content">
						<?php the_content(); ?>
						<?php
						wp_link_pages(
							[
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'quasar' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							]
						);
						?>
					</div><!-- .entry-content -->

				</article><!-- #post -->

				<?php
				if ( xr_get_option( 'activate_comments_on_pages', false ) ) {
					comments_template();
				}
				?>
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php
if ( function_exists( 'rockthemes_pb_frontend_sidebar_after_content' ) ) {
	rockthemes_pb_frontend_sidebar_after_content();
} else {
	get_sidebar();
}

do_action( 'rockthemes_pb_frontend_after_page' );
get_footer();
