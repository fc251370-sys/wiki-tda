<?php

test('carrera update returns a validation response for an empty name', function () {
    $response = $this->putJson('/api/carreras/1', [
        'name' => '',
        'code' => null,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('message', 'El nombre es requerido');
});
