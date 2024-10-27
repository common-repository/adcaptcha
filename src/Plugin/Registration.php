<?php

namespace AdCaptcha\Plugin\Registration;

use AdCaptcha\Widget\AdCaptcha\AdCaptcha;
use AdCaptcha\Widget\Verify\Verify;
use AdCaptcha\AdCaptchaPlugin\AdCaptchaPlugin;
use WP_Error;

class Registration extends AdCaptchaPlugin {

    public function setup() {
        global $adCAPTCHAWordpressRegistration;
        $adCAPTCHAWordpressRegistration = $this;
        add_action( 'register_form', [ AdCaptcha::class, 'enqueue_scripts' ] );
        add_action( 'register_form', [ Verify::class, 'get_success_token' ] );
        add_action( 'register_form', [ AdCaptcha::class, 'captcha_trigger' ] );
        add_action( 'registration_errors', [ $adCAPTCHAWordpressRegistration, 'verify' ], 10, 1 );
    }

    public function verify( $errors ) {
        $successToken = sanitize_text_field(wp_unslash($_POST['adcaptcha_successToken']));
        $verify = new Verify();
        $response = $verify->verify_token($successToken);


        if ( $response === false ) {
            $errors = new WP_Error('adcaptcha_error', __( 'Incomplete captcha, Please try again.', 'adcaptcha' ));
        }

        return $errors;
    }
}
