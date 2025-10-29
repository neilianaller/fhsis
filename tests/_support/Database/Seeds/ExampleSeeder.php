<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExampleSeeder extends Seeder
{
    public function run(): void
    {
        $factories = [
            [
                'name'    => 'Test Factory',
                'uid'     => 'test001',
                'class'   => 'Factories\Tests\NewFactory',
                'icon'    => 'bi bi-puzzle-piece',
                'summary' => 'Longer sample text for testing',
            ],
            [
                'name'    => 'Widget Factory',
                'uid'     => 'widget',
                'class'   => 'Factories\Tests\WidgetPlant',
                'icon'    => 'bi bi-puzzle-piece',
                'summary' => 'Create widgets in your factory',
            ],
            [
                'name'    => 'Evil Factory',
                'uid'     => 'evil-maker',
                'class'   => 'Factories\Evil\MyFactory',
                'icon'    => 'bi bi-book-dead',
                'summary' => 'Abandon all hope, ye who enter here',
            ],
        ];

        $builder = $this->db->table('factories');

        foreach ($factories as $factory) {
            $builder->insert($factory);
        }
    }
}
