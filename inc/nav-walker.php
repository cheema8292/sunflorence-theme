<?php
/**
 * Sidebar menu walker — same markup as the Shopify sidebar (rr-dl accordion, up to 3 levels).
 *
 * Parent items get an arrow button; their sub-menu starts with a "View all …" link so the
 * parent page stays reachable (the parent name itself only opens/closes the sub-menu).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

class SF_Drawer_Walker extends Walker_Nav_Menu {

	/** Parent item stack, used for the "View all" link of each sub-menu. */
	private $parents = array();

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$parent = end( $this->parents );
		$output .= '<ul class="rr-dl__sub">';
		if ( $parent && ! empty( $parent->url ) && '#' !== $parent->url ) {
			$output .= '<li class="rr-dl__all"><a href="' . esc_url( $parent->url ) . '">'
				. esc_html( sprintf( __( 'View all %s', 'sunflorence' ), $parent->title ) ) . '</a></li>';
		}
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true ) && $depth < 2;
		$current      = in_array( 'current-menu-item', (array) $item->classes, true );
		$ancestor     = in_array( 'current-menu-ancestor', (array) $item->classes, true ) || in_array( 'current-menu-parent', (array) $item->classes, true );
		$active_attr  = $current ? ' class="is-active" aria-current="page"' : '';

		if ( $has_children ) {
			$this->parents[] = $item;
			$output .= '<li class="rr-dl__item' . ( $ancestor ? ' is-open' : '' ) . '">';
			$output .= '<div class="rr-dl__row">';
			$output .= '<a href="' . esc_url( $item->url ) . '" data-rr-parent' . $active_attr . '>' . esc_html( $item->title ) . '</a>';
			$output .= '<button type="button" class="rr-dl__toggle" aria-expanded="' . ( $ancestor ? 'true' : 'false' ) . '" aria-label="'
				. esc_attr( sprintf( __( 'Show %s submenu', 'sunflorence' ), $item->title ) ) . '">' . sf_icon( 'chevron' ) . '</button>';
			$output .= '</div>';
		} else {
			$output .= '<li><a href="' . esc_url( $item->url ) . '"' . $active_attr . '>' . esc_html( $item->title ) . '</a>';
		}
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) && $depth < 2 ) {
			array_pop( $this->parents );
		}
		$output .= '</li>';
	}
}
