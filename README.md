# Ad-Adder-sample-project
Sample project showing my current knowledge.

Main folder vsebuje tudi mapo dropzone, za uporabo dropzone.js knjižnjice,
mapo getid3, knjižnjico za analizo videoposnetkov v PHP,
mapo vendor za Slim 4 framework,
in mapo materiali, ki simulira disk na serverju, kamor se naložene datoteke dejansko shranijo.

Uporabil sem Bootstrap 4 za front-end oblikovanje, PHP s Slim 4 za API.

Namen aplikacije je nalaganje in upravljanje z naloženim oglasnim materialom. 
Uporabniki so stranke podjetja, ki jim nudi spletno oglaševanje, in tu lahko upravljajo z oglasi.

Na prvi strani imajo vpogled v projekte, lahko tudi ustvarijo nov projekt. Ob izbiri projekta
se jim ta odpre in prikaže vse naložene datoteke. V njem lahko spremenijo njegovo ime ali ga izbrišejo.
Slike ali videje nalagajo v dropzone na modal oknu.

API je glavna datoteka index.php, ki črpa CRUD upravljanje z bazo iz mape Models. 
V njej so modeli, vsak za upravljanje s svojo tabelo v bazi, in razredi uporabljeni v teh modelih.
