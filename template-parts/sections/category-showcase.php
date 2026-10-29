<?php
/**
 * Section: Category showcase — 5 designs (from Shopify rr-category-showcase).
 *
 *  spotlight : featured slider (one random product per subcategory) + tabs + 2×2 grid
 *  slider    : promo tile + scrolling row with arrows
 *  banner    : image banner with titles/tabs inside, product cards overlap its edge
 *  dark      : glass cards on a dark background
 *  lookbook  : tall arched image + product list
 *
 * Products come live from WooCommerce product categories.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$demo = ! empty( $args['demo'] );
$uid  = 'rr-cs-' . (int) ( $args['index'] ?? 0 );

/* ── settings (ACF row, or demo values) ── */
if ( $demo ) {
	$term   = $args['term'] ?? null;
	$layout = $args['layout'] ?? 'spotlight';
	$s      = array(
		'eyebrow' => $term ? $term->name : '', 'heading' => $term ? $term->name : '', 'subheading' => $term ? $term->description : '',
		'collection' => $term ? $term->term_id : 0, 'tabs' => array(), 'products_count' => 'spotlight' === $layout ? 4 : 8,
		'view_all_text' => __( 'View all', 'sunflorence' ), 'feature_label' => __( 'From', 'sunflorence' ), 'autoplay_speed' => 4,
		'image' => 0, 'image_title' => '', 'image_text' => '', 'image_button' => '', 'image_url' => '',
		'bg_color' => '#FAF6F2', 'text_color' => '#22344A', 'accent_color' => '#6E1F2F', 'card_color' => '#FFFFFF', 'padding_vertical' => 88,
	);
	if ( $term ) {
		foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $term->term_id, 'hide_empty' => true, 'number' => 6 ) ) as $child ) {
			$s['tabs'][] = array( 'collection' => $child->term_id, 'label' => '' );
		}
	}
} else {
	$layout = sf_s( 'layout', 'spotlight' );
	$s      = array();
	foreach ( array( 'eyebrow', 'heading', 'subheading', 'collection', 'products_count', 'view_all_text', 'view_all_url', 'feature_label', 'feature_pool', 'autoplay_speed', 'image', 'image_title', 'image_text', 'image_button', 'image_url', 'bg_color', 'text_color', 'accent_color', 'card_color', 'padding_vertical' ) as $k ) {
		$s[ $k ] = sf_s( $k, '' );
	}
	$s['tabs'] = sf_blocks( 'tab' );
}
$layout = in_array( $layout, array( 'spotlight', 'slider', 'banner', 'dark', 'lookbook' ), true ) ? $layout : 'spotlight';
$count  = max( 2, (int) ( $s['products_count'] ? $s['products_count'] : 8 ) );

/* tabs → list of [term, label] (skip empty / deleted categories) */
$tabs = array();
foreach ( (array) $s['tabs'] as $row ) {
	$t = sf_term( sf_row( $row, 'collection' ) );
	if ( $t ) {
		$tabs[] = array( 'term' => $t, 'label' => sf_row( $row, 'label', $t->name ) );
	}
}
$main     = sf_term( $s['collection'] );
$main_url = $main ? sf_cat_link( $main ) : ( $tabs ? sf_cat_link( $tabs[0]['term'] ) : sf_cat_link( null ) );
if ( ! empty( $s['view_all_url'] ) ) {
	$main_url = $s['view_all_url'];
}
$pool = max( 1, (int) ( empty( $s['feature_pool'] ) ? 8 : $s['feature_pool'] ) );

$style = sprintf(
	'--cs-bg:%s;--cs-text:%s;--cs-accent:%s;--cs-card:%s;--cs-pad:%dpx;',
	sf_color( $s['bg_color'], '#FAF6F2' ), sf_color( $s['text_color'], '#22344A' ),
	sf_color( $s['accent_color'], '#6E1F2F' ), sf_color( $s['card_color'], '#FFFFFF' ),
	(int) ( $s['padding_vertical'] ? $s['padding_vertical'] : 88 )
);

/* ── pieces (same as the Liquid captures) ── */
$titles = function () use ( $s ) {
	echo '<div class="rr-cs__titles">';
	if ( $s['eyebrow'] ) {
		echo '<div class="rr-cs__eyebrow">' . esc_html( $s['eyebrow'] ) . '</div>';
	}
	if ( $s['heading'] ) {
		echo '<h2 class="rr-cs__heading">' . sf_inline( $s['heading'] ) . '</h2>'; // phpcs:ignore
	}
	if ( $s['subheading'] ) {
		echo '<p class="rr-cs__sub">' . esc_html( $s['subheading'] ) . '</p>';
	}
	echo '</div>';
};

$tab_buttons = function () use ( $tabs, $uid, $main_url ) {
	if ( count( $tabs ) < 2 ) {
		return;
	}
	echo '<div class="rr-cs__tabs" role="tablist">';
	foreach ( $tabs as $n => $t ) {
		printf(
			'<button type="button" role="tab" class="rr-cs__tab%1$s" id="%2$s-tab-%3$d" aria-controls="%2$s-panel-%3$d" aria-selected="%4$s" data-view-all="%5$s">%6$s</button>',
			0 === $n ? ' is-active' : '', esc_attr( $uid ), (int) $n, 0 === $n ? 'true' : 'false',
			esc_url( sf_cat_link( $t['term'] ) ), esc_html( $t['label'] )
		);
	}
	echo '</div>';
};

$view_all = function () use ( $s, $tabs, $main_url ) {
	if ( ! $s['view_all_text'] ) {
		return;
	}
	$first = $tabs ? sf_cat_link( $tabs[0]['term'] ) : $main_url;
	echo '<a class="rr-cs__view-all" href="' . esc_url( $first ) . '" data-cs-view-all>' . esc_html( $s['view_all_text'] ) . ' ' . sf_icon( 'arrow' ) . '</a>'; // phpcs:ignore
};

$arrows = function () use ( $layout ) {
	if ( ! in_array( $layout, array( 'slider', 'dark' ), true ) ) {
		return;
	}
	echo '<div class="rr-cs__arrows">'
		. '<button type="button" class="rr-cs__arrow" data-cs-dir="-1" aria-label="' . esc_attr__( 'Previous products', 'sunflorence' ) . '">' . sf_icon( 'prev' ) . '</button>'
		. '<button type="button" class="rr-cs__arrow" data-cs-dir="1" aria-label="' . esc_attr__( 'Next products', 'sunflorence' ) . '">' . sf_icon( 'next' ) . '</button>'
		. '</div>'; // phpcs:ignore
};

$promo_tile = function () use ( $s, $layout, $main_url ) {
	if ( 'slider' !== $layout || ! $s['image_title'] ) {
		return;
	}
	echo '<a class="rr-cs__promo-tile" href="' . esc_url( $s['image_url'] ? $s['image_url'] : $main_url ) . '">';
	echo sf_img( $s['image'], 'large', array( 'loading' => 'lazy', 'alt' => $s['image_title'] ) ); // phpcs:ignore
	echo '<span class="rr-cs__promo-tile-body"><span class="rr-cs__promo-tile-title">' . esc_html( $s['image_title'] ) . '</span>';
	if ( $s['image_text'] ) {
		echo '<span class="rr-cs__promo-tile-text">' . esc_html( $s['image_text'] ) . '</span>';
	}
	if ( $s['image_button'] ) {
		echo '<span class="rr-cs__btn">' . esc_html( $s['image_button'] ) . '</span>';
	}
	echo '</span></a>';
};

$cards = function ( $term, $limit ) {
	$products = sf_products_in_cat( $term, $limit );
	if ( ! $term ) {
		for ( $i = 1; $i <= $limit; $i++ ) {
			get_template_part( 'template-parts/product/card', null, array( 'product' => null, 'index' => $i ) );
		}
		return;
	}
	if ( ! $products ) {
		echo '<p class="rr-cs__empty">' . esc_html( sprintf( __( 'No products in “%s” yet.', 'sunflorence' ), $term->name ) ) . '</p>';
		return;
	}
	foreach ( $products as $n => $p ) {
		get_template_part( 'template-parts/product/card', null, array( 'product' => $p, 'index' => $n + 1 ) );
	}
};

$panels = function () use ( $tabs, $main, $uid, $count, $cards, $promo_tile ) {
	if ( $tabs ) {
		foreach ( $tabs as $n => $t ) {
			printf( '<div class="rr-cs__panel%1$s" role="tabpanel" id="%2$s-panel-%3$d" aria-labelledby="%2$s-tab-%3$d" data-block-id="%3$d"><div class="rr-cs__items">', 0 === $n ? ' is-active' : '', esc_attr( $uid ), (int) $n );
			$promo_tile();
			$cards( $t['term'], $count );
			echo '</div></div>';
		}
	} else {
		echo '<div class="rr-cs__panel is-active"><div class="rr-cs__items">';
		$promo_tile();
		$cards( $main, $count );
		echo '</div></div>';
	}
};

$image_alt = wp_strip_all_tags( $s['image_title'] ? $s['image_title'] : $s['heading'] );
?>
<section id="<?php echo esc_attr( $uid ); ?>" class="rr-cs rr-cs--<?php echo esc_attr( $layout ); ?>" style="<?php echo esc_attr( $style ); ?>">
	<div class="rr-cs__inner">
	<?php switch ( $layout ) :

		case 'banner': ?>
			<div class="rr-cs__banner">
				<?php echo sf_img( $s['image'], 'full', array( 'class' => 'rr-cs__banner-img', 'loading' => 'lazy', 'alt' => $image_alt, 'sizes' => '100vw' ) ); // phpcs:ignore ?>
				<div class="rr-cs__banner-body">
					<?php $titles(); ?>
					<div class="rr-cs__tools"><?php $tab_buttons(); $view_all(); ?></div>
				</div>
			</div>
			<div class="rr-cs__body"><?php $panels(); ?></div>
			<?php break;

		case 'lookbook': ?>
			<div class="rr-cs__look">
				<a class="rr-cs__look-media" href="<?php echo esc_url( $s['image_url'] ? $s['image_url'] : $main_url ); ?>">
					<?php echo sf_img( $s['image'], 'large', array( 'loading' => 'lazy', 'alt' => $image_alt, 'sizes' => '(max-width: 900px) 100vw, 45vw' ) ); // phpcs:ignore ?>
					<span class="rr-cs__look-caption">
						<?php if ( $s['image_title'] ) : ?><span class="rr-cs__look-title"><?php echo esc_html( $s['image_title'] ); ?></span><?php endif; ?>
						<?php if ( $s['image_text'] ) : ?><span class="rr-cs__look-text"><?php echo esc_html( $s['image_text'] ); ?></span><?php endif; ?>
						<?php if ( $s['image_button'] ) : ?><span class="rr-cs__btn"><?php echo esc_html( $s['image_button'] ); ?></span><?php endif; ?>
					</span>
				</a>
				<div class="rr-cs__look-main">
					<div class="rr-cs__head"><?php $titles(); ?></div>
					<div class="rr-cs__tools"><?php $tab_buttons(); $view_all(); ?></div>
					<?php $panels(); ?>
				</div>
			</div>
			<?php break;

		case 'dark': ?>
			<span class="rr-cs__orb rr-cs__orb--1" aria-hidden="true"></span>
			<span class="rr-cs__orb rr-cs__orb--2" aria-hidden="true"></span>
			<div class="rr-cs__head"><?php $titles(); ?><div class="rr-cs__head-side"><?php $view_all(); $arrows(); ?></div></div>
			<div class="rr-cs__tools"><?php $tab_buttons(); ?></div>
			<?php $panels(); ?>
			<?php break;

		case 'spotlight':
			// Featured slider: one slide per subcategory; each slide holds up to 8 candidates,
			// the browser shows a random one on every visit (category-showcase.js).
			$slides = array();
			if ( $tabs ) {
				foreach ( $tabs as $n => $t ) {
					$cands = sf_products_in_cat( $t['term'], $pool );
					if ( $cands ) {
						$slides[] = array( 'n' => $n, 'term' => $t['term'], 'label' => $t['label'], 'cands' => $cands );
					}
				}
			} elseif ( $main ) {
				foreach ( sf_products_in_cat( $main, $pool ) as $p ) {
					$slides[] = array( 'n' => 0, 'term' => $main, 'label' => $main->name, 'cands' => array( $p ) );
				}
			}
			?>
			<div class="rr-cs__head"><?php $titles(); ?><div class="rr-cs__head-side"><?php $view_all(); ?></div></div>
			<div class="rr-cs__split">
				<div class="rr-cs__feature" data-autoplay="<?php echo esc_attr( max( 2, (int) ( $s['autoplay_speed'] ? $s['autoplay_speed'] : 4 ) ) ); ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured products', 'sunflorence' ); ?>">
					<div class="rr-cs__feature-track">
						<?php if ( $slides ) : foreach ( $slides as $k => $sl ) : ?>
							<div class="rr-cs__fslide<?php echo 0 === $k ? ' is-active' : ''; ?>" data-block-id="<?php echo esc_attr( $sl['n'] ); ?>" role="group" aria-roledescription="slide">
								<a class="rr-cs__fslide-label" href="<?php echo esc_url( sf_cat_link( $sl['term'] ) ); ?>"><span><?php echo esc_html( $s['feature_label'] ? $s['feature_label'] : __( 'From', 'sunflorence' ) ); ?></span> <?php echo esc_html( $sl['label'] ); ?></a>
								<?php foreach ( $sl['cands'] as $c => $p ) : ?>
									<div class="rr-cs__fcand"<?php echo $c ? ' hidden' : ''; ?>><?php get_template_part( 'template-parts/product/card', null, array( 'product' => $p, 'index' => $c + 1 ) ); ?></div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; else : ?>
							<div class="rr-cs__fslide is-active"><div class="rr-cs__fcand"><?php get_template_part( 'template-parts/product/card', null, array( 'product' => null, 'index' => 1 ) ); ?></div></div>
						<?php endif; ?>
					</div>
					<?php if ( count( $slides ) > 1 ) : ?>
						<div class="rr-cs__feature-nav">
							<button type="button" class="rr-cs__feature-btn" data-feature-dir="-1" aria-label="<?php esc_attr_e( 'Previous featured product', 'sunflorence' ); ?>"><?php echo sf_icon( 'prev' ); // phpcs:ignore ?></button>
							<div class="rr-cs__dots">
								<?php foreach ( $slides as $k => $sl ) : ?>
									<button type="button" class="rr-cs__dot<?php echo 0 === $k ? ' is-active' : ''; ?>" data-feature-go="<?php echo esc_attr( $k ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show slide %d', 'sunflorence' ), $k + 1 ) ); ?>"></button>
								<?php endforeach; ?>
							</div>
							<button type="button" class="rr-cs__feature-btn" data-feature-dir="1" aria-label="<?php esc_attr_e( 'Next featured product', 'sunflorence' ); ?>"><?php echo sf_icon( 'next' ); // phpcs:ignore ?></button>
						</div>
						<div class="rr-cs__feature-progress"><span></span></div>
					<?php endif; ?>
				</div>
				<div class="rr-cs__split-main">
					<div class="rr-cs__tools"><?php $tab_buttons(); ?></div>
					<?php $panels(); ?>
				</div>
			</div>
			<?php break;

		default: /* slider */ ?>
			<div class="rr-cs__head"><?php $titles(); ?><div class="rr-cs__head-side"><?php $view_all(); $arrows(); ?></div></div>
			<div class="rr-cs__tools"><?php $tab_buttons(); ?></div>
			<?php $panels(); ?>
			<?php break;

	endswitch; ?>
	</div>
</section>
