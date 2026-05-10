<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Domain;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Scopes\DomainScope;
use App\Services\IpLocationService;
use Illuminate\Support\Facades\Validator;
use App\Support\ActivityLog;

class UsersController extends Controller
{
    public function index(Request $request)
	{
        $isSuper = auth('admin')->user()?->role === 'SuperAdmin';
        $query = User::query()->with(['domain','info']);
		if ($isSuper) {
			$query->withoutGlobalScope(DomainScope::class);
            // Ensure users_info is loaded across domains for SuperAdmin
            $query->with(['info' => function ($q) {
                $q->withoutGlobalScope(DomainScope::class);
            }]);
		}
		// Filters
		if ($search = trim((string) $request->string('q'))) {
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', '%'.$search.'%')
				  ->orWhere('username', 'like', '%'.$search.'%');
			});
		}
		$from = $request->date('from');
		$to = $request->date('to');
		if ($from && $to) {
			$query->whereBetween('created_at', [
				\Carbon\Carbon::parse($from)->startOfDay(),
				\Carbon\Carbon::parse($to)->endOfDay(),
			]);
		} elseif ($from) {
			$query->where('created_at', '>=', \Carbon\Carbon::parse($from)->startOfDay());
		} elseif ($to) {
			$query->where('created_at', '<=', \Carbon\Carbon::parse($to)->endOfDay());
		}
		$users = $query
			->where('role', '!=', 'SuperAdmin')
			->orderByDesc('id')
			->paginate(15)
			->appends($request->only('q','from','to'));
		return view('admin.users.index', compact('users', 'search', 'from', 'to'));
	}

	public function create()
	{
		$isSuper = auth('admin')->user()?->role === 'SuperAdmin';
		$domains = $isSuper ? Domain::query()->orderBy('name')->get() : collect();
		return view('admin.users.create', compact('domains', 'isSuper'));
	}

	public function store(Request $request)
	{
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';
		$domainId = $isSuper
			? (int) $request->input('domain_id')
			: ($admin->domain_id ?? (app()->bound('currentDomain') ? app('currentDomain')->id : null));

		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'username' => [
				'required', 'string', 'max:255',
				Rule::unique('users', 'username')->where(function ($q) use ($domainId) {
					return $q->where('domain_id', $domainId);
				}),
			],
			'password' => ['required', 'string', 'min:6'],
			'role' => ['required', 'in:user,admin'],
            'status' => ['nullable', 'in:Active,Block'],
			'domain_id' => $isSuper ? ['required', 'exists:domains,id'] : ['nullable'],
			// Optional user info fields when role is user
			'wallet' => ['nullable', 'numeric'],
			'credit_score' => ['nullable', 'integer'],
			'withdrawal_code' => ['nullable', 'string', 'max:6'],
			'full_name' => ['nullable', 'string', 'max:255'],
			'id_card_number' => ['nullable', 'string', 'max:255'],
			'id_card_front' => ['nullable', 'image', 'max:5120'],
			'id_card_back' => ['nullable', 'image', 'max:5120'],
			'id_card_selfie' => ['nullable', 'image', 'max:5120'],
			'address' => ['nullable', 'string'],
			'company' => ['nullable', 'string', 'max:255'],
			'company_address' => ['nullable', 'string'],
			'position' => ['nullable', 'string', 'max:255'],
			'monthly_income' => ['nullable', 'numeric'],
			'email' => ['nullable', 'email', 'max:255'],
			'contact_1_person' => ['nullable', 'string', 'max:255'],
			'contact_1_phone' => ['nullable', 'string', 'max:255'],
			'contact_1_relativity' => ['nullable', 'string', 'max:255'],
			'contact_2_person' => ['nullable', 'string', 'max:255'],
			'contact_2_phone' => ['nullable', 'string', 'max:255'],
			'contact_2_relativity' => ['nullable', 'string', 'max:255'],
			'bank_name' => ['nullable', 'string', 'max:255'],
			'bank_number' => ['nullable', 'string', 'max:255'],
		]);

        $ipLocation = IpLocationService::buildIpLocationLabel($request->ip());

        $user = User::create([
			'domain_id' => $domainId,
			'name' => $request->name,
			'username' => $request->username,
			'password' => Hash::make($request->password),
			'role' => $request->role,
            'status' => $request->input('status', 'Active'),
            'ip_location' => $ipLocation,
		]);

		if ($request->role === 'user') {
			$domain = Domain::find($domainId);
			$basePath = trim(($domain->host ?? 'default') . '/profiles/' . $user->id, '/');
			$paths = [];
			foreach (['id_card_front', 'id_card_back', 'id_card_selfie'] as $field) {
				if ($request->file($field)) {
					$ext = $request->file($field)->getClientOriginalExtension() ?: 'jpg';
					$filename = $field . '-' . Str::random(8) . '.' . $ext;
					$stored = $request->file($field)->storeAs($basePath, $filename, 'public');
					$paths[$field] = $stored;
				}
			}
			UserInfo::updateOrCreate(
				['user_id' => $user->id],
				array_merge([
					'domain_id' => $domainId,
					'wallet' => $request->wallet ?? 0,
					'credit_score' => $request->credit_score,
					'withdrawal_code' => $request->withdrawal_code,
					'full_name' => $request->full_name,
					'id_card_number' => $request->id_card_number,
					'address' => $request->address,
					'email' => $request->email,
					'company' => $request->company,
					'company_address' => $request->company_address,
					'position' => $request->position,
					'monthly_income' => $request->monthly_income,
					'contact_1_person' => $request->contact_1_person,
					'contact_1_phone' => $request->contact_1_phone,
					'contact_1_relativity' => $request->contact_1_relativity,
					'contact_2_person' => $request->contact_2_person,
					'contact_2_phone' => $request->contact_2_phone,
					'contact_2_relativity' => $request->contact_2_relativity,
					'bank_name' => $request->bank_name,
					'bank_number' => $request->bank_number,
				], $paths)
			);
		}

		try {
			if ($admin) {
				ActivityLog::forAdmin($admin, 'created user', [
					'user_id' => $user->id,
					'username' => $user->username,
					'role' => $user->role,
				], $user);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.users.index')->with('success', 'User created.');
	}

    public function edit($user)
	{
        $user = $this->resolveUserForAdmin($user)->load('info');
		if ($user->role === 'SuperAdmin') {
			return redirect()->route('admin.users.index')->with('error', 'SuperAdmin cannot be edited.');
		}
		$isSuper = auth('admin')->user()?->role === 'SuperAdmin';
		$domains = $isSuper ? Domain::query()->orderBy('name')->get() : collect();
        // For SuperAdmin, reload info without domain scope so it populates regardless of currentDomain
        if ($isSuper) {
            $user->setRelation('info', \App\Models\UserInfo::withoutGlobalScope(DomainScope::class)->where('user_id', $user->id)->first());
        }
		return view('admin.users.edit', compact('user', 'domains', 'isSuper'));
	}

	public function update(Request $request, $user)
	{
		$user = $this->resolveUserForAdmin($user);
		if ($user->role === 'SuperAdmin') {
			return redirect()->route('admin.users.index')->with('error', 'SuperAdmin cannot be edited.');
		}
		$admin = auth('admin')->user();
		$isSuper = $admin?->role === 'SuperAdmin';
		$domainId = $isSuper
			? (int) $request->input('domain_id', $user->domain_id)
			: ($admin->domain_id ?? (app()->bound('currentDomain') ? app('currentDomain')->id : $user->domain_id));

		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'username' => [
				'required', 'string', 'max:255',
				Rule::unique('users', 'username')->ignore($user->id)->where(function ($q) use ($domainId) {
					return $q->where('domain_id', $domainId);
				}),
			],
			'password' => ['nullable', 'string', 'min:6'],
			'role' => ['required', 'in:user,admin'],
            'status' => ['nullable', 'in:Active,Block'],
			'domain_id' => $isSuper ? ['required', 'exists:domains,id'] : ['nullable'],
			// Optional user info fields when role is user
			'wallet' => ['nullable', 'numeric'],
			'credit_score' => ['nullable', 'integer'],
			'withdrawal_code' => ['nullable', 'string', 'max:12'],
			'full_name' => ['nullable', 'string', 'max:255'],
			'id_card_number' => ['nullable', 'string', 'max:255'],
			'id_card_front' => ['nullable', 'image', 'max:5120'],
			'id_card_back' => ['nullable', 'image', 'max:5120'],
			'id_card_selfie' => ['nullable', 'image', 'max:5120'],
			'address' => ['nullable', 'string'],
			'company' => ['nullable', 'string', 'max:255'],
			'company_address' => ['nullable', 'string'],
			'position' => ['nullable', 'string', 'max:255'],
			'monthly_income' => ['nullable', 'numeric'],
			'email' => ['nullable', 'email', 'max:255'],
			'contact_1_person' => ['nullable', 'string', 'max:255'],
			'contact_1_phone' => ['nullable', 'string', 'max:255'],
			'contact_1_relativity' => ['nullable', 'string', 'max:255'],
			'contact_2_person' => ['nullable', 'string', 'max:255'],
			'contact_2_phone' => ['nullable', 'string', 'max:255'],
			'contact_2_relativity' => ['nullable', 'string', 'max:255'],
			'bank_name' => ['nullable', 'string', 'max:255'],
			'bank_number' => ['nullable', 'string', 'max:255'],
		]);

		$data = [
			'domain_id' => $domainId,
			'name' => $request->name,
			'username' => $request->username,
			'role' => $request->role,
            'status' => $request->input('status', $user->status ?? 'Active'),
		];
		if ($request->filled('password')) {
			$data['password'] = Hash::make($request->password);
		}

		$user->update($data);

		if ($request->role === 'user') {
			$domain = Domain::find($domainId);
			$basePath = trim(($domain->host ?? 'default') . '/profiles/' . $user->id, '/');
			$paths = [];
			foreach (['id_card_front', 'id_card_back', 'id_card_selfie'] as $field) {
				if ($request->file($field)) {
					$ext = $request->file($field)->getClientOriginalExtension() ?: 'jpg';
					$filename = $field . '-' . Str::random(8) . '.' . $ext;
					$stored = $request->file($field)->storeAs($basePath, $filename, 'public');
					$paths[$field] = $stored;
				}
			}
			UserInfo::updateOrCreate(
				['user_id' => $user->id],
				array_merge([
					'domain_id' => $domainId,
					'wallet' => $request->wallet ?? 0,
					'credit_score' => $request->credit_score,
					'withdrawal_code' => $request->withdrawal_code,
					'full_name' => $request->full_name,
					'id_card_number' => $request->id_card_number,
					'address' => $request->address,
					'email' => $request->email,
					'company' => $request->company,
					'company_address' => $request->company_address,
					'position' => $request->position,
					'monthly_income' => $request->monthly_income,
					'contact_1_person' => $request->contact_1_person,
					'contact_1_phone' => $request->contact_1_phone,
					'contact_1_relativity' => $request->contact_1_relativity,
					'contact_2_person' => $request->contact_2_person,
					'contact_2_phone' => $request->contact_2_phone,
					'contact_2_relativity' => $request->contact_2_relativity,
					'bank_name' => $request->bank_name,
					'bank_number' => $request->bank_number,
				], $paths)
			);
		}

		try {
			$admin = auth('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'updated user', [
					'user_id' => $user->id,
					'username' => $user->username,
				], $user);
			}
		} catch (\Throwable $e) {}

		return redirect()->route('admin.users.index')->with('success', 'User updated.');
	}

	public function destroy($user)
	{
		$user = $this->resolveUserForAdmin($user);
		if ($user->role === 'SuperAdmin') {
			return redirect()->route('admin.users.index')->with('error', 'SuperAdmin cannot be deleted.');
		}
		$user->delete();
		try {
			$admin = auth('admin')->user();
			if ($admin) {
				ActivityLog::forAdmin($admin, 'deleted user', [
					'user_id' => $user->id,
					'username' => $user->username,
				], $user);
			}
		} catch (\Throwable $e) {}
		return redirect()->route('admin.users.index')->with('success', 'User deleted.');
	}

    // --- Quick edit endpoints ---
    public function quickWallet(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate(['wallet' => ['required', 'numeric']]);
        UserInfo::updateOrCreate(['user_id' => $user->id], [
            'domain_id' => $user->domain_id,
            'wallet' => $data['wallet'],
        ]);
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'updated user wallet', [
                    'user_id' => $user->id,
                    'wallet' => $data['wallet'],
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'Wallet updated.');
    }

    public function quickWithdrawalCode(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate(['withdrawal_code' => ['required', 'string', 'max:6']]);
        UserInfo::updateOrCreate(['user_id' => $user->id], [
            'domain_id' => $user->domain_id,
            'withdrawal_code' => $data['withdrawal_code'],
        ]);
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'updated user withdrawal code', [
                    'user_id' => $user->id,
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'Withdrawal code updated.');
    }

    public function quickScore(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate(['credit_score' => ['required', 'integer']]);
        UserInfo::updateOrCreate(['user_id' => $user->id], [
            'domain_id' => $user->domain_id,
            'credit_score' => $data['credit_score'],
        ]);
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'updated user credit score', [
                    'user_id' => $user->id,
                    'credit_score' => $data['credit_score'],
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'Credit score updated.');
    }

    public function quickId(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'id_card_number' => ['required', 'string', 'max:255'],
        ]);
        UserInfo::updateOrCreate(['user_id' => $user->id], [
            'domain_id' => $user->domain_id,
            'full_name' => $data['full_name'],
            'id_card_number' => $data['id_card_number'],
        ]);
        // Also sync user's display name
        $user->name = $data['full_name'];
        $user->save();
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'updated user id', [
                    'user_id' => $user->id,
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'ID details updated.');
    }

    public function quickPassword(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $user->password = Hash::make($data['password']);
        $user->save();
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'change password', [
                    'user_id' => $user->id,
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'Password updated.');
    }

    public function quickBank(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        $data = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_number' => ['nullable', 'string', 'max:255'],
        ]);
        UserInfo::updateOrCreate(['user_id' => $user->id], [
            'domain_id' => $user->domain_id,
            'bank_name' => $data['bank_name'] ?? '',
            'bank_number' => $data['bank_number'] ?? '',
        ]);
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'updated user bank', [
                    'user_id' => $user->id,
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'Bank info updated.');
    }

    protected function authorizeUser(User $user): void
    {
        // Prevent operating on SuperAdmin
        if ($user->role === 'SuperAdmin') {
            abort(403, 'SuperAdmin cannot be modified.');
        }
    }

    /**
     * Resolve a user record for the acting admin.
     * - SuperAdmin: bypass domain scope (can access any domain)
     * - Admin: restricted to current domain via global scope
     */
    protected function resolveUserForAdmin($id): User
    {
        $isSuper = auth('admin')->user()?->role === 'SuperAdmin';
        if ($isSuper) {
            return User::withoutGlobalScope(DomainScope::class)->findOrFail($id);
        }
        return User::query()->findOrFail($id);
    }

    public function quickDisable(Request $request, $user)
    {
        $user = $this->resolveUserForAdmin($user);
        $this->authorizeUser($user);
        // No additional validation; it's a simple status flip
        $user->status = 'Block';
        $user->save();
        try {
            $admin = auth('admin')->user();
            if ($admin) {
                ActivityLog::forAdmin($admin, 'disabled user', [
                    'user_id' => $user->id,
                ], $user);
            }
        } catch (\Throwable $e) {}
        return back()->with('success', 'User has been disabled.');
    }
}


