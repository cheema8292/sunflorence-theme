<?php
/**
 * Fallback template (blog index, archives without a more specific template).
 *
 * @package Sunflorence
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="sf-container sf-page">
	<header class="sf-page__head">
		<h1 class="sf-page__title"><?php echo is_home() ? esc_html__( 'Journal', 'sunflorence' ) : wp_kses_post( get_the_archive_title() ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="sf-posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'sf-post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="sf-post-card__img"><?php the_post_thumbnail( 'woocommerce_thumbnail' ); ?></a>
					<?php endif; ?>
					<div class="sf-post-card__body">
						<p class="sf-post-card__date"><?php echo esc_html( get_the_date() ); ?></p>
						<h2 class="sf-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing here yet.', 'sunflorence' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
