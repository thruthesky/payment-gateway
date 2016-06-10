<?php
/**
 * @file function.php
 * @desc 기본 함수 라이브러리
 */

/**
 *
 * 각 CMS 의 회원 번호를 리턴한다.
 *
 * @return int - 회원번호
 *
 */
function payment_get_user_id() {
    switch ( PAYMENT_CMS ) {
        case 'wordpress'            : return wp_get_current_user()->ID;
        default                     : return 0;
    }
}

/**
 * 결제 폼 변수 입력 값 체크
 *
 * @return int - 성공이면 0 을 리턴.
 */
function payment_check_input() {

// min
    if ( ! isset($_REQUEST['min']) || empty($_REQUEST['min']) ) {
        jsAlert("수업 분을 선택하십시오.");
        return -1;
    }
    $min = $_REQUEST['min'];
    $GLOBALS['payment']['min'] = $min;

    $amt = 0;
    if ( $min == '25' ) $amt = 120000;
    else if ( $min == '50' ) $amt = 240000;
    if ( $amt == 0 ) {
        jsAlert("수업료를 선택하십시오.");
        return -1;
    }

    $GLOBALS['payment']['amt'] = $amt;

// payment method
    if ( ! isset( $_REQUEST['method'] ) || empty($_REQUEST['method']) ) {
        jsAlert("결재 방식을 선택하십시오.");
        return -2;
    }
    $GLOBALS['payment']['method'] = $_REQUEST['method'];


    $GLOBALS['payment']['values_from_user_form'] = serialize( $_REQUEST );
    return 0;
}

/**
 * 사용자 결제 정보를 데이터베이스 저장한다.
 *
 * @note 저장하고 테이블의 ID 값을 $GLOBALS[payment][ID] 에 저장한다.
 *
 * @return int - 성공이면 0, 에러이면 참.
 */
function payment_insert_info() {
    global $payment;
    if ( PAYMENT_CMS == 'wordpress' ) {

        global $wpdb;
        $table = $wpdb->prefix . 'payment';

// insert db
        $q = "INSERT INTO $table (user_id, method, amount, stamp_create, values_from_user_form) VALUES ( %d, %s, %d, %d, %s )";
        $re = $wpdb->query( $wpdb->prepare(
            $q, payment_get_user_id(), $payment['method'], $payment['amt'], time(), $payment['values_from_user_form'] ) );

        if ( $re === false ) {
            jsAlert("Error on inserting payment information");
            return -4002;
        }
        $payment['ID'] = $wpdb->insert_id;
        return 0;
    }
    return -1;
}

/**
 * 결제 로그를 기록한다.
 * @return int - 성공이면 0. 실패면 참.
 */
function payment_insert_log() {
    return 0;
}

function create_table_payment( $table_name ) {

    $sql = <<<EOS
CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `paygate` varchar(32) NOT NULL DEFAULT '',
  `paygate_account` varchar(64) NOT NULL DEFAULT '',
  `session_id` varchar(255) NOT NULL DEFAULT '',
  `method` varchar(64) NOT NULL DEFAULT '',
  `currency` varchar(16) DEFAULT '',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `stamp_create` int(10) unsigned NOT NULL DEFAULT '0',
  `stamp_finish` int(10) unsigned NOT NULL DEFAULT '0',
  `result` char(1) NOT NULL DEFAULT '',
  `values_from_user_form` LONGTEXT,
  `values_from_paygate_server` LONGTEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  KEY `paygate_account` (`paygate_account`),
  KEY `result` (`result`),
  KEY `paygate` (`paygate`),
  KEY `method` (`method`),
  KEY `stamp_create` (`stamp_create`),
  KEY `stamp_finish` (`stamp_finish`)
)

EOS;
    return $sql;
}

function create_table_payment_log( $table_name ) {
    $sql = <<<EOS
CREATE TABLE $table_name (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    payment_id INT UNSIGNED NOT NULL DEFAULT 0,
    message LONGTEXT,
    stamp_create INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY  (id)
);
EOS;
    return $sql;
}
