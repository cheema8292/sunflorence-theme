<?php
/**
 * Search results — Shopify sections/rr-search.liquid (products first, then pages).
 * Texts + popular searches: Sunflorence → Pages → Search page.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
sf_ctx( 'opt:search' );

$term     = get_search_query();
$products = array();
$page_results    = array();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( 'product' === get_post_type() && function_exists( 'wc_get_product' ) ) {
			$p = wc_get_product( get_the_ID() );
			if ( $p && $p->is_visible() ) {
				$products[] = $p;
			}
		} elseif ( in_array( get_post_type(), array( 'page', 'post' ), true ) ) {
			$page_results[] = get_post();
		}
	}
	rewind_posts();
}
$count       = count( $products ) + count( $page_results );
$total       = (int) $GLOBALS['wp_query']->found_posts;
$suggestions = sf_blocks( 'suggestion' );
if ( ! $suggestions ) {
	$suggestions = array_map( function ( $t ) {
		return array( 'term' => $t );
	}, array( 'Facial Cleanser', 'Makeup Brush', 'K-Beauty', 'Shampoo', 'Oral Care' ) );
}
?>
<div class="rr-search">
	<div class="rr-search__hero">
		<div class="rr-search__eyebrow"><?php echo esc_html( sf_s( 'eyebrow' ) ); ?></div>
		<h1 class="rr-search__title">
			<?php if ( '' !== $term ) : ?>
				<?php esc_html_e( 'Results for', 'sunflorence' ); ?> <em>"<?php echo esc_html( $term ); ?>"</em>
			<?php else : ?>
				<?php echo esc_html( sf_s( 'heading' ) ); ?>
			<?php endif; ?>
		</h1>
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="rr-search__form" role="search">
			<input type="search" name="s" class="rr-search__input" placeholder="<?php echo esc_attr( sf_s( 'placeholder' ) ); ?>" value="<?php echo esc_attr( $term ); ?>" autocomplete="off" aria-label="<?php esc_attr_e( 'Search', 'sunflorence' ); ?>">
			<button type="submit" class="rr-search__btn">
				<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
				<?php esc_html_e( 'Search', 'sunflorence' ); ?>
			</button>
		</form>
	</div>

	<div class="rr-search__body">
		<div class="rr-search__body-inner">
			<?php if ( '' !== $term ) : ?>
				<div class="rr-search__meta">
					<div class="rr-search__meta-text">
						<?php if ( ! $count ) : ?>
							<?php esc_html_e( 'No results for', 'sunflorence' ); ?> <strong>"<?php echo esc_html( $term ); ?>"</strong>
						<?php else : ?>
							<strong><?php echo esc_html( $total ); ?></strong> <?php echo esc_html( _n( 'result for', 'results for', $total, 'sunflorence' ) ); ?> <strong>"<?php echo esc_html( $term ); ?>"</strong>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $count ) : ?>
					<?php if ( $products ) : ?>
						<div class="rr-search__grid">
							<?php foreach ( $products as $p ) :
								$price   = (float) wc_get_price_to_display( $p );
								$regular = (float) wc_get_price_to_display( $p, array( 'price' => $p->get_regular_price() ) );
								?>
								<a href="<?php echo esc_url( $p->get_permalink() ); ?>" class="rr-search__card">
									<div class="rr-search__card-img"><?php echo $p->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => $p->get_name() ) ); // phpcs:ignore ?></div>
									<div class="rr-search__card-info">
										<div class="rr-search__card-name"><?php echo esc_html( $p->get_name() ); ?></div>
										<div class="rr-search__card-price">
											<?php if ( $p->is_type( 'variable' ) ) : ?>
												<?php echo wp_kses_post( $p->get_price_html() ); ?>
											<?php else : ?>
												<?php echo wc_price( $price ); // phpcs:ignore ?>
												<?php if ( $regular > $price ) : ?><del><?php echo wc_price( $regular ); // phpcs:ignore ?></del><?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $page_results ) : ?>
						<div class="rr-search__pages">
							<div class="rr-search__pages-title"><?php esc_html_e( 'Pages', 'sunflorence' ); ?></div>
							<?php foreach ( $page_results as $pg ) : ?>
								<a href="<?php echo esc_url( get_permalink( $pg ) ); ?>" class="rr-search__page-item">
									<div class="rr-search__page-name"><?php echo esc_html( get_the_title( $pg ) ); ?></div>
									<?php $ex = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $pg->post_content ) ), 20 ); ?>
									<?php if ( $ex ) : ?><div class="rr-search__page-excerpt"><?php echo esc_html( $ex ); ?></div><?php endif; ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php the_posts_pagination( array( 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?>
				<?php else : ?>
					<div class="rr-search__empty">
						<div class="rr-search__empty-title"><?php esc_html_e( 'No results found', 'sunflorence' ); ?></div>
						<p class="rr-search__empty-sub"><?php esc_html_e( 'Try a different search term or browse our collections below.', 'sunflorence' ); ?></p>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $suggestions ) : ?>
				<div class="rr-search__suggestions">
					<div class="rr-search__suggestions-title"><?php esc_html_e( 'Popular Searches', 'sunflorence' ); ?></div>
					<div class="rr-search__suggestion-tags">
						<?php foreach ( $suggestions as $sg ) : ?>
							<?php if ( sf_row( $sg, 'term' ) ) : ?>
								<a href="<?php echo esc_url( add_query_arg( array( 's' => sf_row( $sg, 'term' ), 'post_type' => 'product' ), home_url( '/' ) ) ); ?>"><?php echo esc_html( sf_row( $sg, 'term' ) ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
sf_ctx( 'row' );
get_footer();
