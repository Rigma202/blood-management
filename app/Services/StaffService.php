<?php

namespace App\Services;
use App\Mail\StaffCredentialsMail;
use App\Models\User;
use App\Models\BloodBank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
class StaffService
{
    public function getAllStaff()
    {
        return User::with('bloodBanks')->where('role', 'staff')->paginate(10);
    }
    public function getAllBloodbanks()
    {
        return BloodBank::all();//addstatuschecking
    }
    public function create(array $data)
    {
            DB::beginTransaction();
            try {

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => "Staff@123",
                    'role' => 'staff',
                ]);
                $user->bloodBanks()->attach(
                    $data['blood_bank_id']
                );
                Mail::to($user->email)
                    ->queue(
                        new StaffCredentialsMail(
                            $user,
                            "Staff@123"
                        )
                    );

                DB::commit();
                return $user;
            } catch (\Exception $e) {

                DB::rollBack();
                throw $e;
            }
        }

    public function update(User $user, array $data)
    {
        $bloodBankIds = $data['blood_bank_id'] ?? [];
        unset($data['blood_bank_id']);
        $user->update($data);
        $user->bloodBanks()->sync($bloodBankIds);
        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }
    public function getAssignedBloodBanks($id)
    {
        BloodBank::whereHas('users', fn($q) => $q->where('id', $id))
            ->select('blood_banks.name', 'blood_banks.location')
            ->get();
    }
     public function getStaffWithBloodBanks($id)
    {
        return User::with('bloodBanks')
            ->findOrFail($id);
    }
    public function getBloodBanksWithActiveRefrigerators(array $bloodBankIds)
    {
        return BloodBank::whereIn('id', $bloodBankIds)
            ->whereHas('refrigerators', fn ($q) => $q->where('is_active', true))
            ->get();
    }
}
