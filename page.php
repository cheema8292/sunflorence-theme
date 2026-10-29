<?php
/**
 * Default page template = Shopify rr-policy layout (privacy, refund, shipping, terms … and any simple page).
 * WooCommerce cart / checkout / account pages keep a plain layout for their forms.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$is_wc = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

	if ( $is_wc ) :
		?>
		<div class="sf-container sf-page sf-page--wc">
			<header class="sf-page__head"><h1 class="sf-page__title"><?php the_title(); ?></h1></header>
			<div class="sf-page__content sf-prose"><?php the_content(); ?></div>
		</div>
		<?php
		continue;
	endif;

	sf_ctx( 'post:policy' );
	?>
	<div class="rr-policy">
		<div class="rr-policy__hero">
			<div class="rr-policy__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
			<h1 class="rr-policy__title"><?php the_title(); ?></h1>
			<div class="rr-policy__updated"><?php echo esc_html( sprintf( __( 'Last updated %s', 'sunflorence' ), get_the_modified_date( 'F d, Y' ) ) ); ?></div>
		</div>
		<div class="rr-policy__body">
			<div class="rr-policy__inner">
				<div class="rr-policy__content"><?php the_content(); ?></div>
			</div>
		</div>
	</div>
	<?php
	sf_ctx( 'row' );
endwhile;

get_footer();
