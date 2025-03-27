<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Utils\ColorUtils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::transaction(function () {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'),
                'role' => 'ADMIN'
            ]);

            $this->seedColors();
        });
    }

    public function seedColors(): void
    {
        $colors = [
            'Роскошный пурпур' => 'rgb(88,64,153)',
            'Щербет розовый' => '#FF85C8',
            'Розовая жвачка' => 'rgb(235,77,151)',
            'Черный перец' => 'rgb(17, 18, 18)',
            'Красное вино' => 'rgb(235,34,39)',
            'Полночный синий' => '#3155A4',
            'Синий деним' => 'rgb(0,129,195)',
            'Ледяной синий' => '#43B7E8',
            'Свежая вода' => 'rgb(20,191,217)',
            'Мальдивы' => '#5EC8DA',
            'Настоящий бирюзовый' => 'rgb(61,191,187)',
            'Попсовый фиолетовый' => '#9F81BB',
            'Гроздья винограда' => 'rgb(161,84,161)',
            'Чернила индиго' => 'rgb(65,73,114)',
            'Фиолетовый' => '#7267A8',
            'Стальной серый' => 'rgb(86,122,139)',
            'Стальной синий' => '#5279A3',
            'Осень' => 'rgb(244,130,32)',
            'Вечнозеленый' => '#5ECE58',
            'Глубокий бирюзовый' => 'rgb(0,134,151)',
            'Тихий океан' => '#204972',
            'Синий' => 'rgb(72,126,208)',
            'Пурпур' => '#8681D3',
            'Ярко-розовый' => 'rgb(231,25,139)',
            'Светлый розовый' => '#FF5CCD',
            'Розовый' => 'rgb(249,140,228)',
            'Лайм' => 'rgb(190,213,48)',
            'Мятное мороженое' => 'rgb(148,212,204)',
            'Сиреневый' => 'rgb(149,181,228)',
            'Лавандовый' => 'rgb(190,189,225)',
            'Мягкий зеленый' => 'rgb(153,221,188)',
        ];

        foreach ($colors as $label => $color) {
            $hex = $color[0] === '#' ? $color : ColorUtils::rgbStringToHex($color);
            Color::factory()->create([
                'label' => $label,
                'hex' => $hex
            ]);
        }
    }
}
