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

if(!defined('MALE')) define('MALE', 'male');
if(!defined('FEMALE')) define('FEMALE', 'female');
if(!defined('OTHERS')) define('OTHERS', 'others');

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
if(!defined('BANK_TRANSFER')) define('BANK_TRANSFER',  'BANK_TRANSFER');
if(!defined('PAYMENT_OFFLINE')) define('PAYMENT_OFFLINE',  'OFFLINE');
if(!defined('PAYMENT_MODE_WALLET')) define('PAYMENT_MODE_WALLET',  'WALLET');

if(!defined('STRIPE_MODE_LIVE')) define('STRIPE_MODE_LIVE',  'live');
if(!defined('STRIPE_MODE_SANDBOX')) define('STRIPE_MODE_SANDBOX',  'sandbox');

//////// USERS

if(!defined('USER_PENDING')) define('USER_PENDING', 2);

if(!defined('USER_APPROVED')) define('USER_APPROVED', 1);

if(!defined('USER_DECLINED')) define('USER_DECLINED', 0);

if(!defined('USER_EMAIL_NOT_VERIFIED')) define('USER_EMAIL_NOT_VERIFIED', 0);

if(!defined('USER_EMAIL_VERIFIED')) define('USER_EMAIL_VERIFIED', 1);


if(!defined('CONTENT_CREATOR_EMAIL_NOT_VERIFIED')) define('CONTENT_CREATOR_EMAIL_NOT_VERIFIED', 0);

if(!defined('CONTENT_CREATOR_EMAIL_VERIFIED')) define('CONTENT_CREATOR_EMAIL_VERIFIED', 1);

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

if(!defined('SORT_BY_DOCUMENT_VERIFIED')) define('SORT_BY_DOCUMENT_VERIFIED',5);

if(!defined('SORT_BY_DOCUMENT_APPROVED')) define('SORT_BY_DOCUMENT_APPROVED',6);

if(!defined('SORT_BY_DOCUMENT_PENDING')) define('SORT_BY_DOCUMENT_PENDING',7);


if(!defined('STATIC_PAGE_SECTION_1')) define('STATIC_PAGE_SECTION_1', 1);

if(!defined('STATIC_PAGE_SECTION_2')) define('STATIC_PAGE_SECTION_2', 2);

if(!defined('STATIC_PAGE_SECTION_3')) define('STATIC_PAGE_SECTION_3', 3);

if(!defined('STATIC_PAGE_SECTION_4')) define('STATIC_PAGE_SECTION_4', 4);

if(!defined('USER_DOCUMENT_VERIFIED')) define('USER_DOCUMENT_VERIFIED', 1);


if(!defined('STARDOM')) define('STARDOM', 'stardom');

if(!defined('USER'))  define('USER', 'user');

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

if(!defined('WITHDRAW_DECLINED')) define('WITHDRAW_DECLINED', 3);

if(!defined('WITHDRAW_CANCELLED')) define('WITHDRAW_CANCELLED', 4);



if(!defined('USER_WALLET_PAYMENT_INITIALIZE')) define('USER_WALLET_PAYMENT_INITIALIZE', 0);
if(!defined('USER_WALLET_PAYMENT_PAID')) define('USER_WALLET_PAYMENT_PAID', 1);
if(!defined('USER_WALLET_PAYMENT_UNPAID')) define('USER_WALLET_PAYMENT_UNPAID', 2);
if(!defined('USER_WALLET_PAYMENT_CANCELLED')) define('USER_WALLET_PAYMENT_CANCELLED', 3);
if(!defined('USER_WALLET_PAYMENT_DISPUTED')) define('USER_WALLET_PAYMENT_DISPUTED', 4);
if(!defined('USER_WALLET_PAYMENT_WAITING')) define('USER_WALLET_PAYMENT_WAITING', 5);


// amount_type - add and debitedd
if(!defined('WALLET_AMOUNT_TYPE_ADD')) define('WALLET_AMOUNT_TYPE_ADD', 'add');
if(!defined('WALLET_AMOUNT_TYPE_MINUS')) define('WALLET_AMOUNT_TYPE_MINUS', 'minus');

// payment type - specifies the transaction usage
if(!defined('WALLET_PAYMENT_TYPE_ADD')) define('WALLET_PAYMENT_TYPE_ADD', 'add');
if(!defined('WALLET_PAYMENT_TYPE_PAID')) define('WALLET_PAYMENT_TYPE_PAID', 'paid');
if(!defined('WALLET_PAYMENT_TYPE_CREDIT')) define('WALLET_PAYMENT_TYPE_CREDIT', 'credit');
if(!defined('WALLET_PAYMENT_TYPE_WITHDRAWAL')) define('WALLET_PAYMENT_TYPE_WITHDRAWAL', 'withdrawal');

if (!defined('PAID_STATUS')) define('PAID_STATUS', 1);


// Subscribed user status

if(!defined('SUBSCRIBED_USER')) define('SUBSCRIBED_USER', 1);

if(!defined('NON_SUBSCRIBED_USER')) define('NON_SUBSCRIBED_USER', 0);

if(!defined('TAKE_COUNT')) define('TAKE_COUNT', 12);

if(!defined('SHOW')) define('SHOW', 1);

if(!defined('HIDE')) define('HIDE', 0);

if(!defined('READ')) define('READ', 1);

if(!defined('UNREAD')) define('UNREAD', 0);

// AUTORENEWAL STATUS

if(!defined('AUTORENEWAL_ENABLED')) define('AUTORENEWAL_ENABLED',0);

if(!defined('AUTORENEWAL_CANCELLED')) define('AUTORENEWAL_CANCELLED',1);

if(!defined('PRODUCT_AVAILABLE')) define('PRODUCT_AVAILABLE',1);

if(!defined('PRODUCT_NOT_AVAILABLE')) define('PRODUCT_NOT_AVAILABLE',0);

if(!defined('PUBLISHED')) define('PUBLISHED',1);

if(!defined('UNPUBLISHED')) define('UNPUBLISHED', 0);


if(!defined('USER_DOCUMENT_NONE')) define('USER_DOCUMENT_NONE', 0);
if(!defined('USER_DOCUMENT_PENDING')) define('USER_DOCUMENT_PENDING', 1);
if(!defined('USER_DOCUMENT_APPROVED')) define('USER_DOCUMENT_APPROVED', 2);
if(!defined('USER_DOCUMENT_DECLINED')) define('USER_DOCUMENT_DECLINED', 3);

if(!defined('USER_FREE_ACCOUNT')) define('USER_FREE_ACCOUNT', 0);
if(!defined('USER_PREMIUM_ACCOUNT')) define('USER_PREMIUM_ACCOUNT', 1);

if(!defined('USER_SUBSCRIPTION_MONTHLY')) define('USER_SUBSCRIPTION_MONTHLY', 'monthly');
if(!defined('USER_SUBSCRIPTION_YEARLY')) define('USER_SUBSCRIPTION_YEARLY', 'yearly');

if(!defined('BOOKMARK_TYPE_ALL')) define('BOOKMARK_TYPE_ALL', 'all');
if(!defined('BOOKMARK_TYPE_PHOTOS')) define('BOOKMARK_TYPE_PHOTOS', 'photos');
if(!defined('BOOKMARK_TYPE_VIDEOS')) define('BOOKMARK_TYPE_VIDEOS', 'videos');
if(!defined('BOOKMARK_TYPE_AUDIOS')) define('BOOKMARK_TYPE_AUDIOS', 'audios');
if(!defined('BOOKMARK_TYPE_LOCKED')) define('BOOKMARK_TYPE_LOCKED', 'locked');
if(!defined('BOOKMARK_TYPE_OTHERS')) define('BOOKMARK_TYPE_OTHERS', 'others');

// Bell notification status

if(!defined('BELL_NOTIFICATION_STATUS_UNREAD')) define('BELL_NOTIFICATION_STATUS_UNREAD', 1);

if(!defined('BELL_NOTIFICATION_STATUS_READ')) define('BELL_NOTIFICATION_STATUS_READ', 2);

if(!defined('POSTS_ALL')) define('POSTS_ALL', 'all');
if(!defined('POSTS_IMAGE')) define('POSTS_IMAGE', 'image');
if(!defined('POSTS_VIDEO')) define('POSTS_VIDEO', 'video');
if(!defined('POSTS_AUDIO')) define('POSTS_AUDIO', 'audio');
if(!defined('POSTS_TEXT')) define('POSTS_TEXT', 'text');
if(!defined('POSTS_LOCKED')) define('POSTS_LOCKED', 'locked');

if(!defined('POSTS_PAYMENT_SUBSCRIPTION')) define('POSTS_PAYMENT_SUBSCRIPTION', 'subscription');

if(!defined('POSTS_PAYMENT_PPV')) define('POSTS_PAYMENT_PPV', 'ppv');


if(!defined('FOLLOWER_ACTIVE')) define('FOLLOWER_ACTIVE', 1);

if(!defined('FOLLOWER_EXPIRED')) define('FOLLOWER_EXPIRED', 0);

if(!defined('BELL_NOTIFICATION_TYPE_FOLLOW')) define('BELL_NOTIFICATION_TYPE_FOLLOW', 'follow');
if(!defined('BELL_NOTIFICATION_TYPE_NEW_POST')) define('BELL_NOTIFICATION_TYPE_NEW_POST', 'new-post');
if(!defined('BELL_NOTIFICATION_TYPE_LIKE')) define('BELL_NOTIFICATION_TYPE_LIKE', 'like');
if(!defined('BELL_NOTIFICATION_TYPE_DISLIKE')) define('BELL_NOTIFICATION_TYPE_DISLIKE', 'dislike');
if(!defined('BELL_NOTIFICATION_TYPE_POST_COMMENT')) define('BELL_NOTIFICATION_TYPE_POST_COMMENT', 'comment');
if(!defined('BELL_NOTIFICATION_TYPE_SUBSCRIPTION')) define('BELL_NOTIFICATION_TYPE_SUBSCRIPTION', 'subscription');
if(!defined('BELL_NOTIFICATION_TYPE_SEND_TIP')) define('BELL_NOTIFICATION_TYPE_SEND_TIP', 'tips');

if(!defined('PRODUCTION')) define('PRODUCTION', 'production');
if(!defined('SANDBOX')) define('SANDBOX', 'sandbox');

if(!defined('STORAGE_TYPE_S3')) define('STORAGE_TYPE_S3', 1);
if(!defined('STORAGE_TYPE_LOCAL')) define('STORAGE_TYPE_LOCAL', 0);