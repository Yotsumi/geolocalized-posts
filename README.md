# Geolocalized-posts
A wordpress plugin for manage and associate geotag to 

Shortcode for the map is [mapForGeotags]

Function to call for the sort is sortPosts(WeightDistance, WeightTime)
For a correct use of the function sortPosts() the sum of the tow variables must be 10
Example:
sortPosts(10, 0) The function order the posts only by distance
sortPosts(0, 10) The function order the posts only by time
sortPosts(5, 5) The function order the posts equally
sortPosts(7, 3) The function prefer the distance instead of the time
