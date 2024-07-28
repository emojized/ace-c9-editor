<?php
/*
 * Plugin Name:       ACE Cloud9 Code Editor
 * Plugin URI:        https://github.com/emojized/ace-c9-editor
 * Description:       Replacing the WP/CP Code Editor with the Cloud9 ACE
 * Version:           1.0
 * Requires at least: 4.9.15
 * Requires PHP:      7.4
 * Requires CP:       2.2
 * Author:            The emojized Team
 * Author URI:        https://emojized.com
 * License:           GPL v2 and BSD
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/


function c9_emojized_ace_deregister_all_scripts() {
    // Deregister each script
    wp_deregister_script('wp-codemirror');
    wp_deregister_script('csslint');
    wp_deregister_script('esprima');
    wp_deregister_script('jshint');
    wp_deregister_script('jsonlint');
    wp_deregister_script('htmlhint');
    wp_deregister_script('htmlhint-kses');
    wp_deregister_script('code-editor');
    wp_deregister_script('wp-theme-plugin-editor');
}
add_action('admin_enqueue_scripts', 'c9_emojized_ace_deregister_all_scripts', 100);


function c9_emojized_ace_admin_script() {
    // Check if we are in the admin area
    if (is_admin()) {
        // Register and enqueue the script
        wp_enqueue_script(
            'ace-editor', // Handle for the script
            plugins_url('ace.js', __FILE__), // URL to the ace.js script in the plugin directory
            array(), // No dependencies
            null, // Version number
            false // Load in header
        );
    }
}
// Hook into the admin_enqueue_scripts action
add_action('admin_enqueue_scripts', 'c9_emojized_ace_admin_script');

// Function to enqueue inline script on plugin-editor.php
function emojized_ace_inline_script_for_plugin_editor() {
    // Get the current screen object
    $current_screen = get_current_screen();

    // Check if we are on the plugin-editor.php page
    if ($current_screen->base === 'plugin-editor' OR $current_screen->base === 'theme-editor' ) {
        // Ensure ace-editor script is enqueued
        wp_enqueue_script('ace-editor');

        // Enqueue the inline script
        wp_add_inline_script(
                    'ace-editor', // Dependency on ace-editor
                    '
                    document.addEventListener("DOMContentLoaded", function() {
                        var textarea = document.querySelector("textarea#newcontent"); // Adjust selector as needed
                        if (textarea) {
                            // Create a div to replace the textarea
                            var div = document.createElement("div");
                            div.id = "ace-editor";
                            div.style.width = "100%";
                            div.style.height = "500px"; // Adjust height as needed
                            textarea.parentNode.insertBefore(div, textarea);
                            textarea.style.display = "none"; // Hide the textarea

                            // Initialize Ace editor on the div
                            var editor = ace.edit("ace-editor");
                            editor.setTheme("ace/theme/cobalt");
                            editor.session.setMode("ace/mode/php");
                            editor.setValue(textarea.value); // Set initial content

                            // Sync Ace editor content with the textarea
                            editor.getSession().on("change", function() {
                                textarea.value = editor.getValue();
                            });
                        }
                    });
                    ' // The actual inline script
                );
    }
}
// Hook into admin_enqueue_scripts
add_action('admin_enqueue_scripts', 'emojized_ace_inline_script_for_plugin_editor');


