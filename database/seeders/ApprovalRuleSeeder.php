<?php

namespace Database\Seeders;

use App\Models\ApprovalRule;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalRuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['ACME-MUM', 'ACME-BLR'] as $companyCode) {
            $company = Company::where('code', $companyCode)->firstOrFail();
            $user = fn (string $role) => User::where('company_id', $company->id)
                ->whereHas('roles', fn ($q) => $q->where('name', $role))->value('id');
            $specs = [
                ['PR',0,50000,null,['Dept Head']],
                ['PR',50000.01,500000,null,['Dept Head','Finance Head']],
                ['PR',500000.01,null,null,['Dept Head','Finance Head','CFO']],
                ['PR',0,50000,'IT',['Dept Head','IT Head']],
                ['PR',50000.01,500000,'IT',['Dept Head','IT Head','Finance Head']],
                ['PR',500000.01,null,'IT',['Dept Head','IT Head','Finance Head','CFO']],
                ['PO',0,200000,null,['Finance Head']],
                ['PO',200000.01,null,null,['Finance Head','CFO']],
            ];
            foreach ($specs as [$type,$min,$max,$category,$roles]) {
                $approvers = array_map($user, $roles);
                if (in_array(null,$approvers,true)) continue;
                $rule = ApprovalRule::updateOrCreate([
                    'company_id'=>$company->id,'document_type'=>$type,'category'=>$category,
                    'cost_centre_id'=>null,'min_amount'=>$min,'max_amount'=>$max,
                ],['is_active'=>true]);
                $rule->steps()->delete();
                foreach ($approvers as $i=>$id) $rule->steps()->create(['step_order'=>$i+1,'approver_user_id'=>$id]);
            }
        }
    }
}
