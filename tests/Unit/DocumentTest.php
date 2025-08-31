<?php
use App\Models\Category;
use App\Models\Document;

it('belongs to a category', function () {
    $category = Category::factory()->create();
    $document = Document::factory()->create([
        'category_id' => $category->id,
    ]);

    expect($document->category->is($category))->toBeTrue();
});


it('can have labels', function () {
    $document = Document::factory()->create();
    
    $document->label('done');

    expect($document->labels)->tohaveCount(1);
});