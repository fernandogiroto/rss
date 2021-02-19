<?php

/**
 * Display the RSS entries in a list.
 *
 * @since 2.5.0
 *
 * @param string|array|object $rss  RSS url.
 * @param array               $args Widget arguments.
 */
function wp_widget_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		$rss = fetch_feed( $rss );
	} elseif ( is_array( $rss ) && isset( $rss['url'] ) ) {
		$args = $rss;
		$rss  = fetch_feed( $rss['url'] );
	} elseif ( ! is_object( $rss ) ) {
		return;
	}

	if ( is_wp_error( $rss ) ) {
		if ( is_admin() || current_user_can( 'manage_options' ) ) {
			echo '<p><strong>' . __( 'RSS Error:' ) . '</strong> ' . $rss->get_error_message() . '</p>';
		}
		return;
	}

	$default_args = array(
		'show_author'  => 0,
		'show_date'    => 0,
		'show_summary' => 0,
		'show_image'   => 0,
		'items'        => 0,
	);
	$args         = wp_parse_args( $args, $default_args );

	$items = (int) $args['items'];
	if ( $items < 1 || 20 < $items ) {
		$items = 10;
	}
	$show_summary = (int) $args['show_summary'];
	$show_author  = (int) $args['show_author'];
	$show_date    = (int) $args['show_date'];
	$show_image   = (int) $args['show_image'];

	if ( ! $rss->get_item_quantity() ) {
		echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</li></ul>';
		$rss->__destruct();
		unset( $rss );
		return;
	}

	echo '<ul>';
	foreach ( $rss->get_items( 0, $items ) as $item ) {

        //ESTOU TENTANDO BUSCAR AQUI
		print_r(get_the_post_thumbnail($item));
		$link = $item->get_link();
		while ( stristr( $link, 'http' ) !== $link ) {
			$link = substr( $link, 1 );
		}
		$link = esc_url( strip_tags( $link ) );

		$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
		if ( empty( $title ) ) {
			$title = __( 'Untitled' );
		}
		$desc = html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
		$desc = esc_attr( wp_trim_words( $desc, 55, ' [&hellip;]' ) );

		
		$summary = '';
		if ( $show_summary ) {
			$summary = $desc;

			// Change existing [...] to [&hellip;].
			if ( '[...]' === substr( $summary, -5 ) ) {
				$summary = substr( $summary, 0, -5 ) . '[&hellip;]';
			}

			$summary = '<div class="rssSummary">' . esc_html( $summary ) . '</div>';
		}

		$date = '';
		if ( $show_date ) {
			$date = $item->get_date( 'U' );

			if ( $date ) {
				$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
			}
		}

		$author = '';
		if ( $show_author ) {
			$author = $item->get_author();
			if ( is_object( $author ) ) {
				$author = $author->get_name();
				$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
			}
		}

		if ( '' === $link ) {
			echo "<li>title${$date}{$summary}{$author}</li>";
		} elseif ( $show_summary ) {
			echo "<li><a class='rsswidget aaaa' href='$link'>$title</a>{$date}{$summary}{$author}</li>";
		} else {
			echo "<li><a class='rsswidget bbbb' href='$link'>$title</a>{$date}{$author}</li>";
		}

		echo "
		<div class='lf-item-container'>
        <div class='lf-item lf-item-default'>
            <a href='${link}'>
                <div class='overlay' style='background-color: #242429; opacity: 0.4'></div>
                <div class='lf-background' style=\"background-image: url('https://abcoeiras.com/wp-content/uploads/2021/02/0d650ff08d9e99-768x512.jpg\"');'></div>
                <div class='lf-item-info'>
                    <h4 class='case27-secondary-text listing-preview-title'>
                    ${title}
                    </h4>
                    <ul class='lf-contact'>
                    <li>
                        <i class='icon-location-pin-add-2 sm-icon'></i>
                        103 Gaunt Street
                    </li>
                    <li>
                        <i class='icon-calendar-1 sm-icon'></i>
                        ${date}
                    </li>
                    </ul>
                </div>
                <div class='lf-head'></div>
            </a>
        </div>
        <div class='event-host c27-footer-section'>
            <a href='https://abcoeiras.com/registos/ministry-of-sound/'>
                <div class='avatar'>
                    <img
                    src='https://abcoeiras.com/wp-content/uploads/2021/02/1e88dce362f21e-150x150.jpg'
                    alt='Ministry of Sound'
                    />
                </div>
                <span class='host-name'>Ministry of Sound</span>
            </a>
            <div class='ld-info'>
                <ul>
                    <li class='item-preview' data-placement='top' data-original-title='Quick view'>
                        <a href='#' type='button'>
                            <i class='mi zoom_in'></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>";
	}
	echo '</ul>';
	$rss->__destruct();
	unset( $rss );
}
