<?php

it('returns 404 for / when no route is defined', function () {
    $this->get('/')
        ->assertNotFound(); // same as ->assertStatus(404)
});
