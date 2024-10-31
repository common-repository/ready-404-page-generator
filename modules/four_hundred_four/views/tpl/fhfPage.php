<?php if($this->displayStandardHeaderFooter) {
	get_header();
} else { ?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width">
			<title><?php wp_title( '|', true, 'right' ); ?></title>
			<link rel="profile" href="http://gmpg.org/xfn/11">
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
			<?php echo $this->metaData?>
			<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
		</head>
		<body>
<?php }?>

<?php if ( function_exists( 'http_response_code' ) )
    http_response_code( 404 );  ?>

<?php echo $this->htmlContent?>

<?php if($this->displayStandardHeaderFooter) {
	get_footer();
} else { ?>
		</body>
	</html>
<?php }?>