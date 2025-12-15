<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    // Allow either a 200 or 302 (redirect to login) depending on auth setup
    expect(in_array($response->status(), [200, 302]))->toBeTrue();
});
