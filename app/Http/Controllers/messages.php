<?php

    $MAGFA_errors = array();

    $MAGFA_errors[1]['title'] = 'INVALID_RECIPIENT_NUMBER';
    $MAGFA_errors[1]['desc'] = 'the string you presented as recipient numbers are not valid phone numbers, please check them again';

    $MAGFA_errors[2]['title'] = 'INVALID_SENDER_NUMBER';
    $MAGFA_errors[2]['desc'] = 'the string you presented as sender numbers(3000-xxx) are not valid numbers, please check them again';

    $MAGFA_errors[3]['title'] = 'INVALID_ENCODING';
    $MAGFA_errors[3]['desc'] = 'are You sure You\'ve entered the right encoding for this message? You can try other encodings to bypass this error code';

    $MAGFA_errors[4]['title'] = 'INVALID_MESSAGE_CLASS';
    $MAGFA_errors[4]['desc'] = 'entered MessageClass is not valid. for a normal MClass, leave this entry empty';

    $MAGFA_errors[6]['title'] = 'INVALID_UDH';
    $MAGFA_errors[6]['desc'] = 'entered UDH is invalid. in order to send a simple message, leave this entry empty';

    $MAGFA_errors[12]['title'] = 'INVALID_ACCOUNT_ID';
    $MAGFA_errors[12]['desc'] = 'you\'re trying to use a service from another account??? check your UN/Password/NumberRange again';

    $MAGFA_errors[13]['title'] = 'NULL_MESSAGE';
    $MAGFA_errors[13]['desc'] = 'check the text of your message. it seems to be null';

    $MAGFA_errors[14]['title'] = 'CREDIT_NOT_ENOUGH';
    $MAGFA_errors[14]['desc'] = 'Your credit\'s not enough to send this message. you might want to buy some credit.call';

    $MAGFA_errors[15]['title'] = 'SERVER_ERROR';
    $MAGFA_errors[15]['desc'] = 'something bad happened on server side, you might want to call MAGFA Support about this:';

    $MAGFA_errors[16]['title'] = 'ACCOUNT_INACTIVE';
    $MAGFA_errors[16]['desc'] = 'Your account is not active right now, call -- to activate it';

    $MAGFA_errors[17]['title'] = 'ACCOUNT_EXPIRED';
    $MAGFA_errors[17]['desc'] = 'looks like Your account\'s reached its expiration time, call -- for more information';

    $MAGFA_errors[18]['title'] = 'INVALID_USERNAME_PASSWORD_DOMAIN'; // todo : note : one of them are empty
    $MAGFA_errors[18]['desc'] = 'the combination of entered Username/Password/Domain is not valid. check\'em again';

    $MAGFA_errors[19]['title'] = 'AUTHENTICATION_FAILED'; // todo : note : wrong arguments supplied ...
    $MAGFA_errors[19]['desc'] = 'You\'re not entering the correct combination of Username/Password';

    $MAGFA_errors[20]['title'] = 'SERVICE_TYPE_NOT_FOUND';
    $MAGFA_errors[20]['desc'] = 'check the service type you\'re requesting. we don\'t get what service you want to use. your sender number might be wrong, too.';

    $MAGFA_errors[22]['title'] = 'ACCOUNT_SERVICE_NOT_FOUND';
    $MAGFA_errors[22]['desc'] = 'your current number range doesn\'t have the permission to use Webservices';

    $MAGFA_errors[23]['title'] = 'SERVER_BUSY';
    $MAGFA_errors[23]['desc'] = 'Sorry, Server\'s under heavy traffic pressure, try testing another time please';

    $MAGFA_errors[24]['title'] = 'INVALID_MESSAGE_ID';
    $MAGFA_errors[24]['desc'] = 'entered message-id seems to be invalid, are you sure You entered the right thing?';

    $MAGFA_errors[102]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_CLASS_ARRAY';
    $MAGFA_errors[102]['desc'] = 'this happens when you try to define MClasses for your messages. in this case you must define one recipient number for each MClass';

    $MAGFA_errors[103]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_SENDER_NUMBER_ARRAY';
    $MAGFA_errors[103]['desc'] = 'This error happens when you have more than one sender-number for message. when you have more than one sender number, for each sender-number you must define a recipient number...';

    $MAGFA_errors[104]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_ARRAY';
    $MAGFA_errors[104]['desc'] = 'this happens when you try to define UDHs for your messages. in this case you must define one recipient number for each udh';

    $MAGFA_errors[106]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_IS_NULL';
    $MAGFA_errors[106]['desc'] = 'array of recipient numbers must have at least one member';

    $MAGFA_errors[107]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_TOO_LONG';
    $MAGFA_errors[107]['desc'] = 'the maximum number of recipients per message is 90';

    $MAGFA_errors[108]['title'] = 'WEB_SENDER_NUMBER_ARRAY_IS_NULL';
    $MAGFA_errors[108]['desc'] = 'array of sender numbers must have at least one member';

    $MAGFA_errors[109]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_ENCODING_ARRAY';
    $MAGFA_errors[109]['desc'] = 'this happens when you try to define encodings for your messages. in this case you must define one recipient number for each Encoding';

    $MAGFA_errors[110]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_CHECKING_MESSAGE_IDS__ARRAY';
    $MAGFA_errors[110]['desc'] = 'this happens when you try to define checking-message-ids for your messages. in this case you must define one recipient number for each checking-message-id';

    $MAGFA_errors[-1]['title'] = 'NOT_AVAILABLE';
    $MAGFA_errors[-1]['desc'] = 'The target of report is not available(e.g. no message is associated with entered IDs)';
//----------------------------------------------------------------------------
$message_status = array();

$message_status[0]['title'] = 'Unknown';
$message_status[1]['title'] = 'Mobile Delivered';
$message_status[2]['title'] = 'Mobile Failed';
$message_status[8]['title'] = 'SMS Center Delivered';
$message_status[16]['title'] ='SMS Center Failed';
//----------------------------------------------------------------------------
$METHOD_errors = array();
$METHOD_errors[0]['title'] = 'Exception';
$METHOD_errors[14]['title'] = 'INVALID_USER_CREDIT';
$METHOD_errors[18]['title'] = 'INVALID_USERNAME_PASSWORD';
$METHOD_errors[20]['title'] = 'INVALID_SENDID';
$METHOD_errors[100]['title'] = 'INVALID_ARRAY_ENCODING_LENGTH';
$METHOD_errors[101]['title'] = 'INVALID_ARRAY_MESSAGE_LENGTH';
$METHOD_errors[102]['title'] = 'INVALID_ARRAY_MCLASS_LENGTH';
$METHOD_errors[103]['title'] = 'INVALID_ARRAY_MOBILES_LENGTH';
$METHOD_errors[104]['title'] = 'INVALID_ARRAY_UDH_LENGTH';
$METHOD_errors[1000]['title'] = 'INVALID_READY_FOR_SEND';
?>
