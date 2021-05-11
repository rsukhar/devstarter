<?php
/**
 * This is a DEVELOPMENT version of security config and it must be overwritten
 * in a production version
 *
 * TODO Move this to database-stored settings
 */
return array(
	// Encryption method (a one from openssl_get_cipher_methods() list)
	'method' => 'aes-256-cbc',
	// A random 256-bit (or other method-defined length) encryption key. Can be obtained by:
	// bin2hex(openssl_random_pseudo_bytes(32))
	'encryption_key' => '5f2f4903b58092e381d0da7ad887a481fc145b5148411f2933a0353add71c39e',
);