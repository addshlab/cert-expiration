<?php
/*
Plugin Name: Cert Expiration Widget
Plugin URI: 
Description: .
Author: trade.add.sh
Version: 0.1
*/

new CertExpirationWidget();

class CertExpirationWidget extends WP_Widget {

    //コンストラクタでウィジェットを登録
    function __construct(){
        add_action( 'widgets_init', array( $this, 'register_cert_expiration_widget' ) );
        parent::__construct(
            'cert_expiration_widget', //ウィジェットID
            'Cert Expiration Widget', //ウィジェット名
            array('description' => 'Display https certificate expiration date and other information.')    //ウィジェットの概要
        );

        add_shortcode( 'cert_expiration', array( $this, 'get_cert_info') );
    }
    
    //ウィジェットの表示
    public function widget($args, $instance){
        echo $args['before_widget'];
        
        echo $args['before_title'] . $args['after_title'];
        echo $this->get_cert_info();
        
        echo $args['after_widget'];
    }

    public function register_cert_expiration_widget() {
        register_widget( 'CertExpirationWidget' );
    }

    public function get_cert_info() {

    $cert_info = get_transient( 'cert_info' );

    if( false == $cert_info ) :
        ob_start();

        $domain_name = str_replace( 'https://', '', get_home_url() );
        $stream_context = stream_context_create( array(
            'ssl' => array( 'capture_peer_cert' => true )
        ) );

        $resource = stream_socket_client(
            'ssl://' . $domain_name . ':443',$errno,$errstr,5,STREAM_CLIENT_CONNECT,$stream_context
        );

        $cont = stream_context_get_params($resource);

        $parsed = openssl_x509_parse($cont['options']['ssl']['peer_certificate']);

        if(strpos($parsed['subject']['CN'], $domain_name) !== false){
            $output .= '<div style="border-left:2px solid #46b450;margin-left:10px;padding-left:10px;color:#999;">';
            $output .= '<span style="color:#46b450;"><span class="dashicons dashicons-lock"></span>このサイトの接続はHTTPS通信で保護されています。</span><br />';
            $output .= 'ドメイン名:  ' . $parsed['subject']['CN'] . '<br />';
            $output .= '有効期限: ' . date('Y/m/d', $parsed['validTo_time_t']) . '<br />';
            $output .= '発行者: ' . $parsed['issuer']['CN'] . '<br />';
            $output .= '署名アルゴリズム: ' . $parsed['signatureTypeSN'] . '<br />';
            $output .= '<span style="color:#555;">最終確認: ' . esc_html( wp_date( 'Y-m-d H:i:s P', null, new DateTimeZone( get_option( 'timezone_string' ) ) ) ) . '</span>';
            $output .= '</div>';
        } else {
            $output = '有効なサーバー証明書の取得ができませんでした。'; 
        }

        echo $output;

        $cert_info = ob_get_clean();
        set_transient( 'cert_info', $cert_info, 60*60*6 ); // 6hごとに確認
    endif;

    return  $cert_info;
   }
}

