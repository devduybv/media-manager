<?php

use Illuminate\Database\Seeder;
use VCComponent\Laravel\Menu\Entities\MediaDimension;

class MediaDimentionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MediaDimension::create([
            "model" => "post",
            "name"  => "large",
            "width" => 360,
            "height"=> 360,
        ]);

        MediaDimension::create([
            "model" => "post",
            "name"  => "medium",
            "width" => 180,
            "height"=> 180,
        ]);
        
        MediaDimension::create([
            "model" => "post",
            "name"  => "small",
            "width" => 90,
            "height"=> 90,
        ]);
        MediaDimension::create([
            "model" => "product",
            "name"  => "large",
            "width" => 360,
            "height"=> 360,
        ]);

        MediaDimension::create([
            "model" => "product",
            "name"  => "medium",
            "width" => 180,
            "height"=> 180,
        ]);
        
        MediaDimension::create([
            "model" => "product",
            "name"  => "small",
            "width" => 90,
            "height"=> 90,
        ]);
    }
}