# Free UK Property APIs for Due Diligence

A comprehensive reference of free (or free-tier) APIs and open data sources available in the UK that are useful for building a property due diligence application for prospective house buyers.

---

## 1. Postcode Lookup & Geocoding

### Postcodes.io
- **URL:** https://postcodes.io
- **Cost:** Completely free, no API key required
- **What it gives you:** Postcode lookup, latitude/longitude, LSOA/MSOA codes, ward, parish, constituency, local authority, region, CCG, police force area, nearest postcodes, bulk lookups, reverse geocoding, validation, autocomplete
- **Data sources:** ONS Postcode Directory, Ordnance Survey Open Names
- **Why it matters:** This is the backbone of any property app. You can resolve a postcode into all the administrative geography codes you need to query other APIs (LSOA for crime, local authority for planning, etc.)
- **Rate limits:** Generous, suitable for most use cases

### Ordnance Survey Data Hub (Free Tier)
- **URL:** https://osdatahub.os.uk
- **Cost:** Free tier available (OS OpenData plan), requires registration and API key
- **What it gives you:** OS Maps API (base mapping tiles), OS Names API (place name search), OS Features API (buildings, roads, rivers, boundaries), OS Downloads API (bulk open data downloads), OS Linked Identifiers API (cross-referencing UPRNs and TOIDs)
- **Why it matters:** Authoritative mapping and geographic data for Great Britain. The free tier gives access to all OS OpenData products. Premium data (detailed address-level data via OS Places API) requires a paid plan unless you qualify under the Public Sector Geospatial Agreement.
- **Note:** OS Places API (the detailed address lookup) is premium. For free address lookups, use Postcodes.io or the open data alternatives.

---

## 2. Property Sales History & Valuations

### HM Land Registry Price Paid Data
- **URL:** https://landregistry.data.gov.uk
- **Cost:** Free, Open Government Licence
- **What it gives you:** Every residential property sale in England and Wales since January 1995 (24+ million records). Includes sale price, date, property type (detached/semi/terrace/flat), new build flag, freehold/leasehold status, full address.
- **Access methods:**
  - CSV/TXT bulk downloads (monthly updates)
  - SPARQL endpoint for linked data queries
  - Interactive search tool on the website
- **Why it matters:** Essential for understanding what a property last sold for, price trends on a street, and whether the asking price is reasonable relative to comparable sales.

### UK House Price Index (UKHPI)
- **URL:** https://landregistry.data.gov.uk/app/ukhpi
- **Cost:** Free, Open Government Licence
- **What it gives you:** Monthly house price index data for England, Scotland, Wales and Northern Ireland. Broken down by region, local authority, property type, buyer type (first-time buyer vs existing owner). Includes average prices, percentage changes, sales volumes.
- **Access methods:** SPARQL endpoint, direct API queries, CSV downloads
- **Why it matters:** Lets you show area-level price trends and whether prices are rising or falling in the area the buyer is looking at.

### HM Land Registry - Use Land and Property Data Service
- **URL:** https://use-land-property-data.service.gov.uk
- **Cost:** Free (some datasets require account registration and licence agreement)
- **What it gives you:** Several datasets via a REST API:
  - **UK companies that own property in England and Wales (CCOD)** - free
  - **Overseas companies that own property in England and Wales (OCOD)** - free
  - **Registered Leases** - free (licence required)
  - **Restrictive Covenants** - free (licence required)
  - **National Polygon Service** - paid (20k/year)
- **Why it matters:** The CCOD and OCOD datasets let you check if a property is owned by a company rather than an individual, which can be a red flag or indicate a buy-to-let. Registered leases data is useful for leasehold due diligence.

---

## 3. Energy Performance Certificates (EPC)

### MHCLG Domestic EPC API
- **URL:** https://epc.opendatacommunities.org/docs/api
- **Cost:** Free, requires registration for an API key
- **What it gives you:** Full EPC data for domestic properties in England and Wales, going back to 2008. Includes:
  - Energy rating (A-G) and score
  - Property type, age, floor area
  - Wall/roof/window/heating descriptions and ratings
  - Estimated energy costs (lighting, heating, hot water)
  - Environmental impact rating
  - Recommendations for improvement with estimated savings
- **Access methods:**
  - REST API with search by address, postcode, local authority, UPRN
  - Certificate lookup by LMK key
  - Recommendations endpoint
  - Bulk CSV downloads by local authority or time period
  - Swagger/OpenAPI interface
- **Why it matters:** Critical for buyers. Shows energy efficiency, likely running costs, and what improvements could be made. Also gives you floor area (total floor area in m2) which is hard to get elsewhere for free.
- **Note:** Contains personal data elements so requires agreement to data use terms.

---

## 4. Flood Risk

### Environment Agency Flood Monitoring API
- **URL:** https://environment.data.gov.uk/flood-monitoring/doc/reference
- **Cost:** Free, no registration required, Open Government Licence
- **What it gives you:**
  - Current flood warnings and alerts (updated every 15 minutes)
  - Severity levels (Severe Flood Warning, Flood Warning, Flood Alert)
  - Flood warning/alert area polygons (GeoJSON)
  - Real-time river levels, sea levels, groundwater levels, rainfall data from monitoring stations
  - Station metadata and historical readings
- **Why it matters:** Shows the buyer if the property is in or near a current flood alert area and provides real-time monitoring data.

### Environment Agency Flood Risk Data
- **URL:** https://environment.data.gov.uk/dataset/04532375-a198-476e-985e-0579a0a11b47
- **Cost:** Free, Open Government Licence
- **What it gives you:** Long-term flood zone data (Flood Zones 1, 2, 3) as spatial datasets showing risk from rivers and sea. Also available: Risk of Flooding from Rivers and Sea (RoFRS) dataset, Risk of Flooding from Surface Water, and Historic Flood Map.
- **Access methods:** Download as shapefiles/GeoPackage, or via WMS/WFS web services
- **Why it matters:** This is what conveyancers use. Shows whether a property is in a flood zone, which affects insurance costs and mortgage eligibility. Flood Zone 3 means >1% annual probability of river flooding.
- **Note:** For a simple postcode-level lookup, the GOV.UK "Check for flooding" service uses this data, but there's no simple REST API for "give me flood risk for this postcode". You'll need to do a spatial lookup against the polygons, or use the GOV.UK long-term flood risk checker programmatically.

---

## 5. Crime Data

### Police UK API
- **URL:** https://data.police.uk/docs/
- **Cost:** Completely free, no API key required
- **What it gives you:**
  - Street-level crime data by location (lat/long or within a polygon) and month
  - Crime categories (burglary, robbery, anti-social behaviour, vehicle crime, etc.)
  - Outcome data for individual crimes
  - Stop and search data
  - Neighbourhood team information
  - Neighbourhood boundaries
  - Nearest police stations
- **Why it matters:** Buyers want to know the crime profile of an area. You can show a breakdown of crime types in the vicinity of the property over the last 12+ months.
- **Note:** Data is anonymised to street level (snapped to nearest point on a street). Updated monthly, typically with a 2-month lag.

---

## 6. Schools & Education

### DfE Get Information About Schools (GIAS)
- **URL:** https://get-information-schools.service.gov.uk
- **Cost:** Free bulk data downloads
- **What it gives you:** Register of all schools in England including name, address, type (academy, maintained, free school, independent), phase (primary, secondary), age range, pupil numbers, Ofsted rating, trust/federation membership, religious character, gender, URN (Unique Reference Number).
- **Access methods:** Bulk CSV downloads (updated regularly), no formal REST API but data is machine-readable
- **Why it matters:** Proximity to good schools is one of the biggest factors in property value. Cross-reference with Postcodes.io location data to show nearby schools and their Ofsted ratings.

### DfE Explore Education Statistics API
- **URL:** https://explore-education-statistics.service.gov.uk
- **Cost:** Free
- **What it gives you:** Detailed performance data, Ofsted ratings by area, school places data, all downloadable as CSV with programmatic access.
- **Why it matters:** Supplements GIAS with actual performance metrics and area-level education quality data.

### Ofsted API (Third-party)
- **URL:** https://ofstedapi.uk
- **Cost:** Free tier available
- **What it gives you:** RESTful API access to Ofsted inspection data including ratings, inspection dates, and reports for schools and childcare providers.
- **Note:** This is a third-party wrapper around Ofsted's public data, not an official government API. Check their terms for commercial use.

---

## 7. Planning Applications & Land Use

### Planning Data Platform (MHCLG)
- **URL:** https://www.planning.data.gov.uk
- **Cost:** Free, Open Government Licence
- **What it gives you:** National planning and housing datasets collected from local planning authorities, including:
  - Conservation areas
  - Article 4 directions
  - Tree preservation orders
  - Listed buildings (cross-referenced from Historic England)
  - Green belt boundaries
  - Areas of Outstanding Natural Beauty
  - Sites of Special Scientific Interest
  - Brownfield land registers
- **Access methods:** API (experimental/beta), bulk data downloads, interactive map
- **Why it matters:** Shows planning constraints that affect what a buyer can do with a property. Conservation area status, TPOs, and Article 4 directions all limit permitted development rights.
- **Note:** Still in beta. Coverage varies by local authority. Not yet comprehensive for planning applications themselves (more focused on planning constraints/designations).

### Local Authority Planning Portals
- **Cost:** Free (varies by council)
- **What it gives you:** Individual council planning portals typically offer search functionality for planning applications. Many use Idox or similar platforms.
- **Why it matters:** Checking nearby planning applications is essential due diligence. A large development next door could significantly affect a property.
- **Note:** There's no single national free API for planning applications. The Planning Data Platform is working towards this but isn't there yet. You'd need to either scrape individual council sites or use a paid aggregator like PlanIt, LandHawk, or Searchland.

---

## 8. Listed Buildings & Heritage

### Historic England Open Data Hub
- **URL:** https://historicengland.org.uk/listing/the-list/open-data-hub
- **Cost:** Free, Open Government Licence
- **What it gives you:**
  - National Heritage List for England (NHLE) - all listed buildings (Grade I, II*, II), scheduled monuments, registered parks and gardens, registered battlefields, protected wrecks
  - Available as GIS shapefiles (points and polygons), updated regularly
  - Conservation areas dataset (compiled from local authorities)
  - Heritage at Risk register
- **Why it matters:** If a property is listed, the buyer faces significant restrictions on alterations. Grade also affects insurance costs. Being near a listed building or in a conservation area also affects what you can do.

---

## 9. Air Quality

### DEFRA UK-AIR API
- **URL:** https://uk-air.defra.gov.uk
- **Cost:** Free, Open Government Licence
- **What it gives you:** Real-time and historical air pollution measurements from monitoring stations across the UK. Covers NO2, PM2.5, PM10, ozone, SO2, and other pollutants.
- **Access methods:** OGC Sensor Observation Service (SOS) API, plus data downloads
- **Why it matters:** Air quality is increasingly important to buyers, particularly in urban areas. High pollution levels affect health and can impact property values.
- **Note:** Station coverage is patchy - there isn't a monitoring station near every property. Best used for area-level indicators rather than property-specific data.

---

## 10. Broadband & Connectivity

### ThinkBroadband Availability API
- **URL:** https://www.thinkbroadband.com/broadband-availability-api
- **Cost:** Free for non-commercial/low-volume use, paid plans for commercial
- **What it gives you:** Broadband coverage and estimated speeds by postcode or UPRN, including:
  - Available technologies (ADSL, FTTC, FTTP, cable, alt-net)
  - Provider availability (Openreach, Virgin Media, CityFibre, Hyperoptic, etc.)
  - Estimated download/upload speeds
  - Exchange information
  - Ofcom market classification
- **Why it matters:** Broadband speed is a significant factor for many buyers, especially those working from home. Full fibre availability vs. being stuck on ADSL makes a real difference.
- **Note:** Free tier has usage limits. For commercial applications you'll need a paid plan. Check their current terms.

### Ofcom Connected Nations Data
- **URL:** https://www.ofcom.org.uk/research-and-data/telecoms-research/connected-nations
- **Cost:** Free bulk data downloads
- **What it gives you:** Annual datasets on broadband and mobile coverage at postcode level, including availability of superfast/ultrafast broadband by provider.
- **Note:** Ofcom's coverage checker (checker.ofcom.org.uk) doesn't have a public API, but the underlying data is available as bulk downloads.

---

## 11. Demographics & Area Statistics

### ONS Statistics API
- **URL:** https://developer.ons.gov.uk
- **Cost:** Free, no API key required
- **What it gives you:** Census data, population estimates, household income, employment, deprivation indices, and more. Queryable by geography (local authority, LSOA, MSOA, etc.)
- **Why it matters:** Gives context about the neighbourhood - income levels, population density, age demographics, deprivation score. LSOA-level data from the Index of Multiple Deprivation is particularly useful.

### ONS Open Geography Portal
- **URL:** https://geoportal.statistics.gov.uk
- **Cost:** Free
- **What it gives you:** Boundary data and geographic lookups for all UK administrative and statistical geographies. Essential for mapping LSOA/MSOA/ward boundaries.

---

## 12. Company Ownership Checks

### Companies House API
- **URL:** https://developer.company-information.service.gov.uk
- **Cost:** Free, requires registration for an API key
- **Rate limit:** 600 requests per 5 minutes
- **What it gives you:** Company profiles, officers, people of significant control, filing history, insolvency data, charges, and more for all UK registered companies.
- **Why it matters:** If the property is owned by a company (identifiable via HMLR CCOD data), you can look up who's behind that company, check for insolvency, outstanding charges, etc. Also useful for checking the developer/builder of new-build properties.

---

## 13. Council Tax

### VOA Council Tax Band Data
- **URL:** https://www.gov.uk/council-tax-bands (lookup tool) / https://www.tax.service.gov.uk/check-council-tax-band/search
- **Cost:** Free to look up
- **What it gives you:** Council tax band (A-H) for a specific property
- **Note:** There is no official free API for council tax bands. The VOA provides a lookup tool but not a programmatic API. Some third-party services (PropertyData, getAddress) aggregate this data. You could potentially scrape the VOA tool, but this may breach their terms. The EPC data sometimes includes council tax band as a field, which could serve as a proxy.
- **Why it matters:** Council tax band directly affects the ongoing cost of owning the property.

---

## 14. Radon Risk

### UKRadon (UKHSA)
- **URL:** https://www.ukradon.org
- **Cost:** Free map viewer, individual property reports cost ~3-5 GBP
- **What it gives you:** Whether an area is a "radon Affected Area" and the probability of radon levels being above the Action Level.
- **Note:** No free API. The map viewer gives area-level indication but property-specific reports are paid. For a due diligence app, you could use the publicly available radon affected area maps as a rough indicator.
- **Why it matters:** High radon levels are a health risk and may require remediation work. Conveyancing searches typically include radon data.

---

## 15. Transport & Accessibility

### Transport API / TfL API
- **TfL URL:** https://api.tfl.gov.uk (free, registration required)
- **What it gives you (TfL):** Tube/bus/rail station locations, journey planning, service status. London-specific.
- **Note:** For national public transport data, the DfT provides NaPTAN (National Public Transport Access Nodes) as a free download, giving you every bus stop, rail station, airport, etc. in the UK.

### PTAL (Public Transport Accessibility Level)
- **Note:** PTAL is London-only and calculated by TfL. No single national equivalent API exists for free. PropertyData includes PTAL in their paid API.

---

## Summary: Quick Reference Table

| Data Category | Source | Free API? | Registration? |
|---|---|---|---|
| Postcode/geocoding | Postcodes.io | Yes | No |
| Mapping | OS Data Hub | Free tier | Yes |
| Sales history | HMLR Price Paid | Yes (SPARQL/CSV) | No |
| House price trends | UKHPI | Yes (SPARQL/API) | No |
| Ownership (companies) | HMLR CCOD/OCOD | Yes | Yes (free account) |
| EPC data | MHCLG EPC API | Yes | Yes (free account) |
| Flood risk (real-time) | EA Flood Monitoring | Yes | No |
| Flood zones (long-term) | EA Spatial Data | Yes (WMS/download) | No |
| Crime | Police UK API | Yes | No |
| Schools | DfE GIAS | Bulk CSV | No |
| Planning constraints | Planning Data Platform | Yes (beta) | No |
| Listed buildings | Historic England | Yes (downloads) | No |
| Air quality | DEFRA UK-AIR | Yes | No |
| Broadband | ThinkBroadband | Free tier | Check terms |
| Demographics | ONS API | Yes | No |
| Company data | Companies House | Yes | Yes (free key) |
| Council tax band | VOA | No API | N/A |
| Radon | UKHSA UKRadon | No free API | N/A |
| Transport | TfL / NaPTAN | Yes | TfL: Yes |

---

## Notes on Building the Application

**Address resolution chain:** The typical flow would be: user enters postcode -> Postcodes.io gives you lat/long, LSOA, local authority, ward, etc. -> use those identifiers to query the other APIs (Police API wants lat/long, EPC wants postcode, ONS wants LSOA code, etc.)

**Gaps to be aware of:**
- There's no single free API that gives you everything - you're stitching together 10-15 data sources
- Council tax band has no free API (the EPC data sometimes includes it)
- Planning applications don't have a comprehensive free national API yet
- Leasehold details (remaining years, ground rent, service charges) aren't available via free APIs - this is typically obtained through the conveyancing process
- Building survey data, subsidence risk, and Japanese knotweed data are all paid services
- Local authority search data (rights of way, tree preservation orders at property level, road adoption status) is typically only available through paid environmental/local authority search providers like Landmark or Groundsure

**Rate limiting:** Most government APIs are generous but not unlimited. The Police API, for instance, has no published rate limit but will throttle excessive use. Build in sensible caching - property data doesn't change minute to minute.
