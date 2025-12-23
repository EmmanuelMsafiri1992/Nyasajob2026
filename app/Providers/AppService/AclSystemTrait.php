<?php
namespace App\Providers\AppService;

use App\Models\Permission;

trait AclSystemTrait
{
	/**
	 * Setup ACL system
	 * Check & Migrate Old admin authentication to ACL system
	 */
	private function setupAclSystem()
	{
		if (isAdminPanel()) {
			// Check & Fix the default Permissions
			if (!Permission::checkDefaultPermissions()) {
				Permission::resetDefaultPermissions();
			}
		}
	}
}
