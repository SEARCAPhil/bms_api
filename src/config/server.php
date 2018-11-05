<?php
# Server ABSOLUTE PATH
const SERVER_SCHEME = 'http';
const SERVER_ROOT = '/bms_api';
const SERVER_DOMAIN_NAME = 'localhost/'.SERVER_ROOT; 
const SERVER_URL = SERVER_SCHEME.'://'.SERVER_DOMAIN_NAME.'/';
# SMTP Mail Credential
const SMTP_SENDER = 'no-reply';
const SMTP_USERNAME = 'no-reply@mail.local';
const SMTP_PASSWORD = 'hello@1234';

# UPLOAD DIR
const UPLOAD_DIR = SERVER_URL.'public/uploads/';
# absolute pathj
define('UPLOAD_ABSOLUTE_PATH', ($_SERVER["DOCUMENT_ROOT"])."/".SERVER_ROOT.'/public/uploads/'); 
?>

