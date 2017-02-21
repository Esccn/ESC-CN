<?php
/*
	Load the speed booster framework + theme specific files
*/

// load the deploy mode
require_once('td_deploy_mode.php');

// load the config
require_once('includes/td_config.php');
add_action('td_global_after', array('td_config', 'on_td_global_after_config'), 9); //we run on 9 priority to allow plugins to updage_key our apis while using the default priority of 10



// check and load the wp_booster framework
//if (!file_exists('includes/wp_booster/td_wp_booster_functions.php')) {
//    echo ':( wp_booster Framework not found! The framework should be in ' . TD_THEME_NAME . '/includes/wp_booster';
//    die;
//}
require_once('includes/wp_booster/td_wp_booster_functions.php');

require_once('includes/td_css_generator.php');
require_once('includes/widgets/td_page_builder_widgets.php'); // widgets

/**
 * 闅愯棌鏍稿績鏇存柊鎻愮ず WP 3.0+
 * 鏉ヨ嚜 http://wordpress.org/plugins/disable-wordpress-core-update/
 */
add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
 
/**
 * 闅愯棌鎻掍欢鏇存柊鎻愮ず WP 3.0+
 * 鏉ヨ嚜 http://wordpress.org/plugins/disable-wordpress-plugin-updates/
 */
remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$b', "return null;" ) );
 
/**
 * 闅愯棌涓婚鏇存柊鎻愮ず WP 3.0+
 * 鏉ヨ嚜 http://wordpress.org/plugins/disable-wordpress-theme-updates/
 */
remove_action( 'load-update-core.php', 'wp_update_themes' );
add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

//----------color-tag-cloud-----------

/* 爲WordPress添加「點擊展&#65533;_/收縮」功能&#65533;_始（由AREFLY.COM製作） */
function xcollapse($atts, $content = null){
	extract(shortcode_atts(array("title"=>""),$atts));
	return '<div style="margin: 0.5em 0;">
		<div class="xControl">
			<span class="xTitle">'.$title.'</span> 
			<a href="javascript:void(0)" class="collapseButton xButton">展开/收起</a>
			<div style="clear: both;"></div>
		</div>
		<div class="xContent" style="display: none;">'.$content.'</div>
	</div>';
}
add_shortcode('collapse', 'xcollapse');

//自动将文章第一张图片设置为特色图像
function autoset_featured() {
          global $post;
          $already_has_thumb = has_post_thumbnail($post->ID);
              if (!$already_has_thumb)  {
              $attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );
                          if ($attached_image) {
                                foreach ($attached_image as $attachment_id => $attachment) {
                                set_post_thumbnail($post->ID, $attachment_id);
                                }
                           }
                        }
      }  //end function
add_action('the_post', 'autoset_featured');
add_action('save_post', 'autoset_featured');
add_action('draft_to_publish', 'autoset_featured');
add_action('new_to_publish', 'autoset_featured');
add_action('pending_to_publish', 'autoset_featured');
add_action('future_to_publish', 'autoset_featured');

// 评论添加@作者 by Ludou
function ludou_comment_add_at( $comment_text, $comment = '') {
  if( $comment->comment_parent > 0) {
    $comment_text = '@<a href="#comment-' . $comment->comment_parent . '">'.get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text;
  }

  return $comment_text;
}
add_filter( 'comment_text' , 'ludou_comment_add_at', 20, 2);