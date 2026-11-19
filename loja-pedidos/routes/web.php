<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\SomenteAdmin;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pedidos');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'formulario'])->name('login');
    Route::post('/login', [AuthController::class, 'entrar'])->name('login.entrar');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'sair'])->name('logout');

    Route::get('/pedidos', [PedidoController::class, 'index'])
        ->name('pedidos.index');
    Route::patch('/pedidos/{pedido}/status', [PedidoController::class, 'atualizarStatus'])
        ->name('pedidos.status');
    Route::post('/loja/pausa', [LojaController::class, 'alternarPausa'])
        ->name('loja.pausa');

    Route::middleware(SomenteAdmin::class)->group(function () {
        Route::get('/relatorios', [RelatorioController::class, 'index'])
            ->name('relatorios.index');

        Route::get('/avaliacoes', [AvaliacaoController::class, 'index'])
            ->name('avaliacoes.index');

        Route::resource('produtos', ProdutoController::class)->except('show');

        Route::get('/configuracoes', [ConfiguracaoController::class, 'edit'])
            ->name('configuracoes.edit');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])
            ->name('configuracoes.update');
        Route::post('/configuracoes/codigo', [ConfiguracaoController::class, 'novoCodigo'])
            ->name('configuracoes.codigo');
        Route::delete('/configuracoes/vendedores/{vendedor}', [ConfiguracaoController::class, 'removerVendedor'])
            ->name('configuracoes.vendedores.remover');

        Route::resource('usuarios', UsuarioController::class)->except('show');
    });
});
