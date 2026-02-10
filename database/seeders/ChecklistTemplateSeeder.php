<?php

namespace Database\Seeders;

use App\Models\ChecklistTemplate;
use Illuminate\Database\Seeder;

class ChecklistTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Flood & Environmental
            ['category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'key' => 'flood_zone', 'label' => 'Flood Zone', 'severity' => 'deal_breaker', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 1],
            ['category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'key' => 'flood_warnings', 'label' => 'Active Flood Warnings', 'severity' => 'deal_breaker', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 2],
            ['category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'key' => 'surface_water_risk', 'label' => 'Surface Water Risk', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 3],
            ['category' => 'flood_environmental', 'category_label' => 'Flood & Environmental', 'key' => 'radon_risk', 'label' => 'Radon Risk', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Check radon levels for your area', 'link' => 'https://www.ukradon.org', 'sort_order' => 4],

            // Price & Value
            ['category' => 'price_value', 'category_label' => 'Price & Value', 'key' => 'previous_sale_prices', 'label' => 'Previous Sale Prices', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 5],
            ['category' => 'price_value', 'category_label' => 'Price & Value', 'key' => 'price_vs_comparables', 'label' => 'Price vs Local Comparables', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 6],
            ['category' => 'price_value', 'category_label' => 'Price & Value', 'key' => 'area_price_trend', 'label' => 'Area Price Trend', 'severity' => 'nice_to_have', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 7],

            // Energy & Condition
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'epc_rating', 'label' => 'EPC Rating', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 8],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'recommended_improvements', 'label' => 'Recommended Improvements', 'severity' => 'nice_to_have', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 9],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'heating_system', 'label' => 'Heating System', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 10],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'wall_roof_window_efficiency', 'label' => 'Wall/Roof/Window Efficiency', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 11],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'damp_structural', 'label' => 'Damp & Structural', 'severity' => 'deal_breaker', 'type' => 'manual', 'guidance' => 'Check survey report, look for cracks, staining, musty smells', 'link' => null, 'sort_order' => 12],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'roof_condition', 'label' => 'Roof Condition', 'severity' => 'important', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 13],
            ['category' => 'energy_condition', 'category_label' => 'Energy & Condition', 'key' => 'boiler_age_service', 'label' => 'Boiler Age & Service', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Ask agent for boiler age and last service date', 'link' => null, 'sort_order' => 14],

            // Legal & Title
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'freehold_leasehold', 'label' => 'Freehold or Leasehold', 'severity' => 'deal_breaker', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 15],
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'lease_length', 'label' => 'Lease Length', 'severity' => 'deal_breaker', 'type' => 'manual', 'guidance' => 'Check remaining lease years — under 80 years is problematic', 'link' => null, 'sort_order' => 16],
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'restrictive_covenants', 'label' => 'Restrictive Covenants', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Conveyancer checks title deeds', 'link' => null, 'sort_order' => 17],
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'rights_of_way', 'label' => 'Rights of Way', 'severity' => 'important', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 18],
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'boundary_clarity', 'label' => 'Boundary Clarity', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Compare title plan to reality on site', 'link' => null, 'sort_order' => 19],
            ['category' => 'legal_title', 'category_label' => 'Legal & Title', 'key' => 'chancel_repair_liability', 'label' => 'Chancel Repair Liability', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 20],

            // Planning & Building
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'conservation_area', 'label' => 'Conservation Area', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 21],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'listed_building', 'label' => 'Listed Building', 'severity' => 'deal_breaker', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 22],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'article_4_direction', 'label' => 'Article 4 Direction', 'severity' => 'important', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 23],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'tpos', 'label' => 'Tree Preservation Orders', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 24],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'nearby_planning', 'label' => 'Nearby Planning Applications', 'severity' => 'important', 'type' => 'automated', 'guidance' => 'Check council planning portal', 'link' => null, 'sort_order' => 25],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'building_regs', 'label' => 'Building Regs for Modifications', 'severity' => 'deal_breaker', 'type' => 'manual', 'guidance' => 'Ask seller for completion certificates for any works', 'link' => null, 'sort_order' => 26],
            ['category' => 'planning_building', 'category_label' => 'Planning & Building', 'key' => 'permitted_development', 'label' => 'Permitted Development Rights', 'severity' => 'important', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 27],

            // Crime
            ['category' => 'crime', 'category_label' => 'Crime', 'key' => 'overall_crime_level', 'label' => 'Overall Crime Level', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 28],
            ['category' => 'crime', 'category_label' => 'Crime', 'key' => 'burglary_rate', 'label' => 'Burglary Rate', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 29],
            ['category' => 'crime', 'category_label' => 'Crime', 'key' => 'violent_crime', 'label' => 'Violent Crime', 'severity' => 'important', 'type' => 'automated', 'guidance' => null, 'link' => null, 'sort_order' => 30],

            // Schools
            ['category' => 'schools', 'category_label' => 'Schools', 'key' => 'primary_ofsted', 'label' => 'Nearest Primary Ofsted', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 31],
            ['category' => 'schools', 'category_label' => 'Schools', 'key' => 'secondary_ofsted', 'label' => 'Nearest Secondary Ofsted', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 32],

            // Connectivity
            ['category' => 'connectivity', 'category_label' => 'Connectivity', 'key' => 'broadband_fibre', 'label' => 'Broadband & Fibre', 'severity' => 'important', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 33],
            ['category' => 'connectivity', 'category_label' => 'Connectivity', 'key' => 'mobile_signal', 'label' => 'Mobile Signal', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => 'Check signal during viewing', 'link' => null, 'sort_order' => 34],
            ['category' => 'connectivity', 'category_label' => 'Connectivity', 'key' => 'commute', 'label' => 'Commute', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Test commute at actual travel time', 'link' => null, 'sort_order' => 35],

            // Neighbourhood
            ['category' => 'neighbourhood', 'category_label' => 'Neighbourhood', 'key' => 'deprivation_index', 'label' => 'Deprivation Index', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 36],
            ['category' => 'neighbourhood', 'category_label' => 'Neighbourhood', 'key' => 'noise', 'label' => 'Noise Levels', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Visit at different times of day', 'link' => null, 'sort_order' => 37],
            ['category' => 'neighbourhood', 'category_label' => 'Neighbourhood', 'key' => 'parking', 'label' => 'Parking', 'severity' => 'important', 'type' => 'manual', 'guidance' => 'Visit in the evenings to check availability', 'link' => null, 'sort_order' => 38],
            ['category' => 'neighbourhood', 'category_label' => 'Neighbourhood', 'key' => 'neighbours', 'label' => 'Neighbours', 'severity' => 'nice_to_have', 'type' => 'manual', 'guidance' => null, 'link' => null, 'sort_order' => 39],
        ];

        foreach ($items as $item) {
            ChecklistTemplate::updateOrCreate(
                ['key' => $item['key']],
                $item,
            );
        }
    }
}
