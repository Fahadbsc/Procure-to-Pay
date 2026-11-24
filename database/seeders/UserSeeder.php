<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $mumbai = Company::where('code', 'ACME-MUM')->firstOrFail();
        $bangalore = Company::where('code', 'ACME-BLR')->firstOrFail();

        $users = [
            // Mumbai users
            ['Admin Mumbai', 'admin.mum@acme.test', 'ACME-MUM', ['Admin']],
            ['Requester Mumbai', 'req.mum@acme.test', 'ACME-MUM', ['Requester']],
            ['Dept Head Mumbai', 'depthead.mum@acme.test', 'ACME-MUM', ['Dept Head', 'Requester']],
            ['Alternate Dept Head Mumbai', 'depthead.backup.mum@acme.test', 'ACME-MUM', ['Dept Head']],
            ['IT Head Mumbai', 'ithead.mum@acme.test', 'ACME-MUM', ['IT Head']],
            ['Finance Head Mumbai', 'finhead.mum@acme.test', 'ACME-MUM', ['Finance Head']],
            ['CFO Mumbai', 'cfo.mum@acme.test', 'ACME-MUM', ['CFO']],
            ['Procurement Mumbai', 'proc.mum@acme.test', 'ACME-MUM', ['Procurement Officer']],
            ['Store Keeper Mumbai', 'store.mum@acme.test', 'ACME-MUM', ['Store Keeper']],
            ['Accounts Mumbai', 'accounts.mum@acme.test', 'ACME-MUM', ['Accounts']],

            // Bengaluru users
            ['Admin Bengaluru', 'admin.blr@acme.test', 'ACME-BLR', ['Admin']],
            ['Requester Bengaluru', 'req.blr@acme.test', 'ACME-BLR', ['Requester']],
            ['Dept Head Bengaluru', 'depthead.blr@acme.test', 'ACME-BLR', ['Dept Head']],
            ['Alternate Dept Head Bengaluru', 'depthead.backup.blr@acme.test', 'ACME-BLR', ['Dept Head']],
            ['Procurement Bengaluru', 'proc.blr@acme.test', 'ACME-BLR', ['Procurement Officer']],
            ['IT Head Bengaluru', 'ithead.blr@acme.test', 'ACME-BLR', ['IT Head']],
            ['Finance Head Bengaluru', 'finhead.blr@acme.test', 'ACME-BLR', ['Finance Head']],
            ['CFO Bengaluru', 'cfo.blr@acme.test', 'ACME-BLR', ['CFO']],
            ['Store Keeper Bengaluru', 'store.blr@acme.test', 'ACME-BLR', ['Store Keeper']],
            ['Accounts Bengaluru', 'accounts.blr@acme.test', 'ACME-BLR', ['Accounts']],
        ];

        foreach ($users as $data) {

            [$name, $email, $companyCode, $roleNames] = $data;

            $company = $companyCode === 'ACME-MUM'
                ? $mumbai
                : $bangalore;

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'company_id' => $company->id,
                    'password' => Hash::make('password'),
                ]
            );

            $roleIds = Role::whereIn('name', $roleNames)
                ->pluck('id')
                ->toArray();

            $user->roles()->sync($roleIds);
        }

        foreach (['ACME-MUM'=>'cfo.mum@acme.test','ACME-BLR'=>'cfo.blr@acme.test'] as $companyCode=>$managerEmail) {
            $manager = User::where('email',$managerEmail)->first();
            if ($manager) User::where('company_id',$manager->company_id)->where('id','!=',$manager->id)->whereHas('roles',fn($q)=>$q->whereIn('name',['Dept Head','IT Head','Finance Head']))->update(['manager_id'=>$manager->id]);
        }
        foreach (['cfo.mum@acme.test'=>'admin.mum@acme.test','cfo.blr@acme.test'=>'admin.blr@acme.test'] as $email=>$managerEmail) {
            $cfo = User::where('email',$email)->first();
            $admin = User::where('email',$managerEmail)->first();
            if ($cfo && $admin) $cfo->update(['manager_id'=>$admin->id]);
        }
    }
}
