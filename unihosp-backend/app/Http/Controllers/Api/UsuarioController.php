<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with('roles')
            ->latest('created_at')
            ->paginate((int) $request->integer('per_page', 15));

        return response()->json([
            'data' => UserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $dados = $request->validated();
        $roles = $dados['roles'] ?? [];
        unset($dados['roles']);

        $user = User::query()->create($dados);
        $user->syncRoles($roles);

        return response()->json([
            'data' => new UserResource($user->fresh()->load('roles')),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $user): JsonResponse
    {
        $dados = $request->validated();
        $roles = $dados['roles'] ?? null;
        unset($dados['roles']);

        if (empty($dados['password'])) {
            unset($dados['password']);
        }

        $user->update($dados);

        if (is_array($roles)) {
            $user->syncRoles($roles);
        }

        return response()->json([
            'data' => new UserResource($user->fresh()->load('roles')),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuário removido com sucesso.',
        ]);
    }

    public function roles(): JsonResponse
    {
        return response()->json([
            'data' => Role::query()->orderBy('name')->pluck('name'),
        ]);
    }
}
