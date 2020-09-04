<?php

/*
|--------------------------------------------------------------------------
| Application Constants
|--------------------------------------------------------------------------
|
| 
|
*/

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 6);

if(!defined('NO')) define('NO', 0);
if(!defined('YES')) define('YES', 1);

if(!defined('PAID')) define('PAID',1);
if(!defined('UNPAID')) define('UNPAID', 0);

if(!defined('DEVICE_ANDROID')) define('DEVICE_ANDROID', 'android');
if(!defined('DEVICE_IOS')) define('DEVICE_IOS', 'ios');
if(!defined('DEVICE_WEB')) define('DEVICE_WEB', 'web');

if(!defined('APPROVED')) define('APPROVED', 1);
if(!defined('DECLINED')) define('DECLINED', 0);

if(!defined('DEFAULT_TRUE')) define('DEFAULT_TRUE', true);
if(!defined('DEFAULT_FALSE')) define('DEFAULT_FALSE', false);

if(!defined('ADMIN')) define('ADMIN', 'admin');
if(!defined('USER')) define('USER', 'user');
if(!defined('ContentCreator')) define('ContentCreator', 'creator');

if(!defined('COD')) define('COD',   'COD');
if(!defined('PAYPAL')) define('PAYPAL', 'PAYPAL');
if(!defined('CARD')) define('CARD',  'CARD');

if(!defined('STRIPE_MODE_LIVE')) define('STRIPE_MODE_LIVE',  'live');
if(!defined('STRIPE_MODE_SANDBOX')) define('STRIPE_MODE_SANDBOX',  'sandbox');

//////// USERS

if(!defined('USER_PENDING')) define('USER_PENDING', 0);

if(!defined('USER_APPROVED')) define('USER_APPROVED', 1);

if(!defined('USER_DECLINED')) define('USER_DECLINED', 2);

if(!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED', 0);

if(!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED', 1);

if(!defined('STARDOM_PENDING')) define('STARDOM_PENDING', 0);

if(!defined('STARDOM_APPROVED')) define('STARDOM_APPROVED', 1);

if(!defined('STARDOM_DECLINED')) define('STARDOM_DECLINED', 2);

if(!defined('STARDOM_EMAIL_NOT_VERIFIED')) define('STARDOM_EMAIL_NOT_VERIFIED', 0);

if(!defined('STARDOM_EMAIL_VERIFIED')) define('STARDOM_EMAIL_VERIFIED', 1);

//////// USERS END

/***** ADMIN CONTROLS KEYS ********/

if(!defined('ADMIN_CONTROL_ENABLED')) define('ADMIN_CONTROL_ENABLED', 1);
if(!defined('ADMIN_CONTROL_DISABLED')) define('ADMIN_CONTROL_DISABLED', 0);

if(!defined('NO_DEVICE_TOKEN')) define("NO_DEVICE_TOKEN", "NO_DEVICE_TOKEN");

if(!defined('PLAN_TYPE_MONTH')) define('PLAN_TYPE_MONTH', 'months');
if(!defined('PLAN_TYPE_YEAR')) define('PLAN_TYPE_YEAR', 'years');

if(!defined('PLAN_TYPE_WEEK')) define('PLAN_TYPE_WEEK', 'weeks');

if(!defined('PLAN_TYPE_DAY')) define('PLAN_TYPE_DAY', 'days');

if(!defined('TODAY')) define('TODAY', 'today');

if(!defined('COMPLETED')) define('COMPLETED',3);

if(!defined('SORT_BY_APPROVED')) define('SORT_BY_APPROVED',1);

if(!defined('SORT_BY_DECLINED')) define('SORT_BY_DECLINED',2);

if(!defined('SORT_BY_EMAIL_VERIFIED')) define('SORT_BY_EMAIL_VERIFIED',3);

if(!defined('SORT_BY_EMAIL_NOT_VERIFIED')) define('SORT_BY_EMAIL_NOT_VERIFIED',4);

if(!defined('STATIC_PAGE_SECTION_1')) define('STATIC_PAGE_SECTION_1', 1);

if(!defined('STATIC_PAGE_SECTION_2')) define('STATIC_PAGE_SECTION_2', 2);

if(!defined('STATIC_PAGE_SECTION_3')) define('STATIC_PAGE_SECTION_3', 3);

if(!defined('STATIC_PAGE_SECTION_4')) define('STATIC_PAGE_SECTION_4', 4);

if(!defined('STARDOM')) define('STARDOM', 'stardom');

if(!defined('USER'))  define('USER', 'user');

if(!defined('STARDOM_DOCUMENT_VERIFIED')) define('STARDOM_DOCUMENT_VERIFIED',1);

if(!defined('STARDOM_DOCUMENT_NOT_VERIFIED')) define('STARDOM_DOCUMENT_NOT_VERIFIED',0);

if(!defined('FREE')) define('FREE', 3);

if(!defined('FREE_POST')) define('FREE_POST',0);

if(!defined('PAID_POST')) define('PAID_POST',1);

if(!defined('SORT_BY_FREE_POST')) define('SORT_BY_FREE_POST',5);

if(!defined('SORT_BY_PAID_POST')) define('SORT_BY_PAID_POST',6);


if(!defined('SORT_BY_ORDER_PLACED')) define('SORT_BY_ORDER_PLACED',1);

if(!defined('SORT_BY_ORDER_SHIPPED')) define('SORT_BY_ORDER_SHIPPED',2);

if(!defined('SORT_BY_ORDER_DELIVERD')) define('SORT_BY_ORDER_DELIVERD',3);

if(!defined('SORT_BY_ORDER_CANCELLED')) define('SORT_BY_ORDER_CANCELLED',4);

if(!defined('ORDER_PLACED')) define('ORDER_PLACED',1);

if(!defined('ORDER_SHIPPED')) define('ORDER_SHIPPED',2);

if(!defined('ORDER_DELIVERD')) define('ORDER_DELIVERD',3);

if(!defined('ORDER_CACELLED')) define('ORDER_CACELLED',4);

if(!defined('PAYMENT_OFFLINE')) define('PAYMENT_OFFLINE','offline_payment');

if(!defined('WITHDRAW_INITIATED')) define('WITHDRAW_INITIATED', 0);

if(!defined('WITHDRAW_PAID')) define('WITHDRAW_PAID', 1);

if(!defined('WITHDRAW_ONHOLD')) define('WITHDRAW_ONHOLD', 2);

if(!defined('WITHDRAW_REJECTED')) define('WITHDRAW_REJECTED', 3);

if(!defined('WITHDRAW_CANCELLED')) define('WITHDRAW_CANCELLED', 4);
