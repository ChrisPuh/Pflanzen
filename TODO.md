jede todo ist eine aufgabe für dich. wenn du sie erledigt hast, dann streiche sie durch. wenn du eine aufgabe nicht verstehst, dann frage mich bitte.
denke ans testen. generell gilt php 8.4 typesafety, laravel 12, nutze vorhandene strukturen

## Views

- [x] erstelle in index page layout (wie create) wovon alles index views nutzen haben. die filter section sollte aus page raus (andere benötigen das ja nicht)und füge es dort ein. denke das gleiche auch für stats, auch das results feld sollte extrahiert werden und darein
- [ ] die sidebar hat alle links einfach untereinander stehen. bitte sections machen für alles was da rein gehört


## Tests

- [x] bitte shau in den feature ordner die tests liegen alle im gleichen verzeichniss bitte all in ein plausibles verzeichnis ablegen
- [x] mach dir bitte ein vermerk in claude.md dass du zukünftig weist dass du tests in pest schreibst und mit describe() strukturierst 
- [x] mache dir bitte ein vermerk in claude.md dass du zukünftig weisst dass du wenn du fertig mit meiner aufgabe bist noch 'composer test' ausführst und mögliche fehler behebst
- [x] Garden model accessor issues behoben - location accessor, age calculation, full_location logic und age_display für views
- [ ] prüfe bitte alle tests ob sie aktuell sind, describe benutzen und pestphp
- [ ] performance steigern

## Feature Gardens

- [x] die garden.show view wird langsam unübersichtlich. lass uns das bitte beheben. sections in partials? schau bitte dass keine php code existiert bzw prüfe ob der controller es übernehmen kann, gibt es sachen die in anderen .show views auch verwendet werden (cards) sollten wir components machen (bitte sinvoll ablegen)
- [x] die garden.edit benutzt noch nicht die vorhandenen form components. bitte ändere das und die options für selectfields sollten vom controller bereitgestellt werden - komplett refactored mit input, select, textarea components
- [x] gefühlt alle garden views edit create etc nutzen in den action slots ähnliche buttons. bitte erstelle (wenn noch nicht vorhanden) eine component dafür und nutze sie in den views - back-button component erstellt und in allen views genutzt
- [x] die details section in garden.show benutzt immer wieder die gleichen sachen. man könnte array übergeben und loopen - detail-list component erstellt für wiederverwendbare Details
- [x] die garden.edit benutzt noch nicht die vorhandenen form components. bitte ändere das und die options für selectfields sollten vom controller bereitgestellt werden
- [x] die garden.edit benutzt noch nicht die vorhandenen form components. bitte ändere das und die options für selectfields sollten vom controller bereitgestellt werden
- [x] gefühlt alle garden views edit create etc nutzen in den action slots ähnliche buttons. bitte erstelle (wenn noch nicht vorhanden) eine component dafür und nutze sie in
  den views
- [x] die details section in garden.show benutzt immer wieder die gleichen sachen. man könnte array übergeben und loopen

## Feature Areas
- [x] der User sollte nun die möglichkeit haben seine areas zu editieren (denke an links damit der user auch zu den areas kommt)
- [x] der User sollte nun die möglichkeit haben seine areas zu löschen 
- [x] momentan ist es so gelöst dass ein garten viele blumen hat (belongstomany). dazu gibt es auch einen pivottable. ich denke aber dass eine blume in einem bereich gepflanzt wird. also sollte die pflanze eher eine beziehung zum bereich haben und nicht zum garten. ändere das bitte. nutze die vorhandenen sachen (migration) benenne sie um passe sie an den
- [x] warum werden die dataen für die selectboxen in den views geladen? das sollte doch im controller passieren. bitte ändere das (index view)

## Feature Plants
- [x] die plants index view implementiert noch nicht die layouts.index view
- [x] die plants index view nutzt noch nicht die vorhanden form components oder auch die filter card
- [X] die plants index view lädt die daten für select noch in der view anstatt im controller
- [ ] die plants index view enthält cards welche den cards in area index und garden index ähneln. bitte in ein composent auslagern und in den views nutzen (also cards in area index, garden index und plants index)

## routes

- [x] alle routes liegen da unstrukturiert in web. bitte modern und elegant strukturieren (realworld). 

## Controller

- [x] kontroller sollen schlank sein. überprüfe bitte ob alle kontroller eine request und service benutzen 
- [x] kontrolliere dass alle controller die einen authenticated user nutzen den authenticated controller extenden
