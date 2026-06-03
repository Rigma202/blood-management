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

                $plainPassword = $data['password'];
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($plainPassword),
                    'role' => 'staff',
                ]);
                $user->bloodBanks()->attach(
                    $data['blood_bank_id']
                );
                Mail::to($user->email)
                    ->queue(
                        new StaffCredentialsMail(
                            $user,
                            $plainPassword
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
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }
}
