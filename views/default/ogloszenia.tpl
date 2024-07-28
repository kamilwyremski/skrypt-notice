
{foreach from=$ogloszenia item=item key=key}
	<div class="oferta{if $item.promowana} promowana{/if}" itemscope itemtype="http://schema.org/Product">
          	<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	          <meta itemprop="priceCurrency" content="PLN"></meta>
	          <meta itemprop="price" content="{$item.cena}"></meta>
	          <meta itemprop="availability" content="http://schema.org/InStock"></meta>
	          <meta itemprop="description" content="{$item.opis|truncate:250}"></meta>
	          <meta itemprop="url" content="{$ustawienia.base_url}/{$item.id},{$item.prosty_tytul}" title="{$item.tytul}"></meta>               
		</div>
	        <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
	          <meta itemprop="ratingValue" content="5"></meta>
	          <meta itemprop="bestRating" content="5"></meta>
	          <meta itemprop="ratingCount" content="1"></meta>
	        </div>

		<div class="oferta_opis inline_block">
			<a href="{$item.id},{$item.prosty_tytul}" title="Ogłoszenie: {$item.tytul}"><img src="{if $item.miniaturka!=''}{$ustawienia.upload}/{$item.miniaturka}{else}{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png{/if}" onerror="this.src='{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png'" alt="{$item.tytul}" itemprop="image"></a>
		</div>
		<div class="oferta_opis2 inline_block">
			<h3 class="break_word"><a href="{$item.id},{$item.prosty_tytul}" title="Ogłoszenie: {$item.tytul}" itemprop="url"><span itemprop="name">{$item.tytul}</span></a></h3>
			{if $item.za_darmo}
				<p class="cena"><span style="padding-left:0px">Za darmo</span></p>
			{else}
				{if $item.cena!='0.00'}<p class="cena">Cena: <span>{$item.cena|number_format:2:",":" "}&nbsp;{$ustawienia.waluta}</span></p>{/if}
				{if $item.cena_do_negocjacji}<p class="cena_do_negocjacji">Cena do negocjacji</p>{/if}
			{/if}
			{if $strona=='index' or $strona=='schowek'}
				<br>
				<div>
					{if $item.telefon!=''}<a href="tel:{$item.telefon|replace:' ':''}" title="Zadzwoń do: {$item.telefon}" class="boxy_kontakt inline_block"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_telefon.png" alt="Telefon">{if $ustawienia.ukrywaj_telefon}<span class="pokaz_ukryte_dane" data-dane="{$item.telefon}">pokaż telefon...</span>{else}{$item.telefon}{/if}</a>{/if}
					<a href="mailto:{$item.email}" title="Napisz do: {$item.email}" class="boxy_kontakt inline_block"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_email.png" alt="Email">{if $ustawienia.ukrywaj_email}<span class="pokaz_ukryte_dane" data-dane="{$item.email}">pokaż email...</span>{else}{$item.email}{/if}</a>
					{if $strona=='schowek'}
						<form action="" method="post" class="inline_block">
							<input type="hidden" name="akcja" value="usun_schowek">
							<input type="hidden" name="id" value="{$item.id}">
							<input type="submit" value="Usuń ze schowka" style="margin:0">
						</form>
					{/if}
				</div>
			{elseif $strona=='moje_ogloszenia'}
				<br>
				<p>
					<div class="div_odswiez center">
						{if $item.odswiez.aktywne}
							<a href="#" class="submit ajax_confirm" title="Odśwież ogłoszenie" data-akcja="odswiez" data-id="{$item.id}" data-title='Czy na pewno chcesz odświeżyć ogłoszenie: "{$item.tytul}"?'>Odśwież</a>
						{elseif !$item.oplacona}
							<a href="#" class="submit nieaktywny" title="Odśwież ogłoszenie">Odśwież</a>
							<p>Nieopłacone</p>
						{else}
							<a href="#" class="submit nieaktywny" title="Odśwież ogłoszenie">Odśwież</a>
							<p>Dostępne za {$item.odswiez.dni} dni</p>
						{/if}
					</div>
					<a href="edytuj,{$item.id},{$item.prosty_tytul}" class="submit" title="Edytuj ogłoszenie">Edytuj</a>
					<a href="dodaj,{$item.id}" class="submit" title="Dodaj podobne ogłoszenie">Dodaj podobne</a>
					{if $item.aktywna}
						<a href="#" class="submit ajax_confirm" title="Zakończ ogłoszenie" data-akcja="zakoncz" data-id="{$item.id}" data-title='Czy na pewno chcesz zakończyć wyświetlanie ogłoszenia: "{$item.tytul}"?'>Zakończ</a>
					{/if}
					<a href="#" class="submit ajax_confirm" title="Usuń ogłoszenie" data-akcja="usun" data-id="{$item.id}" data-title='Czy na pewno chcesz usunąć ogłoszenie: "{$item.tytul}"?'>Usuń</a>
				</p>
			{/if}
		</div>
		<div class="oferta_opis3 inline_block right">
			{if $strona=='moje_ogloszenia'}
				<p>{if $item.aktywna==1}<b>Aktywne</b>{else}<span class="red">Nieaktywne</span>{/if}</p>
				<p>{if $item.oplacona==1}<b>Opłacone</b>{else}<p><span class="red">Nieopłacone</span>{/if}</p>
				<hr style="height:2px; visibility:hidden;" />
			{/if}
			{if $item.typ_nazwa!=''}<p><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_typ.png" alt="Typ">{$item.typ_nazwa}</p>{/if}
			{if $item.kategoria_nazwa!=''}<p><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_folder.png" alt="Kategoria"><a href="kategoria,{$item.kategoria},{$item.kategoria_prosta_nazwa}" title="Kategoria: {$item.kategoria_nazwa}"><span itemprop="category">{$item.kategoria_nazwa}</span></a></p>{/if}
			{if $strona=='index' && $item.login!=''}<p><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_autor.png" alt="Autor"><a href="profil,{$item.login}" title="Wystawił: {$item.login}">{$item.login}</a></p>{/if}
			<p><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_kalendarz.png" alt="Kalendarz">{$item.start|date_format:"%d-%m-%Y"}</p>
			{if isset($item.ile)}<p><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/ikona_oko.png" alt="oko">{$item.ile}</p>{/if}
		</div>
	</div>
{/foreach}
