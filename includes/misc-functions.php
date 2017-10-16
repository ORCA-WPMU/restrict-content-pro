<?php
/**
 * Misc. Functions
 *
 * @package     Restrict Content Pro
 * @subpackage  Misc Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Determines if we are in sandbox mode
 *
 * @access public
 * @since 2.6.4
 * @return bool True if we are in sandbox mode
 */
function rcp_is_sandbox(){
    global $rcp_options;
    return (bool) apply_filters( 'rcp_is_sandbox', isset( $rcp_options['sandbox'] ) );
}

/**
 * Checks whether the post is Paid Only.
 *
 * @param int $post_id ID of the post to check.
 *
 * @access private
 * @return bool True if the post is paid only, false if not.
 */
function rcp_is_paid_content( $post_id ) {
	if ( $post_id == '' || ! is_int( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$return = false;
	$post_type_restrictions = rcp_get_post_type_restrictions( get_post_type( $post_id) );

	if ( ! empty( $post_type_restrictions ) ) {

		// Check post type restrictions.
		if ( array_key_exists( 'is_paid', $post_type_restrictions ) ) {
			$return = true;
		}

	} else {

		// Check regular post.
		$is_paid = get_post_meta( $post_id, '_is_paid', true );
		if ( $is_paid ) {
			// this post is for paid users only
			$return = true;
		}

	}

	return (bool) apply_filters( 'rcp_is_paid_content', $return, $post_id );
}


/**
 * Retrieve a list of all Paid Only posts.
 *
 * @access public
 * @return array Lists all paid only posts.
 */
function rcp_get_paid_posts() {
	$args = array(
		'meta_key'       => '_is_paid',
		'meta_value'     => 1,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'post_type'      => 'any',
		'fields'         => 'ids'
	);
	$paid_ids = get_posts( $args );
	if ( $paid_ids ) {
		return $paid_ids;
	}

	return array();
}


/**
 * Apply the currency sign to a price.
 *
 * @param float $price Price to add the currency sign to.
 *
 * @access public
 * @return string List of currency signs.
 */
function rcp_currency_filter( $price ) {
	global $rcp_options;

	$currency = rcp_get_currency();
	$position = isset( $rcp_options['currency_position'] ) ? $rcp_options['currency_position'] : 'before';
	if ( $position == 'before' ) :
		$formatted = rcp_get_currency_symbol( $currency ) . $price;
		return apply_filters( 'rcp_' . strtolower( $currency ) . '_currency_filter_before', $formatted, $currency, $price );
	else :
		$formatted = $price . rcp_get_currency_symbol( $currency );
		return apply_filters( 'rcp_' . strtolower( $currency ) . '_currency_filter_after', $formatted, $currency, $price );
	endif;
}

/**
 * Return the symbol for a specific currency
 *
 * @param bool $currency
 *
 * @since 2.9.5
 * @return string
 */
function rcp_get_currency_symbol( $currency = false ) {

	if ( empty( $currency ) ) {
		$currency = rcp_get_currency();
	}

	$supported_currencies = rcp_get_currencies();
	if ( ! array_key_exists( $currency, $supported_currencies ) ) {
		$currency = rcp_get_currency();
	}

	switch( $currency ) {
		case "USD" : $symbol = '&#36;'; break;
		case "EUR" : $symbol = '&#8364;'; break;
		case "GBP" : $symbol = '&#163;'; break;
		case "AUD" : $symbol = '&#36;'; break;
		case "BRL" : $symbol = '&#82;&#36;'; break;
		case "CAD" : $symbol = '&#36;'; break;
		case "CHF" : $symbol = '&#67;&#72;&#70;'; break;
		case "CZK" : $symbol = '&#75;&#269;'; break;
		case "DKK" : $symbol = '&#107;&#114;'; break;
		case "HKD" : $symbol = '&#36;'; break;
		case "HUF" : $symbol = '&#70;&#116;'; break;
		case "ILS" : $symbol = '&#8362;'; break;
		case "IRR" : $symbol = '&#65020;'; break;
		case "JPY" : $symbol = '&#165;'; break;
		case "MXN" : $symbol = '&#36;'; break;
		case "MYR" : $symbol = '&#82;&#77;'; break;
		case "NOK" : $symbol = '&#107;&#114;'; break;
		case "NZD" : $symbol = '&#36;'; break;
		case "PHP" : $symbol = '&#8369;'; break;
		case "PLN" : $symbol = '&#122;&#322;'; break;
		case "RUB" : $symbol = '&#1088;&#1091;&#1073;'; break;
		case "SEK" : $symbol = '&#107;&#114;'; break;
		case "SGD" : $symbol = '&#36;'; break;
		case "THB" : $symbol = '&#3647;'; break;
		case "TRY" : $symbol = '&#8356;'; break;
		case "TWD" : $symbol = '&#78;&#84;&#36;'; break;
		default: $symbol = '';
	}

	return apply_filters( 'rcp_' . strtolower( $currency ) . '_symbol', $symbol, $currency );
	
}


/**
 * Get the currency list.
 *
 * @access private
 * @return array List of currencies.
 */
function rcp_get_currencies() {
	$currencies = array(
		'USD' => __( 'US Dollars (&#36;)', 'rcp' ),
		'EUR' => __( 'Euros (&#8364;)', 'rcp' ),
		'GBP' => __( 'Pounds Sterling (&#163;)', 'rcp' ),
		'AUD' => __( 'Australian Dollars (&#36;)', 'rcp' ),
		'BRL' => __( 'Brazilian Real (&#82;&#36;)', 'rcp' ),
		'CAD' => __( 'Canadian Dollars (&#36;)', 'rcp' ),
		'CZK' => __( 'Czech Koruna (&#75;&#269;)', 'rcp' ),
		'DKK' => __( 'Danish Krone (&#107;&#114;)', 'rcp' ),
		'HKD' => __( 'Hong Kong Dollar (&#36;)', 'rcp' ),
		'HUF' => __( 'Hungarian Forint (&#70;&#116;)', 'rcp' ),
		'IRR' => __( 'Iranian Rial (&#65020;)', 'rcp' ),
		'ILS' => __( 'Israeli Shekel (&#8362;)', 'rcp' ),
		'JPY' => __( 'Japanese Yen (&#165;)', 'rcp' ),
		'MYR' => __( 'Malaysian Ringgits (&#82;&#77;)', 'rcp' ),
		'MXN' => __( 'Mexican Peso (&#36;)', 'rcp' ),
		'NZD' => __( 'New Zealand Dollar (&#36;)', 'rcp' ),
		'NOK' => __( 'Norwegian Krone (&#107;&#114;)', 'rcp' ),
		'PHP' => __( 'Philippine Pesos (&#8369;)', 'rcp' ),
		'PLN' => __( 'Polish Zloty (&#122;&#322;)', 'rcp' ),
		'RUB' => __( 'Russian Rubles (&#1088;&#1091;&#1073;)', 'rcp' ),
		'SGD' => __( 'Singapore Dollar (&#36;)', 'rcp' ),
		'SEK' => __( 'Swedish Krona (&#107;&#114;)', 'rcp' ),
		'CHF' => __( 'Swiss Franc (&#67;&#72;&#70;)', 'rcp' ),
		'TWD' => __( 'Taiwan New Dollars (&#78;&#84;&#36;)', 'rcp' ),
		'THB' => __( 'Thai Baht (&#3647;)', 'rcp' ),
		'TRY' => __( 'Turkish Lira (&#8356;)', 'rcp' )
	);
	return apply_filters( 'rcp_currencies', $currencies );
}

/**
 * Is odd?
 *
 * Checks if a number is odd.
 *
 * @param int $int Number to check.
 *
 * @access private
 * @return bool
 */
function rcp_is_odd( $int ) {
	return $int & 1;
}


/**
 * Gets the excerpt of a specific post ID or object.
 *
 * @param object/int $post The ID or object of the post to get the excerpt of.
 * @param int $length The length of the excerpt in words.
 * @param string $tags The allowed HTML tags. These will not be stripped out.
 * @param string $extra Text to append to the end of the excerpt.
 *
 * @return string Post excerpt.
 */
function rcp_excerpt_by_id( $post, $length = 50, $tags = '<a><em><strong><blockquote><ul><ol><li><p>', $extra = ' . . .' ) {

	if ( is_int( $post ) ) {
		// get the post object of the passed ID
		$post = get_post( $post );
	} elseif ( !is_object( $post ) ) {
		return false;
	}
	$more = false;
	if ( has_excerpt( $post->ID ) ) {
		$the_excerpt = $post->post_excerpt;
	} elseif ( strstr( $post->post_content, '<!--more-->' ) ) {
		$more = true;
		$length = strpos( $post->post_content, '<!--more-->' );
		$the_excerpt = $post->post_content;
	} else {
		$the_excerpt = $post->post_content;
	}

	$tags = apply_filters( 'rcp_excerpt_tags', $tags );

	if ( $more ) {
		$the_excerpt = strip_shortcodes( strip_tags( stripslashes( substr( $the_excerpt, 0, $length ) ), $tags ) );
	} else {
		$the_excerpt = strip_shortcodes( strip_tags( stripslashes( $the_excerpt ), $tags ) );
		$the_excerpt = preg_split( '/\b/', $the_excerpt, $length * 2+1 );
		$excerpt_waste = array_pop( $the_excerpt );
		$the_excerpt = implode( $the_excerpt );
		$the_excerpt .= $extra;
	}

	$the_excerpt = wpautop( $the_excerpt );

	/**
	 * Filters the post excerpt.
	 *
	 * @param string  $the_excerpt Generated post excerpt.
	 * @param WP_Post $post        Post object.
	 * @param int     $length      Desired length of the excerpt in words.
	 * @param string  $tags        The allowed HTML tags. These will not be stripped out.
	 * @param string  $extra       Text to append to the end of the excerpt.
	 *
	 * @since 2.9.3
	 */
	return apply_filters( 'rcp_post_excerpt', $the_excerpt, $post, $length, $tags, $extra );
}


/**
 * The default length for excerpts.
 *
 * @param int $excerpt_length Number of words to show in the excerpt.
 *
 * @access private
 * @return string
 */
function rcp_excerpt_length( $excerpt_length ) {
	// the number of words to show in the excerpt
	return 100;
}
add_filter( 'rcp_filter_excerpt_length', 'rcp_excerpt_length' );


/**
 * Get current URL.
 *
 * Returns the URL to the current page, including detection for https.
 *
 * @access private
 * @return string
 */
function rcp_get_current_url() {
	global $post;

	if ( is_singular() ) :

		$current_url = get_permalink( $post->ID );

	else :

		global $wp;

		if( get_option( 'permalink_structure' ) ) {

			$base = trailingslashit( home_url( $wp->request ) );

		} else {

			$base = add_query_arg( $wp->query_string, '', trailingslashit( home_url( $wp->request ) ) );
			$base = remove_query_arg( array( 'post_type', 'name' ), $base );

		}

		$scheme      = is_ssl() ? 'https' : 'http';
		$current_url = set_url_scheme( $base, $scheme );

	endif;

	return apply_filters( 'rcp_current_url', $current_url );
}


/**
 * Check if "Prevent Account Sharing" is enabled.
 *
 * @access private
 * @since  1.4
 * @return bool
 */
function rcp_no_account_sharing() {
	global $rcp_options;
	return (bool) apply_filters( 'rcp_no_account_sharing', isset( $rcp_options['no_login_sharing'] ) );
}


/**
 * Stores cookie value in a transient when a user logs in.
 *
 * Transient IDs are based on the user ID so that we can track the number of
 * users logged into the same account.
 *
 * @param string $logged_in_cookie The logged-in cookie.
 * @param int    $expire           The time the login grace period expires as a UNIX timestamp.
 *                                 Default is 12 hours past the cookie's expiration time.
 * @param int    $expiration       The time when the logged-in authentication cookie expires as a UNIX timestamp.
 *                                 Default is 14 days from now.
 * @param int    $user_id          User ID.
 * @param string $status           Authentication scheme. Default 'logged_in'.
 *
 * @access private
 * @since  1.5
 * @return void
 */
function rcp_set_user_logged_in_status( $logged_in_cookie, $expire, $expiration, $user_id, $status = 'logged_in' ) {

	if( ! rcp_no_account_sharing() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! empty( $user_id ) ) :

		$data = get_transient( 'rcp_user_logged_in_' . $user_id );

		if( false === $data )
			$data = array();

		$data[] = $logged_in_cookie;

		set_transient( 'rcp_user_logged_in_' . $user_id, $data );

	endif;
}
add_action( 'set_logged_in_cookie', 'rcp_set_user_logged_in_status', 10, 5 );


/**
 * Removes the current user's auth cookie from the rcp_user_logged_in_# transient when logging out.
 *
 * @access private
 * @since  1.5
 * @return void
 */
function rcp_clear_auth_cookie() {

	if( ! rcp_no_account_sharing() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! isset( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
		return;
	}

	$user_id = get_current_user_id();

	$already_logged_in = get_transient( 'rcp_user_logged_in_' . $user_id );

	if ( is_serialized( $already_logged_in ) ) {
		preg_match( '/[oO]\s*:\s*\d+\s*:\s*"\s*(?!(?i)(stdClass))/', $already_logged_in, $matches );
		if ( ! empty( $matches ) ) {
			$already_logged_in = false;
		}
	}

	if( $already_logged_in !== false ) :

		$data = maybe_unserialize( $already_logged_in );

		$key = array_search( $_COOKIE[LOGGED_IN_COOKIE], $data );
		if( false !== $key ) {
			unset( $data[$key] );
			$data = array_values( $data );
			set_transient( 'rcp_user_logged_in_' . $user_id, $data );
		}

	endif;

}
add_action( 'clear_auth_cookie', 'rcp_clear_auth_cookie' );


/**
 * Checks if a user is allowed to be logged-in.
 *
 * The transient related to the user is retrieved and the first cookie in the transient
 * is compared to the LOGGED_IN_COOKIE of the current user.
 *
 * The first cookie in the transient is the oldest, so it is the one that gets logged out.
 *
 * We only log a user out if there are more than 2 users logged into the same account.
 *
 * @access private
 * @since  1.5
 * @return void
 */
function rcp_can_user_be_logged_in() {
	if ( is_user_logged_in() && rcp_no_account_sharing() ) {

		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! isset( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
			return;
		}

		$user_id = get_current_user_id();

		$already_logged_in = get_transient( 'rcp_user_logged_in_' . $user_id );

		if ( is_serialized( $already_logged_in ) ) {
			preg_match( '/[oO]\s*:\s*\d+\s*:\s*"\s*(?!(?i)(stdClass))/', $already_logged_in, $matches );
			if ( ! empty( $matches ) ) {
				$already_logged_in = false;
			}
		}

		if( $already_logged_in !== false ) {

			$data = maybe_unserialize( $already_logged_in );

			// remove the oldest logged in users
			$prev_data_count = count( $data );
			while ( count( $data ) >= 2 ) {
				unset( $data[0] );
				$data = array_values( $data );
			}

			// save modified data
			if ( count( $data ) != $prev_data_count ) {
				set_transient( 'rcp_user_logged_in_' . $user_id, $data );
			}

			if( ! in_array( $_COOKIE[LOGGED_IN_COOKIE], $data ) ) {

				// Log the user out - this is one of the oldest user logged into this account
				wp_logout();
				wp_safe_redirect( trailingslashit( get_bloginfo( 'wpurl' ) ) . 'wp-login.php?loggedout=true' );
			}

		}
	}
}
add_action( 'init', 'rcp_can_user_be_logged_in' );


/**
 * Retrieve a list of the allowed HTML tags.
 *
 * This is used for filtering HTML in subscription level descriptions and other places.
 *
 * @access public
 * @since  1.5
 * @return array
 */
function rcp_allowed_html_tags() {
	$tags = array(
		'p' => array(
			'class' => array()
		),
		'span' => array(
			'class' => array()
		),
		'a' => array(
       		'href' => array(),
        	'title' => array(),
        	'class' => array()
        ),
		'strong' => array(),
		'em' => array(),
		'br' => array(),
		'img' => array(
       		'src' => array(),
        	'title' => array(),
        	'alt' => array()
        ),
		'div' => array(
			'class' => array()
		),
		'ul' => array(
			'class' => array()
		),
		'li' => array(
			'class' => array()
		)
	);

	return apply_filters( 'rcp_allowed_html_tags', $tags );
}


/**
 * Checks whether function is disabled.
 *
 * @param  string $function Name of the function.
 *
 * @access public
 * @since  1.5
 * @return bool Whether or not function is disabled.
 */
function rcp_is_func_disabled( $function ) {
	$disabled = explode( ',',  ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}


/**
 * Converts the month number to the month name
 *
 * @param  int $n Month number.
 *
 * @access public
 * @since  1.8
 * @return string The name of the month.
 */
if( ! function_exists( 'rcp_get_month_name' ) ) {
	function rcp_get_month_name($n) {
		$timestamp = mktime(0, 0, 0, $n, 1, 2005);

		return date_i18n( "F", $timestamp );
	}
}

/**
 * Retrieve timezone.
 *
 * @since  1.8
 * @return string $timezone The timezone ID.
 */
function rcp_get_timezone_id() {

    // if site timezone string exists, return it
    if ( $timezone = get_option( 'timezone_string' ) )
        return $timezone;

    // get UTC offset, if it isn't set return UTC
    if ( ! ( $utc_offset = 3600 * get_option( 'gmt_offset', 0 ) ) )
        return 'UTC';

    // attempt to guess the timezone string from the UTC offset
    $timezone = timezone_name_from_abbr( '', $utc_offset );

    // last try, guess timezone string manually
    if ( $timezone === false ) {

        $is_dst = date('I');

        foreach ( timezone_abbreviations_list() as $abbr ) {
            foreach ( $abbr as $city ) {
                if ( $city['dst'] == $is_dst &&  $city['offset'] == $utc_offset )
                    return $city['timezone_id'];
            }
        }
    }

    // fallback
    return 'UTC';
}

/**
 * Get the number of days in a particular month.
 *
 * @param int $calendar Calendar to use for calculation.
 * @param int $month    Month in the selected calendar.
 * @param int $year     Year in the selected calendar.
 *
 * @since  2.0.9
 * @return string $timezone The timezone ID.
 */
if ( ! function_exists( 'cal_days_in_month' ) ) {
	// Fallback in case the calendar extension is not loaded in PHP
	// Only supports Gregorian calendar
	function cal_days_in_month( $calendar, $month, $year ) {
		return date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
	}
}

/**
 * Retrieves the payment status label for a payment.
 *
 * @param int|object $payment Payment ID or database object.
 *
 * @since  2.1
 * @return string
 */
function rcp_get_payment_status_label( $payment ) {

	if( is_numeric( $payment ) ) {
		$payments = new RCP_Payments();
		$payment  = $payments->get_payment( $payment );
	}

	if( ! $payment ) {
		return '';
	}

	$status = ! empty( $payment->status ) ? $payment->status : 'complete';

	switch( $status ) {

		case 'pending' :
			$label = __( 'Pending', 'rcp' );
			break;

		case 'refunded' :
			$label = __( 'Refunded', 'rcp' );
			break;

		case 'abandoned' :
			$label = __( 'Abandoned', 'rcp' );
			break;

		case 'failed' :
			$label = __( 'Failed', 'rcp' );
			break;

		case 'complete' :
		default :
			$label = __( 'Complete', 'rcp' );
			break;
	}

	return apply_filters( 'rcp_payment_status_label', $label, $status, $payment );

}

/**
 * Get User IP.
 *
 * Returns the IP address of the current visitor.
 *
 * @since  1.3
 * @return string $ip User's IP address.
 */
function rcp_get_ip() {

	$ip = '127.0.0.1';

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'rcp_get_ip', $ip );
}

/**
 * Checks to see if content is restricted in any way.
 *
 * @param  int $post_id The post ID to check for restrictions.
 *
 * @since  2.5
 * @return bool True if the content is restricted, false if not.
 */
function rcp_is_restricted_content( $post_id ) {

	if ( empty( $post_id ) || ! is_numeric( $post_id ) ) {
		return false;
	}

	$post_id = absint( $post_id );

	// Check post type restrictions.
	$restricted = rcp_is_restricted_post_type( get_post_type( $post_id ) );

	// Check post restrictions.
	if ( ! $restricted ) {
		$restricted = rcp_has_post_restrictions( $post_id );
	}

	// Check if the post is restricted via a term.
	if ( ! $restricted ) {
		$term_restricted_post_ids = rcp_get_post_ids_assigned_to_restricted_terms();
		if ( in_array( $post_id, $term_restricted_post_ids ) ) {
			$restricted = true;
		}
	}

	return apply_filters( 'rcp_is_restricted_content', $restricted, $post_id );

}

/**
 * Checks to see if a given post has any restrictions. This checks post
 * restrictions only via the Edit Post meta box.
 *
 * @param int $post_id The post ID to check for restrictions.
 *
 * @since 2.8.2
 * @return bool True if the post has restrictions.
 */
function rcp_has_post_restrictions( $post_id ) {

	if ( empty( $post_id ) || ! is_numeric( $post_id ) ) {
		return false;
	}

	$restricted = false;

	$post_id = absint( $post_id );

	if ( ! $restricted && get_post_meta( $post_id, '_is_paid', true ) ) {
		$restricted = true;
	}

	if ( ! $restricted && rcp_get_content_subscription_levels( $post_id ) ) {
		$restricted = true;
	}

	if ( ! $restricted ) {
		$rcp_user_level = get_post_meta( $post_id, 'rcp_user_level', true );
		if ( ! empty( $rcp_user_level ) && 'all' !== strtolower( $rcp_user_level ) ) {
			$restricted = true;
		}
	}

	if ( ! $restricted ) {
		$rcp_access_level = get_post_meta( $post_id, 'rcp_access_level', true );
		if ( ! empty( $rcp_access_level ) && 'None' !== $rcp_access_level ) {
			$restricted = true;
		}
	}

	return (bool) apply_filters( 'rcp_has_post_restrictions', $restricted, $post_id );

}

/**
 * Returns an array of all restricted post types (keys) and their restriction
 * settings (values).
 *
 * @since 2.9
 * @return array
 */
function rcp_get_restricted_post_types() {
	return get_option( 'rcp_restricted_post_types', array() );
}

/**
 * Get restrictions for a specific post type.
 *
 * @param string $post_type The post type to check.
 *
 * @since 2.9
 * @return array Array of restriction settings.
 */
function rcp_get_post_type_restrictions( $post_type ) {
	$restricted_post_types = rcp_get_restricted_post_types();
	return array_key_exists( $post_type, $restricted_post_types ) ? $restricted_post_types[ $post_type ] : array();
}

/**
 * Checks to see if a given post type has global restrictions applied.
 *
 * @param string $post_type The post type to check.
 *
 * @since 2.9
 * @return bool True if the post type is restricted in some way.
 */
function rcp_is_restricted_post_type( $post_type ) {
	$restrictions = rcp_get_post_type_restrictions( $post_type );

	return ! empty( $restrictions );
}

/**
 * Check the provided taxonomy along with the given post id to see if any restrictions are found
 *
 * @since      2.5
 * @param int      $post_id ID of the post to check.
 * @param string   $taxonomy
 * @param null|int $user_id User ID or leave as null to use curently logged in user.
 *
 * @return int|bool true if tax is restricted, false if user can access, -1 if unrestricted or invalid
 */
function rcp_is_post_taxonomy_restricted( $post_id, $taxonomy, $user_id = null ) {

	$restricted = -1;

	if ( current_user_can( 'edit_post', $post_id ) ) {
		return $restricted;
	}

	// make sure this post supports the supplied taxonomy
	$post_taxonomies = get_post_taxonomies( $post_id );
	if ( ! in_array( $taxonomy, (array) $post_taxonomies ) ) {
		return $restricted;
	}

	$terms = get_the_terms( $post_id, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return $restricted;
	}

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// Loop through the categories and determine if one has restriction options
	foreach( $terms as $term ) {

		$term_meta = rcp_get_term_restrictions( $term->term_id );

		if ( empty( $term_meta['paid_only'] ) && empty( $term_meta['subscriptions'] ) && ( empty( $term_meta['access_level'] ) || 'None' == $term_meta['access_level'] ) ) {
			continue;
		}

		$restricted = true;

		/** Check that the user has a paid subscription ****************************************************************/
		$paid_only = ! empty( $term_meta['paid_only'] );
		if( $paid_only && rcp_is_paid_user( $user_id ) ) {
			$restricted = false;
			break;
		}

		/** If restricted to one or more subscription levels, make sure that the user is a member of one of the levels */
		$subscriptions = ! empty( $term_meta['subscriptions'] ) ? array_map( 'absint', $term_meta['subscriptions'] ) : false;
		if( $subscriptions && in_array( rcp_get_subscription_id( $user_id ), $subscriptions ) ) {
			$restricted = false;
			break;
		}

		/** If restricted to one or more access levels, make sure that the user is a member of one of the levls ********/
		$access_level = ! empty( $term_meta['access_level'] ) ? absint( $term_meta['access_level'] ) : 0;
		if( $access_level > 0 && rcp_user_has_access( $user_id, $access_level ) ) {
			$restricted = false;
			break;
		}
	}

	return apply_filters( 'rcp_is_post_taxonomy_restricted', $restricted, $taxonomy, $post_id, $user_id );
}

/**
 * Get RCP Currency.
 *
 * @since  2.5
 * @return string
 */
function rcp_get_currency() {
	global $rcp_options;
	$currency = isset( $rcp_options['currency'] ) ? strtoupper( $rcp_options['currency'] ) : 'USD';
	return apply_filters( 'rcp_get_currency', $currency );
}

/**
 * Determines if a given currency code matches the currency selected in the settings.
 *
 * @param string $currency_code Currency code to check.
 *
 * @since  2.7.2
 * @return bool
 */
function rcp_is_valid_currency( $currency_code ) {
	$valid = strtolower( $currency_code ) == strtolower( rcp_get_currency() );

	return (bool) apply_filters( 'rcp_is_valid_currency', $valid, $currency_code );
}

/**
 * Determines if RCP is using a zero-decimal currency.
 *
 * @param  string $currency
 *
 * @access public
 * @since  2.5
 * @return bool True if currency set to a zero-decimal currency.
 */
function rcp_is_zero_decimal_currency( $currency = '' ) {

	if ( ! $currency ) {
		$currency = strtoupper( rcp_get_currency() );
	}

	$zero_dec_currencies = array(
		'BIF',
		'CLP',
		'DJF',
		'GNF',
		'JPY',
		'KMF',
		'KRW',
		'MGA',
		'PYG',
		'RWF',
		'VND',
		'VUV',
		'XAF',
		'XOF',
		'XPF'
	);

	return apply_filters( 'rcp_is_zero_decimal_currency', in_array( $currency, $zero_dec_currencies ) );

}

/**
 * Sets the number of decimal places based on the currency.
 *
 * @param  int $decimals The number of decimal places. Default is 2.
 *
 * @since  2.5.2
 * @return int The number of decimal places.
 */
function rcp_currency_decimal_filter( $decimals = 2 ) {

	$currency = rcp_get_currency();

	if ( rcp_is_zero_decimal_currency( $currency ) ) {
		$decimals = 0;
	}

	return apply_filters( 'rcp_currency_decimal_filter', $decimals, $currency );
}

/**
 * Formats the payment amount for display to enforce decimal places and add currency symbol.
 *
 * @param float|int $amount
 *
 * @since 2.9.5
 * @return float
 */
function rcp_format_amount( $amount ) {
	// Enforce decimals, configure thousands separator.
	$new_amount = number_format_i18n( $amount, rcp_currency_decimal_filter() );

	// Prefix with currency symbol.
	$new_amount = rcp_currency_filter( $new_amount );

	return $new_amount;
}

/**
 * Gets the taxonomy term ids connected to the specified post ID.
 *
 * @param  int $post_id The post ID.
 *
 * @since 2.7
 * @return array An array of taxonomy term IDs connected to the post.
 */
function rcp_get_connected_term_ids( $post_id = 0 ) {
	$taxonomies = array_values( get_taxonomies( array( 'public' => true ) ) );
	$terms      = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );

	return $terms;
}

/**
 * Gets all post IDs that are assigned to restricted taxonomy terms.
 *
 * @since 2.7
 * @return array An array of post IDs assigned to restricted taxonomy terms.
 */
function rcp_get_post_ids_assigned_to_restricted_terms() {

	global $wpdb;

	if ( false === ( $post_ids = get_transient( 'rcp_post_ids_assigned_to_restricted_terms' ) ) ) {
		$post_ids = array();

		/**
		 * Get all terms with the 'rcp_restricted_meta' key.
		 */
		$terms = get_terms(
			array_values( get_taxonomies( array( 'public' => true ) ) ),
			array(
				'hide_empty' => false,
				'meta_query' => array(
					array(
						'key' => 'rcp_restricted_meta'
					)
				)
			)
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			set_transient( 'rcp_post_ids_assigned_to_restricted_terms', array(), DAY_IN_SECONDS );
			return array();
		}

		foreach ( $terms as $term ) {

			/**
			 * For legacy reasons, we need to check for empty meta
			 * and for meta with just an access_level of 'None'
			 * and ignore them.
			 */
			$meta = get_term_meta( $term->term_id , 'rcp_restricted_meta', true );

			if ( empty( $meta ) ) {
				// Remove the legacy metadata
				delete_term_meta( $term->term_id, 'rcp_restricted_meta' );
				continue;
			}

			if ( 1 === count( $meta) && array_key_exists( 'access_level', $meta ) && 'None' === $meta['access_level'] ) {
				// Remove the legacy metadata
				delete_term_meta( $term->term_id, 'rcp_restricted_meta' );
				continue;
			}

			$p_ids = $wpdb->get_results( $wpdb->prepare( "SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", absint( $term->term_taxonomy_id ) ), ARRAY_A );
			foreach( $p_ids as $p_id ) {
				if ( ! in_array( $p_id['object_id'], $post_ids ) ) {
					$post_ids[] = $p_id['object_id'];
				}
			}
		}

		set_transient( 'rcp_post_ids_assigned_to_restricted_terms', $post_ids, DAY_IN_SECONDS );
	}

	return $post_ids;
}

/**
 * Gets a list of post IDs with post-level restrictions defined.
 *
 * @since 2.7
 * @return array An array of post IDs.
 */
function rcp_get_restricted_post_ids() {

	if ( false === ( $post_ids = get_transient( 'rcp_restricted_post_ids' ) ) ) {

		$post_ids = get_posts( array(
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post_type'      => 'any',
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'   => '_is_paid',
					'value' => 1
				),
				array(
					'key' => 'rcp_subscription_level'
				),
				array(
					'key'     => 'rcp_user_level',
					'value'   => 'All',
					'compare' => '!='
				),
				array(
					'key'     => 'rcp_access_level',
					'value'   => 'None',
					'compare' => '!='
				)
			)
		) );

		set_transient( 'rcp_restricted_post_ids', $post_ids, DAY_IN_SECONDS );
	}

	return $post_ids;
}

/**
 * Clears the transient that holds the post IDs with post-level restrictions defined.
 *
 * @param int $post_id
 *
 * @since 2.7
 */
function rcp_delete_transient_restricted_post_ids( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	delete_transient( 'rcp_restricted_post_ids' );
	delete_transient( 'rcp_post_ids_assigned_to_restricted_terms' );
}
add_action( 'save_post', 'rcp_delete_transient_restricted_post_ids' );
add_action( 'wp_trash_post', 'rcp_delete_transient_restricted_post_ids' );
add_action( 'untrash_post', 'rcp_delete_transient_restricted_post_ids' );

/**
 * Clears the transient that holds the post IDs that are assigned to restricted taxonomy terms.
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 *
 * @return void
 */
function rcp_delete_transient_post_ids_assigned_to_restricted_terms( $term_id, $tt_id, $taxonomy ) {
	delete_transient( 'rcp_post_ids_assigned_to_restricted_terms' );
}
add_action( 'edited_term', 'rcp_delete_transient_post_ids_assigned_to_restricted_terms', 10, 3 );

/**
 * Log a message to the debug file if debug mode is enabled.
 *
 * @param string $message Message to log.
 * @param bool   $force   Whether to force log a message, even if debugging is disabled.
 *
 * @since 2.9
 * @return void
 */
function rcp_log( $message = '', $force = false ) {
	global $rcp_options;

	if ( empty( $rcp_options['debug_mode'] ) && ! $force ) {
		return;
	}

	$logs = new RCP_Logging();
	$logs->log( $message );
}
