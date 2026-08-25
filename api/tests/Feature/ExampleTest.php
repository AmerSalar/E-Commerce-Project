<?php

test('the application returns a successful response', function () {
    $response = $this->get('/api/products');

    $response->assertStatus(200);
});
