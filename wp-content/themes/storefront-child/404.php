<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Gunnars
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'dollarclub' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content" style="text-align: center; height: 50vh">
					<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'dollarclub' ); ?></p>

					<!-- <form action="/shop/" method="get">
						<input type="text" name="s" value="" placeholder="SEARCH" autocomplete="off">
						<button type="submit">Search</button>
					</form> -->

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
