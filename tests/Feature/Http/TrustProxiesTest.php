<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/client-ip', fn (Request $request) => $request->ip());
});

it('resolves the visitor appended by a cloudflare hop', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '104.16.0.1'])
        ->get('/client-ip', ['X-Forwarded-For' => '203.0.113.9']);

    $response->assertOk()->assertContent('203.0.113.9');
});

it('ignores a spoofed leftmost forwarded-for entry', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '104.16.0.1'])
        ->get('/client-ip', ['X-Forwarded-For' => '198.51.100.250, 203.0.113.9']);

    $response->assertOk()->assertContent('203.0.113.9');
});

it('ignores forwarded-for from an address outside the cloudflare ranges', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->get('/client-ip', ['X-Forwarded-For' => '203.0.113.9']);

    $response->assertOk()->assertContent('198.51.100.7');
});

it('builds https links when a cloudflare hop forwards https', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '104.16.0.1'])
        ->get('/', ['X-Forwarded-Proto' => 'https']);

    $response->assertOk()
        ->assertSee('href="https://', false)
        ->assertDontSee('href="http://', false);
});

it('ignores a forwarded scheme from an address outside the cloudflare ranges', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->get('/', ['X-Forwarded-Proto' => 'https']);

    $response->assertOk()
        ->assertSee('href="http://', false)
        ->assertDontSee('href="https://', false);
});

it('ignores a forwarded host from a cloudflare hop', function () {
    $response = $this->withServerVariables(['REMOTE_ADDR' => '104.16.0.1'])
        ->get('/', ['X-Forwarded-Host' => 'example.test']);

    $response->assertOk()
        ->assertSee('href="'.url('notes').'"', false)
        ->assertDontSee('example.test');
});
