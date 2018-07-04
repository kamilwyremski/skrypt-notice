<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="Keywords" content="{$ustawienia.keywords}">
	<meta name="Description" content="{$ustawienia.description}">
	<title>{$ustawienia.title}</title>
	<base href="{$ustawienia.base_url}/">
	
	<!-- style CSS -->
	<link href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="views/{$ustawienia.szablon}/js/owl-carousel/owl.carousel.css">
	<link rel="stylesheet" href="views/{$ustawienia.szablon}/css/style.css">
	<link rel="stylesheet" href="views/{$ustawienia.szablon}/css/menu.css">
	<link rel="stylesheet" href="views/{$ustawienia.szablon}/css/print.css" media="print">
	<link rel="stylesheet" href="views/{$ustawienia.szablon}/css/mobile.css">
	{if $ustawienia.favicon!=''}<link rel="shortcut icon" href="{$ustawienia.favicon}">{/if}
	
	<!-- skrypty javascript -->
	<script type="text/javascript" src="views/{$ustawienia.szablon}/js/whcookies.js"></script>
	<script type="text/javascript" src="views/{$ustawienia.szablon}/js/jquery-2.1.4.min.js"></script> 
	<script type="text/javascript" src="views/{$ustawienia.szablon}/js/jquery-ui-custom.min.js"></script>
	<script type="text/javascript" src="views/{$ustawienia.szablon}/js/owl-carousel/owl.carousel.min.js "></script>
	<script type="text/javascript" src="views/{$ustawienia.szablon}/js/engine.js"></script>

	<!-- integracja z Facebook -->
	{if $ustawienia.logo_facebook!=''}<meta property="og:image" content="{$ustawienia.base_url}{$ustawienia.logo_facebook}">{/if}
	<meta property="og:description" content="{$ustawienia.description}">
	<meta property="og:title" content="{$ustawienia.title}">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="{$ustawienia.title}">
	<meta property="og:locale" content="pl_PL">
	{if $ustawienia.facebook_api}<meta property="fb:app_id" content="{$ustawienia.facebook_api}">{/if}
	
	<!-- inne -->
	{if $ustawienia.rss}<link rel="alternate" type="application/rss+xml" href="{$ustawienia.base_url}/php/rss.php{if isset($rss_kategoria)}?kategoria={$rss_kategoria.id}&nazwa={$rss_kategoria.nazwa}{/if}" title="Nasz kanał RSS">{/if}
	{if $ustawienia.kod_css!=''}<style>{$ustawienia.kod_css}</style>{/if}
	{$ustawienia.kod_head}

<body>

	{include file="$strona.html"}
	
<a href="#" title="Wróć na górę strony" id="link_przewin" class="schowany"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/przewin.png" alt="Strzałka w górę"></a>

{$ustawienia.analytics}
{$ustawienia.kod_body}
</body>
</html>
