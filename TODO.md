jede todo ist eine aufgabe für dich. wenn du sie erledigt hast, dann streiche sie durch. wenn du eine aufgabe nicht verstehst, dann frage mich bitte.
denke ans testen. generell gilt php 8.4 typesafety, laravel 12, nutze vorhandene strukturen

## Views

- [ ] erstelle in index page layout (wie create) wovon alles index views nutzen haben. die filter section sollte aus page raus (andere benötigen das ja nicht)und füge es dort ein. denke das gleiche auch für stats, auch das results feld sollte extrahiert werden und darein
- [ ] die sidebar hat alle links einfach untereinander stehen. bitte sections machen für alles was da rein gehört


## Tests

- [x] bitte shau in den feature ordner die tests liegen alle im gleichen verzeichniss bitte all in ein plausibles verzeichnis ablegen
- [x] mach dir bitte ein vermerk in claude.md dass du zukünftig weist dass du tests in pest schreibst und mit describe() strukturierst 
- [x] mache dir bitte ein vermerk in claude.md dass du zukünftig weisst dass du wenn du fertig mit meiner aufgabe bist noch 'composer test' ausführst und mögliche fehler behebst
- [ ] prüfe bitte alle tests ob sie aktuell sind, describe benutzen und pestphp
- [ ] performance steigern

## Feature area
- [x] der User sollte nun die möglichkeit haben seine areas zu editieren (denke an links damit der user auch zu den areas kommt)
- [x] der User sollte nun die möglichkeit haben seine areas zu löschen 
- [x] momentan ist es so gelöst dass ein garten viele blumen hat (belongstomany). dazu gibt es auch einen pivottable. ich denke aber dass eine blume in einem bereich gepflanzt wird. also sollte die pflanze eher eine beziehung zum bereich haben und nicht zum garten. ändere das bitte. nutze die vorhandenen sachen (migration) benenne sie um passe sie an den
- [x] warum werden die dataen für die selectboxen in den views geladen? das sollte doch im controller passieren. bitte ändere das (index view)

## Feature Plants
- [x] die plants index view implementiert noch nicht die layouts.index view
- [x] die plants index view nutzt noch nicht die vorhanden form components oder auch die filter card
- [X] die plants index view lädt die daten für select noch in der view anstatt im controller

## routes

- [x] alle routes liegen da unstrukturiert in web. bitte modern und elegant strukturieren (realworld). 

## Controller

- [x] kontroller sollen schlank sein. überprüfe bitte ob alle kontroller eine request und service benutzen 
- [x] kontrolliere dass alle controller die einen authenticated user nutzen den authenticated controller extenden
