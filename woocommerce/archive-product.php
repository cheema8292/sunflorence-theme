<?php
/**
 * Shop page + product category / tag pages (Shopify sections/rr-collection.liquid):
 * hero · toolbar (filters button, count, sort) · sidebar "Browse Collections" · product grid · pagination.
 * Settings: Sunflorence → Pages → Category pages.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term     = is_product_taxonomy() ? get_queried_object() : null;
$title    = $term ? $term->name : woocommerce_page_title( false );
$desc     = $term ? term_description( $term ) : '';
$total    = (int) $GLOBALS['wp_query']->found_posts;
$shop_url = wc_get_page_permalink( 'shop' );
$is_cat   = $term && 'product_cat' === $term->taxonomy;

// Sort options (labels from Shopify → WooCommerce orderby values)
$sorts   = array(
	'menu_order' => __( 'Featured', 'sunflorence' ),
	'popularity' => __( 'Best Selling', 'sunflorence' ),
	'price'      => __( 'Price: Low → High', 'sunflorence' ),
	'price-desc' => __( 'Price: High → Low', 'sunflorence' ),
	'date'       => __( 'Newest First', 'sunflorence' ),
	'title'      => __( 'A – Z', 'sunflorence' ),
);
$current = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) ); // phpcs:ignore WordPress.Security.NonceVerification
if ( ! isset( $sorts[ $current ] ) ) {
	$current = 'menu_order';
}

// Collections list (product categories with products)
$cats = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	'orderby'    => 'name',
) );
$all_count = (int) ( wp_count_posts( 'product' )->publish ?? 0 );
ob_start();
?>
<div class="rr-col__filter-section is-open">
	<div class="rr-col__filter-heading">
		<?php esc_html_e( 'Browse Collections', 'sunflorence' ); ?>
		<span class="rr-col__filter-heading-icon"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
	</div>
	<div class="rr-col__filter-body">
		<div class="rr-col__filter-list">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="rr-col__filter-item<?php echo is_shop() ? ' is-active' : ''; ?>" style="text-decoration:none">
				<span class="rr-col__filter-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
				<span class="rr-col__filter-item-label"><?php esc_html_e( 'All Products', 'sunflorence' ); ?></span>
				<span class="rr-col__filter-item-count"><?php echo esc_html( $all_count ); ?></span>
			</a>
			<?php if ( ! is_wp_error( $cats ) ) : foreach ( $cats as $c ) : ?>
				<a href="<?php echo esc_url( get_term_link( $c ) ); ?>" class="rr-col__filter-item<?php echo ( $term && (int) $term->term_id === (int) $c->term_id ) ? ' is-active' : ''; ?>" style="text-decoration:none">
					<span class="rr-col__filter-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
					<span class="rr-col__filter-item-label"><?php echo esc_html( $c->name ); ?></span>
					<span class="rr-col__filter-item-count"><?php echo esc_html( $c->count ); ?></span>
				</a>
			<?php endforeach; endif; ?>
		</div>
	</div>
</div>
<?php
$filters = ob_get_clean();
?>
<div class="sf-notices"><?php woocommerce_output_all_notices(); ?></div>

<div class="rr-col">
	<div class="rr-col__hero">
		<div class="rr-col__eyebrow"><?php echo esc_html( sf_page_opt( 'collection', 'eyebrow' ) ); ?></div>
		<h1 class="rr-col__hero-title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $desc ) : ?>
			<p class="rr-col__hero-desc"><?php echo wp_kses_post( wp_strip_all_tags( $desc ) ); ?></p>
		<?php endif; ?>
		<div class="rr-col__hero-count"><?php echo esc_html( sprintf( _n( '%s Product', '%s Products', $total, 'sunflorence' ), number_format_i18n( $total ) ) ); ?></div>
	</div>

	<div class="rr-col__toolbar">
		<div class="rr-col__toolbar-inner">
			<div class="rr-col__toolbar-left">
				<button type="button" class="rr-col__filter-toggle" id="rr-filter-toggle" aria-label="<?php esc_attr_e( 'Filters', 'sunflorence' ); ?>" aria-controls="rr-col-drawer">
					<svg viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="12" y1="18" x2="12" y2="18" stroke-linecap="round"/></svg>
					<?php esc_html_e( 'Filters', 'sunflorence' ); ?>
				</button>
				<span class="rr-col__count"><span id="rr-visible-count"><?php echo esc_html( $total ); ?></span> <?php echo esc_html( _n( 'product', 'products', $total, 'sunflorence' ) ); ?></span>
			</div>
			<div class="rr-col__sort">
				<span class="rr-col__sort-label"><?php esc_html_e( 'Sort by', 'sunflorence' ); ?></span>
				<div style="position:relative">
					<button type="button" class="rr-col__sort-btn" id="rr-sort-btn" aria-haspopup="listbox" aria-expanded="false">
						<span class="rr-col__sort-btn-text"><?php echo esc_html( $sorts[ $current ] ); ?></span>
						<span class="rr-col__sort-btn-icon"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
					</button>
					<div class="rr-col__sort-dropdown" id="rr-sort-dropdown" role="listbox">
						<?php foreach ( $sorts as $val => $label ) : ?>
							<a href="<?php echo esc_url( add_query_arg( 'orderby', $val, remove_query_arg( 'paged' ) ) ); ?>" class="rr-col__sort-option<?php echo $current === $val ? ' is-active' : ''; ?>" role="option" aria-selected="<?php echo $current === $val ? 'true' : 'false'; ?>">
								<?php echo esc_html( $label ); ?>
								<span class="rr-col__sort-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="rr-col__body">
		<div class="rr-col__layout">
			<aside class="rr-col__sidebar" id="rr-sidebar"><?php echo $filters; // phpcs:ignore ?></aside>

			<div class="rr-col__main">
				<?php if ( have_posts() ) : ?>
					<div class="rr-col__grid" id="rr-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							$p = wc_get_product( get_the_ID() );
							if ( ! $p || ! $p->is_visible() ) {
								continue;
							}
							?>
							<div class="rr-col__card-wrap">
								<?php get_template_part( 'template-parts/product/card-simple', null, array( 'product' => $p ) ); ?>
							</div>
						<?php endwhile; ?>
					</div>
					<?php
					$links = paginate_links( array(
						'total'     => (int) $GLOBALS['wp_query']->max_num_pages,
						'current'   => max( 1, (int) get_query_var( 'paged' ) ),
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'mid_size'  => 1,
						'type'      => 'array',
					) );
					if ( $links ) :
						?>
						<nav class="rr-col__pagination" aria-label="<?php esc_attr_e( 'Pagination', 'sunflorence' ); ?>">
							<?php
							foreach ( $links as $l ) {
								// Shopify markup: <a> links and <span class="current">
								echo str_replace( 'page-numbers current', 'current', $l ); // phpcs:ignore
							}
							?>
						</nav>
					<?php endif; ?>
				<?php else : ?>
					<div style="text-align:center;padding:80px 20px">
						<div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:300;margin-bottom:12px;"><?php esc_html_e( 'No products yet', 'sunflorence' ); ?></div>
						<a href="<?php echo esc_url( $shop_url ); ?>" style="display:inline-block;background:#22344a;color:#fff;padding:14px 36px;border-radius:10px;font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;text-decoration:none;"><?php esc_html_e( 'Shop All', 'sunflorence' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="rr-col__drawer-overlay" id="rr-col-drawer-overlay"></div>
	<div class="rr-col__drawer" id="rr-col-drawer" aria-label="<?php esc_attr_e( 'Filters', 'sunflorence' ); ?>">
		<div class="rr-col__drawer-handle"></div>
		<div class="rr-col__drawer-head">
			<span class="rr-col__drawer-title"><?php esc_html_e( 'Filters', 'sunflorence' ); ?></span>
			<button type="button" class="rr-col__drawer-close" id="rr-col-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'sunflorence' ); ?>">&#x2715;</button>
		</div>
		<?php echo $filters; // phpcs:ignore ?>
	</div>
</div>
<?php
get_footer();
