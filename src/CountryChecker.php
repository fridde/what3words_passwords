<?php


namespace Fridde;


class CountryChecker
{
    private $country;
    
    private $min_x;
    private $max_x;
    private $min_y;
    private $max_y;

    private $Rectangles = [];

    public function __construct(string $country)
    {
        $this->country = $country;
        $this->setRectangles();
        $this->calculateCorners();
    }

    private function calculateCorners(): void
    {
        $lower_left_points = array_column($this->Rectangles, 0);
        $upper_right_points = array_column($this->Rectangles, 1);

        $this->min_x = min(array_column($lower_left_points, 0));
        $this->max_x = max(array_column($upper_right_points, 0));
        $this->min_y = min(array_column($lower_left_points, 1));
        $this->max_y = max(array_column($upper_right_points, 1));
    }

    private function setRectangles(): void
    {
        $coordinates = parse_ini_file(BASE_DIR . 'coordinates.ini', true);
        
        foreach($coordinates[strtolower($this->country)]['x'] as $i => $rectangle){
            $corners = explode(';', $rectangle);
            foreach($corners as $corner){
                $lon_lat = explode(',', $corner);
                array_walk($lon_lat, 'trim');
                $this->Rectangles[$i][] = $lon_lat;
            }
        }
    }

    public function createCoordinateInCountry(): array
    {
        while (true) {
            $coordinate = $this->createCoordinate();
            if ($this->isInThisCountry($coordinate)) {
                return $coordinate;
            }
        }
    }

    public function createCoordinate(): array
    {
        $rand_1 = mt_rand() / mt_getrandmax();
        $rand_2 = mt_rand() / mt_getrandmax();

        $lon = $this->min_x + ($rand_1 * ($this->max_x - $this->min_x));
        $lat = $this->min_y + ($rand_2 * ($this->max_y - $this->min_y));

        return [$lat, $lon];

    }

    private function isInThisCountry(array $coordinate): bool
    {
        return 0 < count(
                array_filter(
                    $this->Rectangles,
                    function ($rectangle) use ($coordinate) {
                        return $this->isInRectangle($coordinate, $rectangle);
                    }
                )
            );
    }

    private function isInRectangle(array $coordinate, array $rectangle): bool
    {
        [$y, $x] = $coordinate;
        $r = $rectangle;

        return ($x >= $r[0][0] && $x <= $r[1][0] && $y >= $r[0][1] && $y <= $r[1][1]);
    }
    
}
