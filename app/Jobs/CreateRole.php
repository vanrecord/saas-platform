<?php

namespace App\Jobs;

use App\Events\RoleCreated;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Permission;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateRole implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $datas;
    public function __construct(array $datas)
    {
        $this->datas = $datas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RoleRepository $role, Permission $perm)
    {
        $id = $this->datas['id'];
        $names = ['name' => $this->datas['name'],'updated_by'=> auth()->user()?->id];
        if ($id) {
            $roleObj = $role->find($id);
            $role->forgetCache()->update($roleObj,$names);
        } else {
            $roleObj =  $role->create(['name' => $this->datas['name'], 'guard_name' => 'web']);
        }

        foreach ($this->datas['perms'] as $value) {
            $permission = $perm->where('name', '=', $value)->first();
            if (!$permission) {
                $perm->create(['name' => $value, 'guard_name' => 'web']);
            }
        }
        $roleObj->syncPermissions($this->datas['perms']);

        RoleCreated::dispatch();
    }
}
