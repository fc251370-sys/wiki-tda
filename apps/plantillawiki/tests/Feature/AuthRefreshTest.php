<?php

test('refresh rejects requests without a bearer token', function () {
    $this->postJson('/api/auth/refresh')
        ->assertUnauthorized()
        ->assertJsonPath('message', 'La sesión expiró. Inicia sesión nuevamente.');
});

test('refresh rejects an invalid bearer token', function () {
    $this->withToken('invalid-token')
        ->postJson('/api/auth/refresh')
        ->assertUnauthorized();
});
