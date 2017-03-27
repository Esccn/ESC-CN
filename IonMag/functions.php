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

//��WordPress֧���û����������¼
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
 
//�滻���û�����Ϊ���û��� / ���䡱
function username_or_email_login() {
	if ( 'wp-login.php' != basename( $_SERVER['SCRIPT_NAME'] ) )
		return;
 
	?><script type="text/javascript">
	// Form Label
	if ( document.getElementById('loginform') )
		document.getElementById('loginform').childNodes[1].childNodes[1].childNodes[0].nodeValue = '<?php echo esc_js( __( '�û���/����', 'email-login' ) ); ?>';
 
	// Error Messages
	if ( document.getElementById('login_error') )
		document.getElementById('login_error').innerHTML = document.getElementById('login_error').innerHTML.replace( '<?php echo esc_js( __( '�û���' ) ); ?>', '<?php echo esc_js( __( '�û���/����' , 'email-login' ) ); ?>' );
	</script><?php
}
add_action( 'login_form', 'username_or_email_login' );

//���غ��ĸ�����ʾ
add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
 
//���ز��������ʾ
remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$b', "return null;" ) );
 
//�������������ʾ
remove_action( 'load-update-core.php', 'wp_update_themes' );
add_filter( 'pre_site_transient_update_themes', create_function( '$c', "return null;" ) );

//��������������ʾ
add_action(��admin_menu��,��wp_hide_nag��);
function wp_hide_nag() {
remove_action( ��admin_notices��, ��update_nag��, 3 );
}

//�Զ����û�����������Ϣ
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

/* ��WordPress��ӡ��c��չ&#65533;_/�տs������&#65533;_ʼ����AREFLY.COM�u���� */
function xcollapse($atts, $content = null){
	extract(shortcode_atts(array("title"=>""),$atts));
	return '<div style="margin: 0.5em 0;">
		<div class="xControl">
			<span class="xTitle">'.$title.'</span> 
			<a href="javascript:void(0)" class="collapseButton xButton">չ��/����</a>
			<div style="clear: both;"></div>
		</div>
		<div class="xContent" style="display: none;">'.$content.'</div>
	</div>';
}
add_shortcode('collapse', 'xcollapse');

//�Զ������µ�һ��ͼƬ����Ϊ��ɫͼ��
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

// �������@����
function ludou_comment_add_at( $comment_text, $comment = '') {
  if( $comment->comment_parent > 0) {
    $comment_text = '@<a href="#comment-' . $comment->comment_parent . '">'.get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text;
  }
  return $comment_text;
}
add_filter( 'comment_text' , 'ludou_comment_add_at', 20, 2);

// �Ƴ�ͷ���������
remove_action( 'wp_head', 'wp_generator' );                                        // WP�汾��Ϣ
remove_action( 'wp_head', 'rsd_link' );                                                // ���߱༭���ӿ�
remove_action( 'wp_head', 'wlwmanifest_link' );                                    // ͬ��
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );    // �������µ�url
remove_action( 'wp_head', 'feed_links', 2 );                                         // ���º�����feed
remove_action( 'wp_head', 'feed_links_extra', 3 );                                // ȥ������feed
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );                   // ������

//�Ƴ�����������
add_action( 'admin_bar_menu', 'cwp_remove_wp_logo_from_admin_bar_new', 25 );
function cwp_remove_wp_logo_from_admin_bar_new( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
}

//�Ƴ��汾��
if(!function_exists('cwp_remove_script_version')){
    function cwp_remove_script_version( $src ){  return remove_query_arg( 'ver', $src ); }
    add_filter( 'script_loader_src', 'cwp_remove_script_version' );
    add_filter( 'style_loader_src', 'cwp_remove_script_version' );
}

//�Ƴ������Դ�С����
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

//�Ƴ��Ǳ�����ҳ��������
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

//ֻ�ں�̨��ʾ����������
if ( !is_admin() ) {  
    add_filter('show_admin_bar', '__return_false'); 
}

//���ùȸ�����
function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );

//�Ż�վ���������ٶ�
add_action( 'wp_enqueue_scripts', 'wpjam_baidu_zz_enqueue_scripts' );
function wpjam_baidu_zz_enqueue_scripts(){
    wp_enqueue_script( 'baidu_zz_push', 'http://push.zhanzhang.baidu.com/push.js');
}
add_action('save_post', 'wpjam_save_post_notify_baidu_zz', 10, 3);
function wpjam_save_post_notify_baidu_zz($post_id, $post, $update){
	if($post->post_status != 'publish') return;

	$baidu_zz_api_url	= 'http://data.zz.baidu.com/urls?site=www.eurovisionchina.com&token=rgeL1Wcev9sHXuGz';
	//�ٶ�վ����̨ר���ύ����
	
	$response	= wp_remote_post($baidu_zz_api_url, array(
		'headers'	=> array('Accept-Encoding'=>'','Content-Type'=>'text/plain'),
		'sslverify'	=> false,
		'blocking'	=> false,
		'body'		=> get_permalink($post_id)
	));
}
