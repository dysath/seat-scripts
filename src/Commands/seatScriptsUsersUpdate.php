<?php
/**
 * User: Denngarr B'tarn <ed.stafford@gmail.com>
 * Date: 2017/11/17
 * Time: 18:03
 */

namespace Denngarr\Seat\SeatScripts\Commands;

use DB;
use Illuminate\Console\Command;
use Seat\Web\Models\User;
use Seat\Web\Models\Group;
use Seat\Web\Models\Acl\Role;
use Seat\Web\Models\Acl\GroupRole;
use Seat\Web\Acl\AccessManager;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

class seatScriptsUsersUpdate extends Command
{
    use AccessManager;

    protected $signature = 'seat-scripts:users:corproles';

    protected $description = 'Sync users with Seat Roles by corporation';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $corp_list = [];
        $all_corps = CorporationInfo::all();

        // Get the List of Roles with the same names as the corporations
        foreach ($all_corps as $corp) {
            $role = Role::where('title', $corp->name)->first();
            if ($role != null) {
                $corp_list[$corp->name] = $corp->corporation_id;
            }
        }
        
        // Get the Group_IDs for each character/user
        foreach ($corp_list as $corp => $id) {
            $corpgroups = [];
            $rolegroups = [];
            $characters = CharacterInfo::where('corporation_id', $id)->get();
            foreach ($characters as $character) {
                $user = User::where('id', $character->character_id)->first();
                if ($user != null) {
                    array_push($corpgroups, $user->group_id);
                }
            }
            // Uniq the groups.  Only need one.
            $corpgroups = array_unique($corpgroups);

            $role = Role::where('title', $corp)->first();
            $grouproles = $role->groups;

            foreach ($grouproles as $grouprole) {
              array_push($rolegroups, $grouprole->id);
            }

            $add_groups = array_diff($corpgroups, $rolegroups);
            $remove_groups = array_diff($rolegroups, $corpgroups);

            foreach ($add_groups as $group) {
                $role->groups()->attach($group);
            }

            foreach ($remove_groups as $group) {
                $role->groups()->detach($group);
            }
        }
    }
}

