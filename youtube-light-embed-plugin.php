<?php
/*
Plugin Name: YouTube Light Embed
Description: Light embed for YouTube with lazy load and privacy disclaimer.
Version: 3.17
Author: Eric Leclercq <eric@bananenbiegerei.de>
Text Domain: youtube-light-embed-plugin
Requires at least: 5.3
Requires PHP: 5.5
BB Update Checker: enabled
*/

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
  die('Access denied.');
}

class YouTubeLightEmbed
{
  public $options;

  public function __construct()
  {
    // Check if ACF is available
    if (function_exists('acf_register_block_type')) {
      add_action('acf/init', [$this, 'registerACFBlock']);
    } else {
      add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><b>YouTube Light Embed:</b> ' .
          __('Required plugin "Advanced Custom Fields" is missing. Please install and activate.', 'youtube-light-embed-plugin') .
          '</p></div>';
      });
      return;
    }

    add_action('init', function () {
      load_plugin_textdomain('youtube-light-embed-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
      $this->get_options();
      $this->disable_youtube_block();
    });
    
    add_action('wp', [$this, 'enqueue_scripts']);
  }

  public function get_options()
  {
    $this->options = [
      'disclaimer' => __(
        'By playing the video you agree that YouTube and Google might store and process your data. Please refer to Google’s <a href="https://www.google.com/policies/privacy/">Privacy Policy</a>.',
        'youtube-light-embed-plugin',
      ),
      'disclaimer_bg_color' => '#ea3324',
      'disclaimer_color' => '#ffffff',
      'deny_button' => __('Deny', 'youtube-light-embed-plugin'),
      'accept_button' => __('Accept & Play', 'youtube-light-embed-plugin'),
    ];
  }

  public function enqueue_scripts()
  {
    if (!has_block('acf/youtube-light-embed')) {
        return;
    }
    wp_enqueue_style('youtube-light-embed', plugin_dir_url(__FILE__) . 'css/youtube-light-embed.css', [], '', 'all');
    $style = ":root { --ytleBG: {$this->options['disclaimer_bg_color']}; --ytleTXT: {$this->options['disclaimer_color']};}";
    wp_add_inline_style('youtube-light-embed', $style);
    wp_enqueue_script('youtube-light-embed', plugin_dir_url(__FILE__) . 'js/youtube-light-embed.js', [], '', ['strategy' => 'async']);
    $script = 'const YouTubeLightEmbedImgP = "' . plugin_dir_url(__FILE__) . 'poster.php"; const YouTubeLightEmbedDisclaimer = ' . json_encode($this->options['disclaimer']) . ';';
    wp_add_inline_script('youtube-light-embed', $script);
  }

  public function disable_youtube_block()
  {
    add_action('enqueue_block_editor_assets', function () {
      wp_enqueue_script('youtube-light-embed-editor', plugin_dir_url(__FILE__) . 'js/youtube-light-embed-editor.js', [], '', true);
    });

    // Replace youtube URL in old content...
    add_filter('the_content', function ($content) {
      return preg_replace('|src="http(s?)://www\.youtube\.com/embed/|', 'src="https://www.youtube-nocookie.com/embed/', $content);
    });
  }

  public function registerACFBlock()
  {
    acf_register_block_type([
      'name' => 'youtube-light-embed',
      'title' => __('YouTube Light Embed', 'youtube-light-embed-plugin'),
      'description' => __('Light embed for YouTube with lazy load and privacy disclaimer', 'youtube-light-embed-plugin'),
      'render_template' => plugin_dir_path(__FILE__) . 'acf-block.php',
      'category' => 'embed',
      'icon' => 'video-alt3',
      'keywords' => [],
      'mode' => false,
    ]);

    acf_add_local_field_group([
      'key' => 'group06447309f25b',
      'title' => __('YouTube Light Embed Block', 'youtube-light-embed-plugin'),
      'fields' => [
      [
      'key' => 'field066101d94b09',
      'label' => __('YouTube video URL', 'youtube-light-embed-plugin'),
      'name' => 'video_url',
      'type' => 'url',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => [
      [
        [
        'field' => 'field064473ef772e',
        'operator' => '==empty',
        ],
      ],
      ],
      'wrapper' => [
      'width' => '',
      'class' => '',
      'id' => '',
      ],
      'default_value' => '',
      'placeholder' => '',
      ],
      [
      'key' => 'field064473ef772e',
      'label' => __('Video ID'),
      'name' => 'video_ID',
      'type' => 'text',
      'instructions' => __('YouTube video ID (e.g. "PwYEfx4ojx0").', 'youtube-light-embed-plugin'),
      'required' => 0,
      'conditional_logic' => [
      [
        [
        'field' => 'field066101d94b09',
        'operator' => '==empty',
        ],
      ],
      ],
      'wrapper' => [
      'width' => '',
      'class' => '',
      'id' => '',
      ],
      'default_value' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
      'maxlength' => '',
      ],
      ],
      'location' => [
      [
      [
      'param' => 'block',
      'operator' => '==',
      'value' => 'acf/youtube-light-embed',
      ],
      ],
      ],
      'menu_order' => 0,
      'position' => 'normal',
      'stytle' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
    ]);
  }
}

$YouTubeLightEmbed = new YouTubeLightEmbed();
