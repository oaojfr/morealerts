# morealerts

Plugin MoreAlerts pour GLPI

Forké de additionalalerts par InfotelGLPI, créé par Joao.

### Français

Ce plugin vous permet d'envoyer les alertes email supplémentaires :
* Matériels ayant une date d'achat vide (choix des types pris en charge : pour ne pas prendre en compte les matériels non achetés)
* Niveaux des cartouches bas (information provenant de Fusion Inventory)
* Machines sans numéro d'inventaire
* Machines sans numéro de série
* Tickets non résolus depuis X jours
* Tickets en attente de validation depuis X jours
* Tickets en attente de réponse utilisateur depuis X jours
* Technicien ayant plus de N tickets ouverts
* Tickets à priorité élevée non traités sous X jours
* Tickets en statut « En attente » depuis trop longtemps
* Matériel sans emplacement
* Alerte garantie expirée sur les équipements (Ordinateur, Moniteur, Périphérique)
* Alerte fin de vie atteinte (équipements dont la date de fin de vie est dépassée)
* Alerte équipement non inventorié depuis X jours (pas de synchronisation d'inventaire récente)
* Alerte équipement sans affectation (utilisateur ou service)
* Alerte informations obligatoires manquantes (numéro de série, marque, modèle)
* Alerte ordinateur non utilisé depuis X jours (pas de connexion récente)
* Alerte périphérique non rattaché à un poste
* Alerte localisation obsolète ou manquante
* Alerte maintenance planifiée ou en retard
* Alerte nombre élevé de pannes/incidents

#### Alertes contrôle qualité (qualité de la CMDB)
* Ordinateurs/moniteurs/périphériques avec modèle, fabricant, OS, numéro d’inventaire, numéro de série ou date d’achat manquants ou incohérents
* Équipements avec doublons détectés (numéro de série, inventaire, nom)
* Équipements affectés à des utilisateurs ou services désactivés/inexistants
* Équipements dont la date d’achat est postérieure à la date de mise en service ou de garantie
* Équipements avec informations obsolètes (OS ou firmware non supporté, version logicielle trop ancienne)
* Équipements sans historique de mouvement ou de maintenance
* Équipements présents dans des lieux supprimés ou non référencés
* Équipements avec relations incomplètes (ex : ordinateur sans moniteur alors que la politique l’exige)
* Équipements avec statuts incohérents (ex : matériel « en stock » mais affecté à un utilisateur)
* Équipements dont la dernière modification date de plus d’un an (risque d’obsolescence des données)
* Équipements sans sauvegarde déclarée ou sans sauvegarde récente
* Ordinateurs/périphériques avec stockage saturé (disque plein)
* Équipements avec incidents récurrents (plus de X incidents sur une période)
* Équipements sans maintenance préventive planifiée
* Périphériques connectés mais non utilisés depuis X jours
* Équipements avec firmware obsolète
* Équipements sans affectation réseau (pas d’IP ou non vus sur le réseau depuis X jours)
* Ordinateurs sans antivirus actif ou à jour
* Ordinateurs sans périphériques critiques (écran, clavier, souris, etc.)

Chaque alerte est activable/désactivable via l'interface web et dispose de son propre modèle de notification GLPI (email configurable).

### English

This plugin enables you to send supplementary email alerts:
* Items with empty buy date (choice of supported types: to ignore non-purchased equipment)
* Cartridges whose level is low (FusionInventory information)
* Computers with empty inventory number
* Computers with empty serial number
* Unresolved tickets for more than X days
* Tickets waiting for validation for more than X days
* Tickets waiting for user response for more than X days
* Technician with more than N open tickets
* High priority tickets not processed within X days
* Tickets pending too long
* Equipment with no location
* Warranty expired alert on equipment (Computer, Monitor, Peripheral)
* End of life alert (equipment with end of life date reached)
* Equipment not inventoried since X days (no recent inventory sync)
* Equipment with no assignment (user or service)
* Equipment with missing mandatory info (serial number, brand, model)
* Computer not used since X days (no recent login)
* Peripheral not linked to a computer
* Equipment with obsolete or missing location
* Maintenance alert (planned or overdue maintenance)
* High incident count alert (equipment with many incidents/failures)

#### Quality control alerts (CMDB data quality)
* Computers/monitors/peripherals with missing or inconsistent required fields (model, manufacturer, OS, inventory number, serial number, buy date)
* Equipment with detected duplicates (serial number, inventory number, name)
* Equipment assigned to disabled/nonexistent users or services
* Equipment with buy date after warranty or commissioning date
* Equipment with obsolete information (unsupported OS/firmware, outdated software version)
* Equipment with no movement or maintenance history
* Equipment located in deleted or unreferenced locations
* Equipment with incomplete relations (e.g. computer without monitor when required)
* Equipment with inconsistent status (e.g. "in stock" but assigned to a user)
* Equipment not modified for over a year (risk of obsolete data)
* Equipment without declared backup or without recent backup
* Computers/peripherals with full storage (disk nearly full)
* Equipment with recurring incidents (more than X incidents in a period)
* Equipment without scheduled preventive maintenance
* Peripherals connected but unused for X days
* Equipment with outdated firmware
* Equipment without network assignment (no IP or not seen on network for X days)
* Computers without active or up-to-date antivirus
* Computers missing critical peripherals (screen, keyboard, mouse, etc.)

Each alert can be enabled/disabled via the web interface and has its own GLPI notification template (configurable email).
