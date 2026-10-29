<?php
/**
 * Left sidebar drawer with the main menu (up to 3 levels, accordion).
 * Menu: Appearance → Menus → location "Sidebar menu".
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$color_id = sf_opt( 'logo_color' );
$logo     = $color_id ? wp_get_attachment_image_url( $color_id, 'medium' ) : sf_asset( 'images/logo-sunflorence.png' );
?>
<div class="rr-nav__drawer" id="rr-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Navigation menu', 'sunflorence' ); ?>">
	<div class="rr-nav__drawer-overlay" data-drawer-close></div>
	<div class="rr-nav__drawer-panel">
		<div class="rr-nav__drawer-head">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rr-nav__drawer-logo">
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" height="52" style="height:52px;width:auto;display:block">
			</a>
			<button class="rr-nav__drawer-close" data-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'sunflorence' ); ?>">&#x2715;</button>
		</div>
		<?php
		if ( has_nav_menu( 'main-menu' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'main-menu',
				'container'      => false,
				'menu_class'     => 'rr-nav__drawer-links',
				'depth'          => 3,
				'walker'         => new SF_Drawer_Walker(),
				'fallback_cb'    => false,
			) );
		} else {
			// No menu assigned yet: list the top product categories.
			echo '<ul class="rr-nav__drawer-links">';
			echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'sunflorence' ) . '</a></li>';
			if ( taxonomy_exists( 'product_cat' ) ) {
				foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) ) as $t ) {
					echo '<li><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
				}
			}
			echo '</ul>';
		}
		?>
	</div>
</div>
