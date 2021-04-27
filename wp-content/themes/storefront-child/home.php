<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package DollarClub
 */

get_header();
?>

	<div id="primary" class="content-area">
		<div class="dollarclub-cat-header">
			<?php do_shortcode("[dollarclub-header heading_color='#fff' position_text='center' position_text_vertical='center' background_image='/wp-content/uploads/2020/06/Dollar-Club-Clip.jpg' heading='CHANGED LIVES' image_height='30vw']");?>
		</div>
		<main id="main" class="site-main blog-posts">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<?php
					// Removed blog title 2/5/20
					// the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="archive-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<?php
				/* Start the Loop */
				?><div class="article-wrapper">
						<div class="article-inner-category"><?php

				while ( have_posts() ) :
					the_post();

					/*
					 * Include the Post-Type-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
					 */
					get_template_part( 'template-parts/archive-content', get_post_type() );


				endwhile;
				?>
				</div>
					<div id="blog-widget">
						<div class="blog-widget-area-1">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Area 1") ) : ?>
							<?php endif;?>
						</div>
						<?php /*
						<div class="blog-widget-area-2">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Area 2") ) : ?>
							<?php endif;?>
						</div>
						<div class="blog-widget-area-3">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Area 3") ) : ?>
							<?php endif;?>
						</div>
						<div class="blog-widget-area-4">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Area 4") ) : ?>
							<?php endif;?>
						</div>
						*/ ?>
						<div class="blog-widget-featured-products">
							<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Featured Products") ) : ?>
							<?php endif;?>
						</div>
					</div>
				</div><?php
				the_posts_navigation();

			else :

				get_template_part( 'template-parts/archive-content', 'none' );

			endif;
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
