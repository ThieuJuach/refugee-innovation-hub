# Map Coordinates Guide

## The Issue

Stories were not appearing on the map because they didn't have latitude and longitude coordinates. The map needs these coordinates to place markers for each story.

## The Solution

Added an "Edit Location" feature in the admin dashboard that allows you to:
1. Automatically geocode locations (find coordinates from city/country names)
2. Manually enter coordinates if needed

## How to Add Coordinates to Stories

### Step 1: Login to Admin Dashboard

1. Go to your site: `http://localhost/refugee-innovation-hub/`
2. Click "Sign In"
3. Login with your admin credentials

### Step 2: Navigate to Dashboard

1. Click "Dashboard" in the navigation
2. Scroll down to "Published Stories" section

### Step 3: Add Coordinates to Stories

You'll now see a "Location" column that shows:
- **📍 Mapped** - Story has coordinates (will appear on map)
- **⚠️ No coordinates** - Story needs coordinates (won't appear on map)

For stories without coordinates:

1. Click the **"Edit Location"** button (blue button)
2. A modal will appear showing:
   - Current location text (e.g., "Nairobi, Kenya")
   - Auto-geocode button
   - Manual latitude/longitude fields

### Option A: Auto-Geocode (Recommended)

1. Click **"🌍 Find Coordinates Automatically"**
2. The system will search OpenStreetMap's database
3. If found, coordinates will be filled in automatically
4. Click **"Save Coordinates"**

### Option B: Manual Entry

If auto-geocoding doesn't work or you want precise coordinates:

1. Find coordinates using:
   - Google Maps (right-click location → copy coordinates)
   - [LatLong.net](https://www.latlong.net/)
   - Any other geocoding service

2. Enter the coordinates:
   - **Latitude**: Range from -90 to 90 (e.g., 40.7128 for New York)
   - **Longitude**: Range from -180 to 180 (e.g., -74.0060 for New York)

3. Click **"Save Coordinates"**

### Step 4: Verify on Map

1. Go to the "Map" page
2. Stories with coordinates will now appear as individual markers
3. Click markers to see story details and read more

## How the Map Works

### With Coordinates (Individual Markers)
- Each story appears at its exact location
- Click marker to see story preview with image
- "Read More" button to view full story

### Without Coordinates (Region Markers)
- Stories are grouped by region (East Africa, West Africa, etc.)
- One marker per region showing all stories in that region
- Approximate regional center coordinates used

## Tips for Best Results

1. **Be Specific**: Instead of just "Kenya", use "Nairobi, Kenya" or "Kakuma Camp, Kenya"

2. **Use English Names**: The geocoding service works best with English location names

3. **Include Country**: Always include the country name for better accuracy

4. **Check Results**: After geocoding, verify the coordinates are correct:
   - Click on the map marker
   - Make sure it's in the right location

5. **Bulk Update**: Add coordinates to all stories for the best map experience

## Common Location Examples

| Location Type | Good Format | Bad Format |
|--------------|-------------|------------|
| City | "Nairobi, Kenya" | "Nairobi" |
| Camp | "Kakuma Camp, Kenya" | "Kakuma" |
| Region | "Kampala, Uganda" | "Uganda" |
| Multiple | "Amman, Jordan" | "Middle East" |

## Troubleshooting

### Geocoding Failed
- Try adding more details (city + country)
- Try different spelling or English name
- Use manual entry with Google Maps coordinates

### Wrong Location
- Click "Edit Location" again
- Use manual entry with correct coordinates
- Be more specific with location text

### Story Not on Map
- Check if coordinates are saved (look for 📍 Mapped indicator)
- Refresh the Map page
- Verify latitude is -90 to 90, longitude is -180 to 180

## Example Workflow

1. Story: "Water Purification Innovation in Kakuma"
2. Location text: "Kakuma Camp, Kenya"
3. Click "Edit Location"
4. Click "Find Coordinates Automatically"
5. System finds: Lat 3.7181, Lon 34.8501
6. Click "Save"
7. Story now appears on map at Kakuma location!

## Next Steps

After adding coordinates to your stories:
- Share the map page with stakeholders
- Stories with exact locations are more impactful
- Update coordinates as you add new stories
- Consider adding coordinates during the approval process
