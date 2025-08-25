jede todo ist eine aufgabe für dich. wenn du sie erledigt hast, dann streiche sie durch. wenn du eine aufgabe nicht
verstehst, dann frage mich bitte.
denke ans testen. generell gilt php 8.4 typesafety, laravel 12, nutze vorhandene

## Tests

- [ ] prüfe bitte alle tests ob sie aktuell sind, describe benutzen und pestphp
- [ ] performance steigern

## Feature Gardens

## Feature Areas

## Feature Plants

## Feature About

## Feature Welcome

## Feature Auth

- [ ] da noch keine Email logik implementiert ist sollte der authenticated user vorerste ausreichen um die routes zu
  besuchen. das feature machen wir nach dem ersten release
- [ ] wir haben emails noch nicht implementiert. also treiber service etc.
- [ ] tests zu email verification müssen vervollständigt werden

## Feature Kontakt

- [ ] section wo man bugs reporten kann (template) direkter post zu githaub issus
- [ ] section wo man feature requests machen kann (template) direkter post zu githaub issus
- [ ] section wo man

## routes

## Feature Requests

- [ ] feature requests wunschzettel in Blumen entdecken

## Refactoring

- [ ] implement invokable Controller, extends AreaController, Data Transfer Objects (DTOs) , Requests, Action,
  Repositories
    - [x] [AreaEditController.php](app/Http/Controllers/Area/AreaEditController.php)
    - [x] [AreaDeleteController.php](app/Http/Controllers/Area/AreaDeleteController.php)
    - [x] [AreaStoreController.php](app/Http/Controllers/Area/AreaStoreController.php)
    - [x] [AreaUpdateController.php](app/Http/Controllers/Area/AreaUpdateController.php)
    - [x] [AttachPlantToAreaController.php](app/Http/Controllers/Area/Actions/AttachPlantToAreaController.php)
    - [x] [PlantSelectionModal.php](app/Livewire/Area/PlantSelectionModal.php)
    - [ ] [AreasIndexController.php](app/Http/Controllers/Area/AreasIndexController.php)
    - [ ] [DetachPlantFromAreaController.php](app/Http/Controllers/Area/Actions/DetachPlantFromAreaController.php)
    - [ ] [AreaShowController.php](app/Http/Controllers/Area/AreaShowController.php)
    - [ ] [GardenCreateController.php](app/Http/Controllers/Garden/GardenCreateController.php)
    - [ ] [GardenEditController.php](app/Http/Controllers/Garden/GardenEditController.php)
    - [ ] [PlantsIndexController.php](app/Http/Controllers/Plants/PlantsIndexController.php)
    - [ ] [AreaForceDeleteController.php](app/Http/Controllers/Area/AreaForceDeleteController.php)
    - [ ] [AreaRestoreController.php](app/Http/Controllers/Area/AreaRestoreController.php)
