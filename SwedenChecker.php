<?php

class SwedenChecker
{
    private $min_x;
    private $max_x;
    private $min_y;
    private $max_y;

    public $rectangles = [
        [[11.777344, 58.859224], [17.204590, 60.576175]],
        [[12.019043, 57.633640], [16.528931, 57.601278]],
        [[12.601318, 60.603150], [16.940918, 63.626745]],
        [[12.744141, 56.692442], [16.430054, 57.601278]],
        [[12.974854, 56.142489], [15.979614, 56.662265]],
        [[13.068237, 55.466399], [14.238281, 56.105747]],
        [[14.238281, 63.626745], [19.577637, 66.998844]],
        [[17.006836, 62.679186], [18.687744, 63.592562]],
        [[17.286987, 59.060330], [18.286743, 60.370429]],
        [[17.402344, 67.007428], [23.466797, 68.688521]],
        [[19.731445, 65.721594], [23.730469, 66.998844]],
    ];

    public function __construct()
    {
        $this->calculateCorners();
    }

    public function createSwedishCoordinate(): array
    {
        while (true) {
            $coordinate = $this->createCoordinate();
            if ($this->isInSweden($coordinate)) {
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

    public function isInSweden(array $coordinate): bool
    {
        return 0 < count(
                array_filter(
                    $this->rectangles,
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

    private function calculateCorners(): void
    {
        $lower_left_points = array_column($this->rectangles, 0);
        $upper_right_points = array_column($this->rectangles, 1);

        $this->min_x = min(array_column($lower_left_points, 0));
        $this->max_x = max(array_column($upper_right_points, 0));
        $this->min_y = min(array_column($lower_left_points, 1));
        $this->max_y = max(array_column($upper_right_points, 1));
    }

}

