<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->search($request->search)
            ->notMe()
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::query()->uuid($id)->firstOrFail();

        return view('user.form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::query()->uuid($id)->firstOrFail();
        $user->fill($request->all());

        if (! empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.edit', [$id])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Check if user exists
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(Request $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Configuration
     *
     * @return \Illuminate\Http\Response
     */
    public function config()
    {
        return view('user.config');
    }

    /**
     * Update config
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateConfig(Request $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->getAuthPassword())) {
            AlertService::alertFail(__('alert.invalidCurrentPassword'));

            return response()->json(['success' => false, 'code' => 'invalid_password'], 400);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        AlertService::alertSuccess(__('alert.processSuccessfully'));

        return response()->json(['success' => true, 'redirect' => route('user.config')]);
    }
}
