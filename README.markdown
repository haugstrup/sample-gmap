# Podio API Sample: Google Maps integration
Say you are trying to organize an event (e.g. a conference) and you need to evaluate several venue options. Wouldn't it be great if you had a visual overview of where each venue is placed along with their distance from transportation hubs, accomodation options and other sites of interests?

With Podio it's easy to create an app to store all this information and with the Podio API it's simple to integrate with Google Maps to show all the locations on one map to get a great visual overview.

This API sample grabs all items from a Location Scouting app and displays them on a Google Map. Each state from the app (e.g. "Event location" or "Accomodation") receives a different icon on the map. When clicking on a location on the map the title and description of the item is displayed in a speech bubble.

# Installation
* Create a "Location Scouting" app with three fields
  - Location field (to place markers on the map)
  - Text field (will be displayed as a description in the info window)
  - State field with the following states: "Event location", "Accomodation", "Site of interest" and "Transportation hub"
* Add some content to your app
* Copy config.php.example to config.php and add your credentials for Podio and add the App ID for your location app
* Run index.php in your browser