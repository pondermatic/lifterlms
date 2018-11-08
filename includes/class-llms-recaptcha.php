<?php
defined( 'ABSPATH' ) || exit;

/**
 * Load reCAPTCHA on open registration and free checkout registration forms
 * @since    [version]
 * @version  [version]
 */
class LLMS_ReCAPTCHA {

	/**
	 * Constructor
	 * @since    [version]
	 * @version  [version]
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'maybe_load' ) );

	}

	private function should_load() {

		$load = false;

		// Load on account page when open registration is enabled.
		if ( is_llms_account_page() && llms_parse_bool( get_option( 'lifterlms_enable_myaccount_registration', 'no' ) ) ) {
			$load = true;

		// Maybe load on checkout if user is not logged in.
		} elseif ( is_llms_checkout() && ! get_current_user_id() ) {

			$plan = absint( filter_input( INPUT_GET, 'plan', FILTER_SANITIZE_NUMBER_INT ) );
			$plan = $plan ? llms_get_post( $plan ) : false;

			// Load for free access plans.
			if ( $plan && $plan->is_free() ) {
				$load = true;
			}

		}

		return $load;

	}

	private static function get_keys() {

		$site_key = get_option( 'llms_recaptcha_site_key' );
		$secret = get_option( 'llms_recaptcha_secret_key' );

		if ( ! $site_key || ! $secret ) {
			return array();
		}

		return compact( 'site_key', 'secret' );

	}

	public function maybe_load() {

		if ( $this->should_load() ) {

			LLMS_ReCAPTCHA::output_script();

		}

	}

	public static function output_script() {

		$keys = self::get_keys();

		if ( ! $keys ) {
			return;
		}

		?>
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $keys['site_key']; ?>"></script>
		<script>
		grecaptcha.ready( function() {
			grecaptcha
				.execute( '<?php echo $keys['site_key']; ?>', { action: 'register' } )
				.then( function( token ) {

					LLMS.Ajax.call( {
						data: {
							action: 'llms_recaptcha',
							token: token,
						},
						success: function( ret ) {

							console.log( ret );

						}

					} );

				} );
		} );
		</script>
		<?php

	}

	public static function verify( $request ) {

		$keys = self::get_keys();
		if ( ! $keys ) {
			return;
		}

		$req = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
			'secret' => $keys['secret'],
			'response' => $request['token'],
			'remoteip' => llms_get_ip_address(),
		) );

		return json_decode( wp_remote_retrieve_body( $req ) );

	}

}

return new LLMS_ReCAPTCHA();
