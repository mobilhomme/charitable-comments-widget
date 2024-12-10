<?php
/*
Plugin Name: Charitable Comments Widget Block
Description: Adds a block to display recent comments on Charitable campaigns.
Version: 1.0
Author: Jason Swihart
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function charitable_comments_widget_block_register() {
    // Automatically load dependencies and version
    $asset_file = include plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

    wp_register_script(
        'charitable-comments-widget-block',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file['dependencies'],
        $asset_file['version']
    );

    wp_register_style(
        'charitable-comments-widget-block-style',
        plugins_url( 'build/style.css', __FILE__ ),
        [],
        filemtime( plugin_dir_path( __FILE__ ) . 'build/style.css' )
    );

    wp_register_style(
        'charitable-comments-widget-block-editor-style',
        plugins_url( 'build/editor.css', __FILE__ ),
        [ 'wp-edit-blocks' ],
        filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' )
    );

    register_block_type( 'charitable/comments-widget', [
        'editor_script' => 'charitable-comments-widget-block',
        'editor_style'  => 'charitable-comments-widget-block-editor-style',
        'style'         => 'charitable-comments-widget-block-style',
        'render_callback' => 'charitable_comments_widget_render',
    ] );
}

add_action( 'init', 'charitable_comments_widget_block_register' );

function charitable_comments_widget_render( $attributes ) {
    $campaign_id = get_the_ID();
    $comments = get_comments( [ 'post_id' => $campaign_id, 'number' => 5 ] );

    if ( $comments ) {
        $output = '<ul class="charitable-comments-widget">';
        foreach ( $comments as $comment ) {
            $output .= sprintf(
                '<li><strong>%s:</strong> %s</li>',
                esc_html( $comment->comment_author ),
                esc_html( wp_trim_words( $comment->comment_content, 10, '...' ) )
            );
        }
        $output .= '</ul>';
    } else {
        $output = '<p>No comments yet.</p>';
    }

    return $output;
}

function charitable_comments_widget_enqueue_block_assets() {
    wp_enqueue_script(
        'charitable-comments-widget-script',
        plugins_url( 'build/index.js', __FILE__ ),
        include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php' ),
        null,
        true
    );
}
add_action( 'enqueue_block_assets', 'charitable_comments_widget_enqueue_block_assets' );

