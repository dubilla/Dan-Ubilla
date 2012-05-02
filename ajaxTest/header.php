<?php
if (!isset($_GET['AJAX']))
{
	?>
	<html>
		<head>
			<title>Example Accessible AJAX website</title>
			<script type="text/javascript" src="/javascript/jquery.js"></script>
		</head>
		<body>
			<div id="navigation"><a href="index.php">Home</a> <a href="page1.php">Page 1</a> <a href="page2.php">Page 2</a></div>
			<div id="content-area">
	<?php
}