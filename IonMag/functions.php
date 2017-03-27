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

//让WordPress支持用户名或邮箱登录
function dr_email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;
 
	if ( !empty( $username ) ) {
		$username = str_replace( '&', '&', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}
 
	return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'dr_email_login_authenticate', 20, 3 );
 
//替换“用户名”为“用户名 / 邮箱”
function username_or_email_login() {
	if ( 'wp-login.php' != basename( $_SERVER['SCRIPT_NAME'] ) )
		return;
 
	?><script type="text/javascript">
	// Form Label
	if ( document.getElementById('loginform') )
		document.getElementById('loginform').childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( '用户名/邮箱', 'email-login' ) ); ?>';
 
	// Error Messages
	if ( document.getElementById('login_error') )
		document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( '用户名' ) ); ?>', '<?php echo esc_js( __( '用户名/邮箱' , 'email-login' ) ); ?>' );
	</script><?php
}
add_action( 'login_form', 'username_or_email_login' );

//隐藏核心更新提示
add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
 
//隐藏插件更新提示
remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$b', "return null;" ) );
 
//隐藏主题更新提示
remove_action( 'load-update-core.php', 'wp_update_themes' );
add_filter( 'pre_site_transient_update_themes', create_function( '$c', "return null;" ) );

//仅仅隐藏升级提示
add_action(‘admin_menu’,’wp_hide_nag’);
function wp_hide_nag() {
remove_action( ‘admin_notices’, ‘update_nag’, 3 );
}

//自定义用户个人资料信息
add_filter( 'user_contactmethods', 'wpdaxue_add_contact_fields' );
function wpdaxue_add_contact_fields( $contactmethods ) {
//	$contactmethods['qq'] = 'QQ';
	unset( $contactmethods['behance'] );
	unset( $contactmethods['blogger'] );
	unset( $contactmethods['delicious'] );
        unset( $contactmethods['deviantart'] );
	unset( $contactmethods['digg'] );
	unset( $contactmethods['dribbble'] );
        unset( $contactmethods['evernote'] );
	unset( $contactmethods['facebook'] );
	unset( $contactmethods['flickr'] );
        unset( $contactmethods['forrst'] );
	unset( $contactmethods['googleplus'] );
	unset( $contactmethods['grooveshark'] );
        unset( $contactmethods['instagram'] );
	unset( $contactmethods['lastfm'] );
	unset( $contactmethods['linkedin'] );
	unset( $contactmethods['mail-1'] );
	unset( $contactmethods['myspace'] );
        unset( $contactmethods['path'] );
	unset( $contactmethods['paypal'] );
	unset( $contactmethods['pinterest'] );
        unset( $contactmethods['reddit'] );
	unset( $contactmethods['rss'] );
	unset( $contactmethods['share'] );
        unset( $contactmethods['skype'] );
	unset( $contactmethods['soundcloud'] );
	unset( $contactmethods['spotify'] );
        unset( $contactmethods['stackoverflow'] );
	unset( $contactmethods['steam'] );
	unset( $contactmethods['stumbleupon'] );
        unset( $contactmethods['tumblr'] );
	unset( $contactmethods['twitter'] );
        unset( $contactmethods['vimeo'] );
	unset( $contactmethods['vk'] );
	unset( $contactmethods['windows'] );
	unset( $contactmethods['wordpress'] );
	unset( $contactmethods['yahoo'] );
        unset( $contactmethods['youtube'] );
	unset( $contactmethods['url'] );
	return $contactmethods;
}

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

// 评论添加@作者
function ludou_comment_add_at( $comment_text, $comment = '') {
  if( $comment->comment_parent > 0) {
    $comment_text = '@<a href="#comment-' . $comment->comment_parent . '">'.get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text;
  }
  return $comment_text;
}
add_filter( 'comment_text' , 'ludou_comment_add_at', 20, 2);

// 移除头部冗余代码
remove_action( 'wp_head', 'wp_generator' );                                        // WP版本信息
remove_action( 'wp_head', 'rsd_link' );                                                // 离线编辑器接口
remove_action( 'wp_head', 'wlwmanifest_link' );                                    // 同上
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );    // 上下文章的url
remove_action( 'wp_head', 'feed_links', 2 );                                         // 文章和评论feed
remove_action( 'wp_head', 'feed_links_extra', 3 );                                // 去除评论feed
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );                   // 短链接

//移除顶部工具栏
add_action( 'admin_bar_menu', 'cwp_remove_wp_logo_from_admin_bar_new', 25 );
function cwp_remove_wp_logo_from_admin_bar_new( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
}

//移除版本号
if(!function_exists('cwp_remove_script_version')){
    function cwp_remove_script_version( $src ){  return remove_query_arg( 'ver', $src ); }
    add_filter( 'script_loader_src', 'cwp_remove_script_version' );
    add_filter( 'style_loader_src', 'cwp_remove_script_version' );
}

//移除部分自带小工具
function coolwp_remove_meta_widget() {
   unregister_widget('WP_Widget_Pages');
   unregister_widget('WP_Widget_Calendar');
   unregister_widget('WP_Widget_Archives');
   unregister_widget('WP_Widget_Links');
   unregister_widget('WP_Widget_Meta');
   //unregister_widget('WP_Widget_Search');
   //unregister_widget('WP_Widget_Text');
   //unregister_widget('WP_Widget_Categories');
   //unregister_widget('WP_Widget_Recent_Posts');
   //unregister_widget('WP_Widget_Recent_Comments');
   unregister_widget('WP_Widget_RSS');
   unregister_widget('WP_Widget_Tag_Cloud');
   //unregister_widget('WP_Nav_Menu_Widget');
}
add_action( 'widgets_init', 'coolwp_remove_meta_widget',11 );

//移除仪表盘首页部分内容
function cwp_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'cwp_remove_dashboard_widgets',11 );

// Break Out of Frames for WordPress
function break_out_of_frames() {
     if (!is_preview()) {
          echo "\n<script type=\"text/javascript\">";
          echo "\n<!--";
          echo "\nif (parent.frames.length > 0) { parent.location.href = location.href; }";
          echo "\n-->";
          echo "\n</script>\n\n";
     }
}
add_action('wp_head', 'break_out_of_frames');

//只在后台显示顶部工具栏
if ( !is_admin() ) {  
    add_filter('show_admin_bar', '__return_false'); 
}

//禁用谷歌字体
function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );

//优化站点推送至百度
add_action( 'wp_enqueue_scripts', 'wpjam_baidu_zz_enqueue_scripts' );
function wpjam_baidu_zz_enqueue_scripts(){
    wp_enqueue_script( 'baidu_zz_push', 'http://push.zhanzhang.baidu.com/push.js');
}
add_action('save_post', 'wpjam_save_post_notify_baidu_zz', 10, 3);
function wpjam_save_post_notify_baidu_zz($post_id, $post, $update){
	if($post->post_status != 'publish') return;

	$baidu_zz_api_url	= 'http://data.zz.baidu.com/urls?site=www.eurovisionchina.com&token=rgeL1Wcev9sHXuGz';
	//百度站长后台专属提交链接
	
	$response	= wp_remote_post($baidu_zz_api_url, array(
		'headers'	=> array('Accept-Encoding'=>'','Content-Type'=>'text/plain'),
		'sslverify'	=> false,
		'blocking'	=> false,
		'body'		=> get_permalink($post_id)
	));
}
