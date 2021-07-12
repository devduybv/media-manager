<?php

use Illuminate\Database\Seeder;
use VCComponent\Laravel\MediaManager\Entities\MediaDimension;

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
            "type"  => "post",
            "width" => 360,
            "height"=> 360,
        ]);

        MediaDimension::create([
            "model" => "post",
            "name"  => "medium",
            "type"  => "post",
            "width" => 180,
            "height"=> 180,
        ]);
        
        MediaDimension::create([
            "model" => "post",
            "name"  => "small",
            "type"  => "post",
            "width" => 90,
            "height"=> 90,
        ]);
        MediaDimension::create([
            "model" => "product",
            "name"  => "large",
            "type"  => "post",
            "width" => 360,
            "height"=> 360,
        ]);

        MediaDimension::create([
            "model" => "product",
            "name"  => "medium",
            "type"  => "post",
            "width" => 180,
            "height"=> 180,
        ]);
        
        MediaDimension::create([
            "model" => "product",
            "name"  => "small",
            "type"  => "post",
            "width" => 90,
            "height"=> 90,
        ]);
    }
}