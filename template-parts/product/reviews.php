<?php
/**
 * Customer reviews — the Shopify customer-reviews-carousel design, filled with the product's real
 * WooCommerce reviews (approved only). Below: the WooCommerce "write a review" form.
 * No reviews yet → only the heading + the form, so nothing is invented.
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'];
$enabled = 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' ) && comments_open( $product->get_id() );
$reviews = get_comments( array(
	'post_id' => $product->get_id(),
	'status'  => 'approve',
	'type'    => 'review',
	'parent'  => 0,
	'number'  => 20,
) );
if ( ! $reviews && ! $enabled ) {
	return;
}
$count  = (int) $product->get_review_count();
$avg    = (float) $product->get_average_rating();
$images = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
$star   = '<path d="M11.5245 3.46353C11.6741 3.00287 12.3259 3.00287 12.4755 3.46353L14.1329 8.56434C14.1998 8.77035 14.3918 8.90983 14.6084 8.90983H19.9717C20.4561 8.90983 20.6575 9.52964 20.2656 9.81434L15.9266 12.9668C15.7514 13.0941 15.678 13.3198 15.745 13.5258L17.4023 18.6266C17.552 19.0873 17.0248 19.4704 16.6329 19.1857L12.2939 16.0332C12.1186 15.9059 11.8814 15.9059 11.7061 16.0332L7.3671 19.1857C6.97524 19.4704 6.448 19.0873 6.59768 18.6266L8.25503 13.5258C8.32197 13.3198 8.24864 13.0941 8.07339 12.9668L3.73438 9.81434C3.34253 9.52964 3.54392 8.90983 4.02828 8.90983H9.39159C9.6082 8.90983 9.80018 8.77035 9.86712 8.56434L11.5245 3.46353Z" fill="currentColor"/>';
?>
<div class="reviews-transformation-section" id="sf-reviews">
	<?php if ( $count ) : ?>
		<div class="rating-summary-wrapper">
			<div class="reviews-rating-summary" style="background-color: #f5f5f5; border-radius: 50px;">
				<span class="rating-stars"><div class="stars">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<svg class="star reviews-summary-star<?php echo $i > round( $avg ) ? ' star-empty' : ''; ?>" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><?php echo $star; // phpcs:ignore ?></svg>
					<?php endfor; ?>
				</div></span>
				<div class="rating-text">
					<?php
					/* translators: 1: average rating, 2: number of reviews */
					echo esc_html( sprintf( _n( 'Rated %1$s/5 based on %2$s review', 'Rated %1$s/5 based on %2$s reviews', $count, 'sunflorence' ), number_format_i18n( $avg, 1 ), number_format_i18n( $count ) ) );
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="reviews-section-header">
		<h2 class="reviews-subtitle section-title heading h1">
			<?php echo esc_html( sf_page_opt( 'product', 'reviews_title' ) ); ?>
			<span class="global-accent-text"><?php echo esc_html( sf_page_opt( 'product', 'reviews_accent' ) ); ?></span>
		</h2>
		<?php if ( ! $reviews ) : ?>
			<p class="reviews-description"><?php esc_html_e( 'No reviews yet — be the first to share your experience.', 'sunflorence' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $reviews ) : ?>
		<div class="reviews-container">
			<?php foreach ( $reviews as $n => $c ) :
				$rating   = (int) get_comment_meta( $c->comment_ID, 'rating', true );
				$verified = wc_review_is_from_verified_owner( $c->comment_ID );
				$img      = $images ? $images[ $n % count( $images ) ] : 0;
				?>
				<div class="review-card">
					<div class="review-image-container image-wrapped">
						<?php if ( $img ) : ?>
							<div class="carousel-review-image">
								<?php echo wp_get_attachment_image( $img, 'woocommerce_single', false, array( 'loading' => 'lazy', 'alt' => '', 'style' => 'object-fit: contain;' ) ); // phpcs:ignore ?>
							</div>
						<?php else : ?>
							<div class="carousel-review-image empty-image"></div>
						<?php endif; ?>
					</div>
					<div class="review-content">
						<div class="carousel-review-header">
							<?php if ( $rating ) : ?>
								<div class="review-stars"><div class="stars" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'sunflorence' ), $rating ) ); ?>">
									<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
										<svg class="star carousel-review-star <?php echo $i <= $rating ? 'star-filled' : 'star-empty'; ?>" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><?php echo $star; // phpcs:ignore ?></svg>
									<?php endfor; ?>
								</div></div>
							<?php endif; ?>
							<div class="reviewer-info-container">
								<div class="reviewer-info">
									<span class="carousel-reviewer-name"><?php echo esc_html( get_comment_author( $c ) ); ?></span>
									<?php if ( $verified ) : ?>
										<span class="verified-badge">
											<svg class="verified-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.5924 3.20027C9.34888 3.4078 9.22711 3.51158 9.09706 3.59874C8.79896 3.79854 8.46417 3.93721 8.1121 4.00672C7.95851 4.03705 7.79903 4.04977 7.48008 4.07522C6.6787 4.13918 6.278 4.17115 5.94371 4.28923C5.17051 4.56233 4.56233 5.17051 4.28923 5.94371C4.17115 6.278 4.13918 6.6787 4.07522 7.48008C4.04977 7.79903 4.03705 7.95851 4.00672 8.1121C3.93721 8.46417 3.79854 8.79896 3.59874 9.09706C3.51158 9.22711 3.40781 9.34887 3.20027 9.5924C2.67883 10.2043 2.4181 10.5102 2.26522 10.8301C1.91159 11.57 1.91159 12.43 2.26522 13.1699C2.41811 13.4898 2.67883 13.7957 3.20027 14.4076C3.40778 14.6511 3.51158 14.7729 3.59874 14.9029C3.79854 15.201 3.93721 15.5358 4.00672 15.8879C4.03705 16.0415 4.04977 16.201 4.07522 16.5199C4.13918 17.3213 4.17115 17.722 4.28923 18.0563C4.56233 18.8295 5.17051 19.4377 5.94371 19.7108C6.278 19.8288 6.6787 19.8608 7.48008 19.9248C7.79903 19.9502 7.95851 19.963 8.1121 19.9933C8.46417 20.0628 8.79896 20.2015 9.09706 20.4013C9.22711 20.4884 9.34887 20.5922 9.5924 20.7997C10.2043 21.3212 10.5102 21.5819 10.8301 21.7348C11.57 22.0884 12.43 22.0884 13.1699 21.7348C13.4898 21.5819 13.7957 21.3212 14.4076 20.7997C14.6511 20.5922 14.7729 20.4884 14.9029 20.4013C15.201 20.2015 15.5358 20.0628 15.8879 19.9933C16.0415 19.963 16.201 19.9502 16.5199 19.9248C17.3213 19.8608 17.722 19.8288 18.0563 19.7108C18.8295 19.4377 19.4377 18.8295 19.7108 18.0563C19.8288 17.722 19.8608 17.3213 19.9248 16.5199C19.9502 16.201 19.963 16.0415 19.9933 15.8879C20.0628 15.5358 20.2015 15.201 20.4013 14.9029C20.4884 14.7729 20.5922 14.6511 20.7997 14.4076C21.3212 13.7957 21.5819 13.4898 21.7348 13.1699C22.0884 12.43 22.0884 11.57 21.7348 10.8301C21.5819 10.5102 21.3212 10.2043 20.7997 9.5924C20.5922 9.34887 20.4884 9.22711 20.4013 9.09706C20.2015 8.79896 20.0628 8.46417 19.9933 8.1121C19.963 7.95851 19.9502 7.79903 19.9248 7.48008C19.8608 6.6787 19.8288 6.278 19.7108 5.94371C19.4377 5.17051 18.8295 4.56233 18.0563 4.28923C17.722 4.17115 17.3213 4.13918 16.5199 4.07522C16.201 4.04977 16.0415 4.03705 15.8879 4.00672C15.5358 3.93721 15.201 3.79854 14.9029 3.59874C14.7729 3.51158 14.6511 3.40781 14.4076 3.20027C13.7957 2.67883 13.4898 2.41811 13.1699 2.26522C12.43 1.91159 11.57 1.91159 10.8301 2.26522C10.5102 2.4181 10.2043 2.67883 9.5924 3.20027ZM16.3735 9.86314C16.6913 9.5453 16.6913 9.03 16.3735 8.71216C16.0557 8.39433 15.5403 8.39433 15.2225 8.71216L10.3723 13.5624L8.77746 11.9676C8.45963 11.6498 7.94432 11.6498 7.62649 11.9676C7.30866 12.2854 7.30866 12.8007 7.62649 13.1186L9.79678 15.2889C10.1146 15.6067 10.6299 15.6067 10.9478 15.2889L16.3735 9.86314Z" fill="currentColor"/></svg>
											<?php esc_html_e( 'Verified Buyer', 'sunflorence' ); ?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="review-text"><?php echo wp_kses_post( wpautop( get_comment_text( $c ) ) ); ?></div>
						<p class="product-purchase-date"><?php echo esc_html( get_comment_date( '', $c ) ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $reviews ) > 1 ) : ?>
			<div class="reviews-navigation-container">
				<div class="reviews-navigation">
					<button type="button" class="nav-button prev" aria-label="<?php esc_attr_e( 'Previous reviews', 'sunflorence' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
					<button type="button" class="nav-button next" aria-label="<?php esc_attr_e( 'Next reviews', 'sunflorence' ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $enabled ) : ?>
		<details class="sf-review-form"<?php echo $reviews ? '' : ' open'; ?>>
			<summary class="sf-review-form__toggle"><?php esc_html_e( 'Write a review', 'sunflorence' ); ?></summary>
			<div class="sf-review-form__body" id="reviews">
				<?php
				$can = 'no' === get_option( 'woocommerce_review_rating_verification_required' ) || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() );
				if ( ! $can ) :
					?>
					<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
				<?php else :
					$commenter    = wp_get_current_commenter();
					$name_email   = get_option( 'require_name_email' );
					$fields       = array(
						'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'woocommerce' ) . ( $name_email ? ' <span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" autocomplete="name" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . ( $name_email ? ' required' : '' ) . '></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'woocommerce' ) . ( $name_email ? ' <span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" autocomplete="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . ( $name_email ? ' required' : '' ) . '></p>',
					);
					$rating_field = '';
					if ( wc_review_ratings_enabled() ) {
						$rating_field = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? ' <span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating"' . ( wc_review_ratings_required() ? ' required' : '' ) . '>
							<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
						</select></div>';
					}
					comment_form( array(
						'title_reply'          => '',
						'title_reply_before'   => '',
						'title_reply_after'    => '',
						'comment_notes_after'  => '',
						'fields'               => $fields,
						'label_submit'         => __( 'Submit review', 'sunflorence' ),
						'class_submit'         => 'sf-btn',
						'logged_in_as'         => '',
						'comment_field'        => $rating_field . '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>',
					), $product->get_id() );
				endif;
				?>
			</div>
		</details>
	<?php endif; ?>
</div>
