<?php
/**
 * Plugin Name: SVP FundraiseUp
 * Description: Inserts the correct FundraiseUp widget based on visitor country, detected via Cloudflare.
 * Version:     1.1.2
 * Author:      SVP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function svp_maybe_set_country_cookie() {
    if ( isset( $_GET['svp_country'] ) && preg_match( '/^[A-Z]{2}$/', $_GET['svp_country'] ) ) {
        $cc = $_GET['svp_country'];
        setcookie( 'svp_country', $cc, [
            'expires'  => time() + ( 60 * 60 * 24 * 90 ), // 90 days
            'path'     => '/',
            'secure'   => true,
            'httponly' => false,
            'samesite' => 'Lax',
        ] );
        wp_safe_redirect( remove_query_arg( 'svp_country' ) );
        exit;
    }
}
add_action( 'init', 'svp_maybe_set_country_cookie' );

function svp_get_country() {
    static $cc = null;
    if ( $cc === null ) {
        if ( isset( $_COOKIE['svp_country'] ) && preg_match( '/^[A-Z]{2}$/', $_COOKIE['svp_country'] ) ) {
            $cc = $_COOKIE['svp_country'];
        } else {
            $raw = isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : 'XX';
            $cc  = preg_match( '/^[A-Z]{2}$/', $raw ) ? $raw : 'XX';
        }
    }
    return $cc;
}

function svp_fru_install_shortcode( $atts ) {
    $atts = is_array( $atts ) ? $atts : [];
    if ( empty( $atts['gb_widget'] ) || empty( $atts['intl_widget'] ) ) {
        return '<script>console.log("fru_install: gb_widget and intl_widget are required");</script>';
    }
    $widget_id = ( svp_get_country() === 'GB' ) ? $atts['gb_widget'] : $atts['intl_widget'];
    if ( ! preg_match( '/^[A-Z]{8}$/', $widget_id ) ) {
        return '<script>console.log("fru_install: invalid widget ID format");</script>';
    }
    $script = <<<JS
(function(w,d,s,n,a){if(!w[n]){var l='call,catch,on,once,set,then,track,openCheckout'.split(','),i,o=function(n){return'function'==typeof n?o.l.push([arguments])&&o:function(){return o.l.push([n,arguments])&&o}},t=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=!0;j.src='https://cdn.fundraiseup.com/widget/'+a+'';t.parentNode.insertBefore(j,t);o.s=Date.now();o.v=5;o.h=w.location.href;o.l=[];for(i=0;i<8;i++)o[l[i]]=o(l[i]);w[n]=o}})(window,document,'script','FundraiseUp','{$widget_id}');
JS;
    return '<script id="fru">' . $script . '</script>';
}
add_shortcode( 'fru_install', 'svp_fru_install_shortcode' );

function svp_fru_link_shortcode( $atts ) {
    $atts = shortcode_atts( [ 'gb_href' => '', 'intl_href' => '' ], $atts, 'fru_link' );
    $button_href = ( svp_get_country() === 'GB' ) ? $atts['gb_href'] : $atts['intl_href'];
    if ( empty( $button_href ) ) {
        return '<script>console.log("fru_link: shortcode used without gb_href/intl_href parameters — no link rendered");</script>';
    }
    if ( ! preg_match( '/^#[A-Z]{8}$/', $button_href ) ) {
        return '<script>console.log("fru_link: invalid href format");</script>';
    }
    return '<a id="fru-button" href="' . esc_url( $button_href ) . '" style="display: none;"></a>';
}
add_shortcode( 'fru_link', 'svp_fru_link_shortcode' );

function svp_fru_switch_shortcode( $atts ) {
    $switch_cc  = ( svp_get_country() === 'GB' ) ? 'XX' : 'GB';
    $switch_url = add_query_arg( 'svp_country', $switch_cc, get_permalink() );
    $defaults   = [ 'label' => ( $switch_cc === 'GB' ) ? 'Switch to UK giving' : 'Switch to international giving' ];
    $atts       = shortcode_atts( $defaults, $atts, 'fru_switch' );
    return '<a href="' . esc_url( $switch_url ) . '">' . esc_html( $atts['label'] ) . '</a>';
}
add_shortcode( 'fru_switch', 'svp_fru_switch_shortcode' );
