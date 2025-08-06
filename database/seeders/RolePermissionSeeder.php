<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions based on requirements
        $permissions = [
            // Proposal permissions
            'create_proposal',
            'view_proposal',
            'edit_proposal',
            'submit_proposal',
            'approve_proposal',
            'reject_proposal',

            // Team management
            'manage_team_members',

            // Document upload
            'upload_proposal_document',
            'upload_final_report',
            'upload_research_output',
            'upload_presentation_material',

            // Review permissions
            'assign_reviewer',
            'conduct_review',
            'view_review_results',

            // Research scheme management
            'create_research_scheme',
            'edit_research_scheme',
            'delete_research_scheme',
            'manage_registration_period',

            // Funding management
            'confirm_fund_stage_1',
            'confirm_fund_stage_2',
            'confirm_fund_stage_3',

            // Monitoring
            'confirm_monitoring_1',
            'confirm_monitoring_2',

            // Output verification
            'verify_research_output',
            'approve_publication_funding',

            // Seminar management
            'schedule_seminar',
            'manage_seminar',

            // Reporting
            'view_dashboard',
            'export_data',
            'view_statistics',

            // User management
            'manage_users',
            'view_all_proposals',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Dosen Peneliti Role
        $dosenRole = Role::create(['name' => 'dosen']);
        $dosenRole->givePermissionTo([
            'create_proposal',
            'view_proposal',
            'edit_proposal',
            'submit_proposal',
            'manage_team_members',
            'upload_proposal_document',
            'upload_final_report',
            'upload_research_output',
            'upload_presentation_material',
        ]);

        // Admin LPPM Role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Reviewer Eksternal Role
        $reviewerRole = Role::create(['name' => 'reviewer']);
        $reviewerRole->givePermissionTo([
            'conduct_review',
            'view_proposal',
        ]);

        // Pimpinan Fakultas Role
        $pimpinanRole = Role::create(['name' => 'pimpinan']);
        $pimpinanRole->givePermissionTo([
            'view_dashboard',
            'view_statistics',
            'view_all_proposals',
        ]);
    }
}
