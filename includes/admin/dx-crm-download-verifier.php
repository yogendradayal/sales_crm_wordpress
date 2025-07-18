<?php
/*
 * dx-crm-download-verifier.php
 *
 * Protects CRM-DM uploaded files with login and CRM roles
 * 
 */

require_once('../../../../../wp-load.php');
require_once('../../../../../wp-admin/includes/file.php');

//check if user is logged-in; if not, redirect to login page
is_user_logged_in() ||  auth_redirect();

//check if user has a CRM user role, or is admin
if ( !current_user_can(DX_CRM_CUSTOMER_ROLE) && 
	 !current_user_can(DX_CRM_CUSTOMER_ROLE) && 
	 !current_user_can('manage_options') && 
	 !current_user_can('manage_network_options' ) ) {
		status_header(403);
		die('403 &#8212; You do not have necessary permissions.');
}

//get absolute path to file
$file_to_download = get_home_path() . DX_CRM_DM_UPLOADS_DIRECTORY;
if ( isset($_GET[ 'file' ]) ) {
	$file_to_download .= '/'.$_GET[ 'file' ];
}

//check if file exists
if (!is_file($file_to_download) || !file_exists ($file_to_download) ) {
	status_header(404);
	die('404 &#8212; File not found.');
}

$mime = wp_check_filetype($file_to_download);
if( false === $mime[ 'type' ] && function_exists( 'mime_content_type' ) )
	$mime[ 'type' ] = mime_content_type( $file_to_download );

if( $mime[ 'type' ] )
	$mimetype = $mime[ 'type' ];
else
	$mimetype = 'image/' . substr( $file_to_download, strrpos( $file_to_download, '.' ) + 1 );

header( 'Content-Type: ' . $mimetype ); // always send this
if ( false === strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) )
	header( 'Content-Length: ' . filesize( $file_to_download ) );

$last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file_to_download ) );
$etag = '"' . md5( $last_modified ) . '"';
header( "Last-Modified: $last_modified GMT" );
header( 'ETag: ' . $etag );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );

// Support for Conditional GET
$client_etag = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;

if( ! isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) )
	$_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;

$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
// If string is empty, return 0. If not, attempt to parse into a timestamp
$client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;

// Make a timestamp for our most recent modification...
$modified_timestamp = strtotime($last_modified);

if ( ( $client_last_modified && $client_etag )
	? ( ( $client_modified_timestamp >= $modified_timestamp) && ( $client_etag == $etag ) )
	: ( ( $client_modified_timestamp >= $modified_timestamp) || ( $client_etag == $etag ) )
	) {
	status_header( 304 );
	exit;
}

// If we made it this far, just serve the file
readfile( $file_to_download );