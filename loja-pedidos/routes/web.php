<?php

use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pedidos');
Route::middleware('auth.basic')->group(function () {
    Route::get('/pedidos', [PedidoController::class, 'index'])
        ->name('pedidos.index');
    Route::patch('/pedidos/{pedido}/status', [PedidoController::class, 'atualizarStatus'])
        ->name('pedidos.status');

    Route::resource('produtos', ProdutoController::class)->except('show');

    Route::get('/configuracoes', [ConfiguracaoController::class, 'edit'])
        ->name('configuracoes.edit');
    Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])
        ->name('configuracoes.update');
});
