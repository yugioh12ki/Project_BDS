/**
 * Google Maps Services integration for BDS project
 * Using @googlemaps/google-maps-services-js library
 */

// If using the npm package directly in frontend (simplified approach)
// import { Client } from "@googlemaps/google-maps-services-js";

// Create a Google Maps client
// const mapsClient = new Client({});

/**
 * Geocode an address string to coordinates
 * @param {string} address The address to geocode
 * @returns {Promise} Promise with geocoding results
 */
async function geocodeAddress(address) {
    return new Promise((resolve, reject) => {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: address }, (results, status) => {
            if (status === "OK") {
                resolve({
                    lat: results[0].geometry.location.lat(),
                    lng: results[0].geometry.location.lng(),
                    formattedAddress: results[0].formatted_address
                });
            } else {
                reject(`Geocode failed: ${status}`);
            }
        });
    });
}

/**
 * Calculate distance between two coordinates
 * @param {object} origin Origin coordinates {lat, lng}
 * @param {object} destination Destination coordinates {lat, lng}
 * @returns {Promise} Promise with distance result
 */
async function calculateDistance(origin, destination) {
    return new Promise((resolve, reject) => {
        const service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix(
            {
                origins: [origin],
                destinations: [destination],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC
            },
            (response, status) => {
                if (status === "OK") {
                    resolve({
                        distance: response.rows[0].elements[0].distance,
                        duration: response.rows[0].elements[0].duration
                    });
                } else {
                    reject(`Distance calculation failed: ${status}`);
                }
            }
        );
    });
}

/**
 * Find places near a location
 * @param {object} location Center location {lat, lng}
 * @param {string} type Place type (e.g., 'school', 'hospital')
 * @param {number} radius Search radius in meters
 * @returns {Promise} Promise with nearby places
 */
async function findNearbyPlaces(location, type, radius = 1000) {
    return new Promise((resolve, reject) => {
        const service = new google.maps.places.PlacesService(
            document.createElement('div')
        );

        service.nearbySearch(
            {
                location: location,
                radius: radius,
                type: type
            },
            (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    resolve(results);
                } else {
                    reject(`Places search failed: ${status}`);
                }
            }
        );
    });
}

// Export functions for use in other files
export { geocodeAddress, calculateDistance, findNearbyPlaces };
