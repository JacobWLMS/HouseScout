<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $renames = [
            'flood_risk' => 'flood_zone',
            'crime_level' => 'overall_crime_level',
            'planning_issues' => 'nearby_planning',
            'price_history' => 'previous_sale_prices',
            'schools_nearby' => 'primary_ofsted',
            'broadband' => 'broadband_fibre',
        ];

        foreach ($renames as $old => $new) {
            DB::table('property_assessments')
                ->where('item_key', $old)
                ->update(['item_key' => $new]);
        }

        // Merge structural + damp → damp_structural (keep latest)
        DB::table('property_assessments')
            ->where('item_key', 'structural')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('property_assessments as pa2')
                    ->whereColumn('pa2.saved_property_id', 'property_assessments.saved_property_id')
                    ->where('pa2.item_key', 'damp');
            })
            ->update(['item_key' => 'damp_structural']);

        // Where both damp and structural exist, keep the more recent one
        $duplicates = DB::table('property_assessments as pa1')
            ->join('property_assessments as pa2', function ($join) {
                $join->on('pa1.saved_property_id', '=', 'pa2.saved_property_id')
                    ->where('pa1.item_key', '=', 'structural')
                    ->where('pa2.item_key', '=', 'damp');
            })
            ->select('pa1.id as structural_id', 'pa2.id as damp_id', 'pa1.updated_at as structural_updated', 'pa2.updated_at as damp_updated')
            ->get();

        foreach ($duplicates as $dup) {
            if ($dup->structural_updated >= $dup->damp_updated) {
                DB::table('property_assessments')->where('id', $dup->damp_id)->delete();
                DB::table('property_assessments')->where('id', $dup->structural_id)->update(['item_key' => 'damp_structural']);
            } else {
                DB::table('property_assessments')->where('id', $dup->structural_id)->delete();
                DB::table('property_assessments')->where('id', $dup->damp_id)->update(['item_key' => 'damp_structural']);
            }
        }

        // Rename remaining damp entries that weren't merged
        DB::table('property_assessments')
            ->where('item_key', 'damp')
            ->update(['item_key' => 'damp_structural']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $renames = [
            'flood_zone' => 'flood_risk',
            'overall_crime_level' => 'crime_level',
            'nearby_planning' => 'planning_issues',
            'previous_sale_prices' => 'price_history',
            'primary_ofsted' => 'schools_nearby',
            'broadband_fibre' => 'broadband',
            'damp_structural' => 'structural',
        ];

        foreach ($renames as $old => $new) {
            DB::table('property_assessments')
                ->where('item_key', $old)
                ->update(['item_key' => $new]);
        }
    }
};
