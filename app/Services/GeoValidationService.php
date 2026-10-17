<?php

namespace App\Services;

class GeoValidationService
{
    /**
     * Check if a point (latitude, longitude) is inside a polygon.
     *
     * @param float $lat
     * @param float $lng
     * @param array $polygon Array of points, e.g., [['lat' => y1, 'lng' => x1], ...] or [[lat1, lng1], ...]
     * @return bool
     */
    public function isPointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $numPoints = count($polygon);
        if ($numPoints < 3) {
            return false;
        }

        // Normalize polygon coordinates format to ['lat', 'lng']
        $coords = [];
        foreach ($polygon as $point) {
            if (isset($point['lat']) && isset($point['lng'])) {
                $coords[] = ['lat' => (float)$point['lat'], 'lng' => (float)$point['lng']];
            } elseif (isset($point[0]) && isset($point[1])) {
                // Leaflet stores coordinates as [lat, lng]
                $coords[] = ['lat' => (float)$point[0], 'lng' => (float)$point[1]];
            }
        }

        $numPoints = count($coords);

        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $coords[$i]['lng'];
            $yi = $coords[$i]['lat'];
            $xj = $coords[$j]['lng'];
            $yj = $coords[$j]['lat'];

            $intersect = (($yi > $lat) != ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi + 0.0000001) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
