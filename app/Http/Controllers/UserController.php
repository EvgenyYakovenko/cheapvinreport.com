<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Отобразить список всех пользователей
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Фильтр по роли
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Фильтр по балансу
        if ($request->filled('balance_from')) {
            $query->where('report_balance', '>=', $request->get('report_balance_from'));
        }
        if ($request->filled('balance_to')) {
            $query->where('report_balance', '<=', $request->get('report_balance_to'));
        }

        // Фильтр по дате регистрации
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Получаем общую статистику для всех пользователей (без фильтров)
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();

        return view('users.index', compact('users', 'totalUsers', 'adminUsers', 'regularUsers'));
    }

    /**
     * Отобразить детали пользователя
     */
    public function show($id): View
    {
        $user = User::with(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Обновить данные пользователя
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'report_balance' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            if ($request->has('report_balance')) {
                $user->report_balance = (int) $request->get('report_balance');
            }

            $user->save();

//            Log::info('UserController: User updated', [
//                'user_id' => $user->id,
//                'updated_fields' => $request->only(['report_balance']),
//            ]);

            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно обновлен',
                'user' => [
                    'id' => $user->id,
                    'report_balance' => $user->report_balance,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('UserController: Failed to update user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ошибка при обновлении пользователя: '.$e->getMessage(),
            ], 500);
        }
    }
}
