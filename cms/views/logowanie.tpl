<!doctype html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="Keywords" content="CMS, Content Management System, System zarządzania treścią, edycja strony www, tworzenie stron www, strony internetowe">
	<meta name="Description" content="CMS - system zarządzania treścią dla Twojej strony internetowej. Tutaj możesz w łatwy sposób, bez znajomości języka HTML edytować treści Twojej strony internetowej. System stworzony przez: Kamil Wyremski - wyremski.pl">
	<meta name="author" content="Kamil Wyremski - wyremski.pl">
	<title>{$title|default:'CMS created by Kamil Wyremski'}</title>
	
	<link href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Oregano&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="views/css/global.css">
	<link rel="stylesheet" type="text/css" href="views/css/style.css">
	<link rel="shortcut icon" href="images/favicon.ico"/>
	
	<script type="text/javascript" src="js/whcookies.js"></script>
</head>
<body>
<div id="okno_logowania" class="center">	
	<a href="http://wyremski.pl" title="Tworzenie stron internetowych" target="_blank"><img src="images/cms.png" alt="Logo"/></a>
	<form method="post" action="">
		<input type="hidden" name="logowanie">
		<h2>Panel logowania</h2>
		<input type="text" name="login" placeholder="Login" maxlength="32" required title="Tu wpisz login"><br>
		<input type="password" name="haslo" placeholder="Hasło" maxlength="32" required title="Tu wpisz hasło"><br>
		<input type="submit" value="LOGUJ"/>
		<h3>{if isset($komunikat)}{$komunikat}{/if}</h3>
	</form>
</div>
</body>
</html>
