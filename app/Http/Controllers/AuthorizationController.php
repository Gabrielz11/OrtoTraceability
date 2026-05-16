<?php

namespace App\Http\Controllers;

use App\Modules\Authorization\Application\Services\AuthorizationService;
use App\Modules\Authorization\Domain\Models\Authorization;
use App\Modules\Authorization\Domain\Models\AuthorizationItem;
use App\Modules\Surgery\Domain\Models\Surgery;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function __construct(
        private readonly AuthorizationService $service,
    ) {}

    public function create(Surgery $surgery)
    {
        return view('authorizations.create', compact('surgery'));
    }

    public function store(Request $request, Surgery $surgery)
    {
        $validated = $request->validate([
            'plano_saude'          => 'required|string',
            'codigo_autorizacao'   => 'nullable|string',
            'data_autorizacao'     => 'nullable|date',
            'validade_autorizacao' => 'nullable|date',
            'status'               => 'required|in:nao_recebida,recebida,parcial,vencida',
            'observacoes'          => 'nullable|string',
        ]);

        $this->service->store($surgery, $validated);

        return redirect()->route('surgeries.show', $surgery)
            ->with('success', 'Autorização cadastrada.');
    }

    public function update(Request $request, Authorization $authorization)
    {
        $validated = $request->validate([
            'plano_saude'          => 'required|string',
            'codigo_autorizacao'   => 'nullable|string',
            'data_autorizacao'     => 'nullable|date',
            'validade_autorizacao' => 'nullable|date',
            'status'               => 'required|in:nao_recebida,recebida,parcial,vencida',
            'observacoes'          => 'nullable|string',
        ]);

        $this->service->update($authorization, $validated);

        return redirect()->route('surgeries.show', $authorization->surgery_id)
            ->with('success', 'Autorização atualizada.');
    }

    public function addItem(Request $request, Authorization $authorization)
    {
        $request->validate([
            'codigo_produto'        => 'nullable|string',
            'descricao_produto'     => 'required|string',
            'quantidade_autorizada' => 'required|integer|min:1',
            'valor_unitario'        => 'nullable|numeric',
            'coberto'               => 'boolean',
        ]);

        $this->service->addItem($authorization, $request->only([
            'codigo_produto', 'descricao_produto',
            'quantidade_autorizada', 'valor_unitario', 'coberto',
        ]));

        return back()->with('success', 'Item adicionado à autorização.');
    }

    public function removeItem(Authorization $authorization, AuthorizationItem $item)
    {
        $this->service->removeItem($item);
        return back()->with('success', 'Item removido.');
    }
}
