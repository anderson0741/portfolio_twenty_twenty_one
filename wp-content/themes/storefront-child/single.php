<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package DollarClub
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="article-wrapper">

			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', get_post_type() );
				?>
				<div id="blog-widget">
					<div class="blog-widget-area-1">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Area 1") ) : ?>
						<?php endif;?>
					</div>
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
					<div class="blog-widget-featured-products">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Blog Widget Featured Products") ) : ?>
						<?php endif;?>
					</div>
				</div>
				</div><?php

				the_post_navigation();

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
