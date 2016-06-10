<?php
/**
 *
 * @file payment-gateway/config.php
 * @desc
 *      AllTheGate 와 PayPal 등 모든 결제 모듈의 저보를 여기서 설정한다.
 */

/**
 * --------------------------------------------------------------------------
 *
 * 올더게이트 설정
 *
 * --------------------------------------------------------------------------
 */



/** ====== 필수 ===== */
define( 'PAYMENT_CMS', 'wordpress' );                               // CMS 를 기록한다. 2016년 현재, 'wordpress' 만 지원.
define( 'PAYMENT_GATEWAY_PATH', __LMS_PATH__ . 'payment-gateway/allthegate/' ); // 필수 : 각 CMS 에 맞게 수정.
$StoreId = "aegis";                                                 // 상점 아이디. 테스트 아이디는 "aegis"


/** ====== 옵션 ===== */
define( 'PAYMENT_DEBUG', true );                                    // 옵션 : 디버깅
define( 'PAYMENT_LOG', true );                                      // 결제 연동 간편 메뉴얼 참고. AGS_pay_ing.php 참고.
define( 'PAYMENT_LOG_PATH', PAYMENT_GATEWAY_PATH . 'log');          // 이 폴더에 반드시 쓰기 퍼미션이 있어야 한다.
define( 'PAYMENT_LOG_LEVEL', 'DEBUG');                              // DEBUG, INFO 등을 기록.



/** ===== 기타 ===== */

$GLOBALS['payment'] = array();                                     // 모든 처리 값을 이 변수 하나에 담는다. 각 함수에서 이 변수를 공유한다.
