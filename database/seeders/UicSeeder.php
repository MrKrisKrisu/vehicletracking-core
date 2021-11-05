<?php

namespace Database\Seeders;

use App\UicCountry;
use App\UicSeries;
use App\UicType;
use Illuminate\Database\Seeder;

class UicSeeder extends Seeder {

    public function run(): void {
        $this->seedSeries();
        $this->seedTypes();
        $this->seedCountries();
    }

    private function seedSeries(): void {
        $data = [
            420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 432, 433, 434, 435, 440, 441, 442, 443, 445, 446, 450,
            452, 455, 460, 462, 463, 472, 473, 474, 479, 480, 481, 482, 483, 484, 485, 490, 493, 827, 828, 829, 841,
            843, 850, 852, 860, 863, 874, 879, 885, 1427, 1428, 1429, 1430, 1440, 1441, 1442, 1443, 1462, 1484, 1490,
            1827, 1828, 1829, 1830, 1841, 1843, 1862, 2429, 2442, 2443, 2462, 2463, 2829, 2863, 3427, 3429, 3442, 3443,
            3462, 3827, 3829, 3862, 8442, 8443, 8843, 9442, 9443, 9843,
        ];

        foreach($data as $series) {
            UicSeries::factory()->create(['id' => $series]);
        }
    }

    private function seedCountries() {
        $data = [
            ['10', 'Finnland'],
            ['20', 'Russland'],
            ['21', 'Belarus'],
            ['22', 'Ukraine'],
            ['23', 'Moldau'],
            ['24', 'Litauen'],
            ['25', 'Lettland'],
            ['26', 'Estland'],
            ['27', 'Kasachstan'],
            ['28', 'Georgien'],
            ['29', 'Usbekistan'],
            ['30', 'Nordkorea'],
            ['31', 'Mongolei'],
            ['32', 'Vietnam'],
            ['33', 'China'],
            ['40', 'Kuba'],
            ['41', 'Albanien'],
            ['42', 'Japan'],
            ['49', 'Bosnien und Herzegowina'],
            ['50', 'DDR (bis 1993)'],
            ['51', 'Polen'],
            ['52', 'Bulgarien'],
            ['53', 'Rumänien'],
            ['54', 'Tschechien'],
            ['55', 'Ungarn'],
            ['56', 'Slowakei'],
            ['57', 'Aserbaidschan'],
            ['58', 'Armenien'],
            ['59', 'Kirgisistan'],
            ['60', 'Irland'],
            ['61', 'Südkorea'],
            ['62', 'Montenegro'],
            ['65', 'Nordmazedonien'],
            ['66', 'Tadschikistan'],
            ['67', 'Turkmenistan'],
            ['68', 'Afghanistan'],
            ['70', 'Vereinigtes Königreich'],
            ['71', 'Spanien'],
            ['72', 'Serbien'],
            ['73', 'Griechenland'],
            ['74', 'Schweden'],
            ['75', 'Türkei'],
            ['76', 'Norwegen'],
            ['78', 'Kroatien'],
            ['79', 'Slowenien'],
            ['80', 'Deutschland (vor 1990 nur BRD bzw. Westdeutschland)'],
            ['81', 'Österreich'],
            ['82', 'Luxemburg'],
            ['83', 'Italien'],
            ['84', 'Niederlande'],
            ['85', 'Schweiz'],
            ['86', 'Dänemark'],
            ['87', 'Frankreich'],
            ['88', 'Belgien'],
            ['90', 'Ägypten'],
            ['91', 'Tunesien'],
            ['92', 'Algerien'],
            ['93', 'Marokko'],
            ['94', 'Portugal'],
            ['95', 'Israel'],
            ['96', 'Iran'],
            ['97', 'Syrien'],
            ['98', 'Libanon'],
            ['99', 'Irak'],
        ];

        foreach($data as $row) {
            UicCountry::factory()->create([
                                              'id'          => $row[0],
                                              'description' => $row[1],
                                          ]);
        }
    }

    private function seedTypes(): void {
        $data = [
            ['90', 'Sonstige'],
            ['91', 'Elektrische Lokomotive'],
            ['92', 'Diesellokomotive'],
            ['93', 'Elektrischer Triebzug (Hochgeschwindigkeitszug)'],
            ['94', 'Elektrischer Triebzug (außer Hochgeschwindigkeitszug)'],
            ['95', 'Dieseltriebzug'],
            ['96', 'Spezieller Beiwagen'],
            ['97', 'Elektrische Rangierlok'],
            ['98', 'Dieselrangierlokomotive'],
            ['99', 'Sonderfahrzeug'],
        ];

        foreach($data as $row) {
            UicType::factory()->create([
                                           'id'          => $row[0],
                                           'description' => $row[1],
                                       ]);
        }
    }
}
