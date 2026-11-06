<?php
namespace App\Http\Controllers;


use App\Actions\Central\ClearData;
use App\Actions\Central\CreateNewDomain;
use App\Actions\Central\UpdateDomain;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Package;
use App\Models\Province;
use App\Models\Sector;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\DomainRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Repositories\ProvinceRepository;

class DomainController extends Controller
{
    use PasswordValidationRules;
    protected $domain;

	public function __construct(){
        $this->domain = App(DomainRepository::class);
        $this->role = App(RoleRepository::class);
        $this->province = App(ProvinceRepository::class);
    }

	public function index(HttpRequest $request){
        $this->authorize('domain.index',Domain::class);
        $requestData = $request->all();
        $user = auth()->user();
        $list_domain =app(Domain::class)->query()->FilterUser()->get()->pluck('company','tenant_id')->toArray();
        $domains = $this->domain->getDataTable($requestData);

		return Inertia::render('Domain/Index',[
            'domains' => $domains
		])->table(function (InertiaTable $table) use ($list_domain){
            $this->domain->columnsDataTable($table,$list_domain);
        });
    }
    public function create(){
        $this->authorize('domain.create',Domain::class);
        $centralDomain = config("tenancy.tenancy_domains");
        $user = auth()->user();
        // $allUser = array_merge([$user->id],$user->parent()->pluck('id')->toArray());
        $allUser = array_merge([$user->id],$user->getDescendantsAndSelf()->pluck('id')->toArray());
        $parent_option = Domain::whereIn('user_id', $allUser)->get()->toArray();
        $bool_domain = true;
        if(empty($user->parent_id)){
            $domain = Domain::where('user_id',$user->id)->first();
            $bool_domain = $domain?true:false;
        }
        $province_option = app(Province::class)->get();
        return Inertia::render("Domain/Create",[
            'centralDomain' => $centralDomain[0],
            'boolDomain' => $bool_domain,
            'parentOption' => $parent_option,
            'provinceOption' => $province_option,
        ]);
    }

    public function store(CreateNewDomain $creator)
    {
        $this->authorize('domain.store',Domain::class);
        $domain = $creator->create(Request::all());
        if(is_array($domain) && isset($domain['success']) && $domain['success'] == false){
            return Redirect::back()->withErrors(['errors'=> $domain['error']]);
        }
        return Redirect::route('domain.index')->with('success', 'Domain created.');
    }

    public function edit($id){
        $this->authorize('domain.edit',Domain::class);
        $domain = $this->domain->with('tenant')->find($id);
        $centralDomain = config("tenancy.tenancy_domains");
        if($domain) $domain->domain = str_replace('.'.$centralDomain[0], '', $domain->domain);
        $user = User::find($domain->created_by)??auth()->user();
        $allUser = array_merge([$user->id],$user->parent()->pluck('id')->toArray());
        $parent_option = Domain::whereIn('user_id', $allUser)->where('id','!=', $id)
                        ->orWhere('id','=',$domain->parent_id)->get()->toArray(); 
        $bool_domain = true;
        if(empty($user->parent_id)){
            $bool_domain = Domain::where('user_id',$user->id)->first()?true:false;
        }
        $province_option = app(Province::class)->get();
        return Inertia::render("Domain/Edit", [
            'centralDomain' => $centralDomain[0],
            'domain' => $domain,
            'boolDomain' => $bool_domain?true:false,
            'parentOption' => $parent_option,
            'provinceOption' => $province_option,
        ]);
    }

    public function update($id,UpdateDomain $updator)
    {
        $this->authorize('domain.edit', Domain::class);
        $request = Request::all();
        $find_domain = $this->domain->find($id);
        $find_domain->domain = $request['domain'] . '.' . config('tenancy.tenancy_domains')[0];
        $find_domain->company = $request['company'];
        $domain = $updator->update($id,$this->domain,Request::all());
        if(is_array($domain) && isset($domain['success']) && $domain['success'] == false){
            return Redirect::back()->withErrors(['errors'=> $domain['error']]);
        }
        if($find_domain->isDirty()==true && !empty($find_domain->ehealth_organization_code)) {
            $this->updateDomainNameApp($request['domain'],$find_domain->ehealth_organization_code, $request['company']);
        }
        return Redirect::route('domain.index')->with('success', 'Domain updated.');
    }

    public function clearData(ClearData $clearData){

        $request = Request::validate([
            'input' => ['required', 'max:100'],
        ]);

        $result    = true;
        $type      = Request::all()['type'];
        $domain_id = Request::all()['domain_id'];

        $domain = $this->domain->find($domain_id);
        if($domain instanceof Domain && $domain->tenant->tenancy_db_name == Request::all()['input']) {

            $this->runBackup($domain);

            $result = $clearData->clearData($domain->tenant_id, $type);
        } else {
            return redirect()->back()->withErrors([
                'error' => 'Opp! Please input the correct database.'
            ]);

        }

        return $result?Redirect::route('domain')->with('success', 'Clear data successfully.'):Redirect::route('domain')->withErrors(['error' => 'Opp! Something went wrong.']);
    }

    // private function runMaintenanceMode(){
    //      \Artisan::call('tenants:run', [
    //             'commandname' => 'down', // String
    //             '--tenants' => [$database] 
    //         ]);
    // }

    private function runBackup($client){
        config(["backup.backup.name"=>$client->domain]);

        \Artisan::call('tenants:run', [
            'commandname' => 'backup:run', // String
            '--tenants' => [$client->tenant_id]
        ]);
    }

    public function listDistrict($id){
        $province = app(Province::class)->find($id);
        $list_district = [];
        if($province){
            $list_district = $province->districts;
        }
        return Response::json($list_district);
    }

    public function destroy(){
        $this->authorize('domain.index', Domain::class);
        try {
            Request::validate([
                'input' => ['required', 'max:100'],
                'domain_id' => ['required'],
            ]);

            $domain_id = Request::all()['domain_id'];
            $input = trim(Request::all()['input']);
            $domain = $this->domain->find($domain_id);
            if($domain instanceof Domain){
                $subdomain = str_replace('.' . config('tenancy.tenancy_domains')[0], '', $domain->domain);
                $isMatch = ($domain->domain === $input || $subdomain === $input || $domain->company === $input || optional($domain->tenant)->tenancy_db_name === $input);

                if ($isMatch) {
                    if ($domain->tenant) {
                        try {
                            $domain->tenant->delete();
                        } catch (\Throwable $te) {
                            info('Tenant delete event error (ignored for single-database): ' . $te->getMessage());
                            $domain->delete();
                        }
                    } else {
                        $domain->delete();
                    }
                    return Redirect::route('domain.index')->with('success', 'Delete domain successfully.');
                } else {
                    return redirect()->back()->withErrors([
                        'error' => 'Opp! Please input the correct domain name to confirm.'
                    ]);
                }
            } else {
                return redirect()->back()->withErrors(['error' => 'Domain not found.']);
            }
        } catch (\Throwable $th){
            info('Destroy domain error: ' . $th->getMessage());
            return Redirect::route('domain.index')->withErrors(['error' => 'Opp! Something went wrong.']);
        }
    }

    public function maintenanceMode()
    {
        $tenantModel = config('tenancy.tenant_model');
        $tenantId = Request::all()['tenantId'] ?? null;
        $tenant = $tenantModel::with('domains')->find($tenantId);
        if ($tenant instanceof $tenantModel) {
            $isStopping = empty($tenant->maintenance_mode);

            if ($tenant->maintenance_mode) {
                // Start domain
                $tenant->update(['maintenance_mode' => null]);
                Domain::where('tenant_id', $tenant->id)->update(['is_active' => true]);
                $isDisable = false;
            } else {
                // Stop domain
                $tenant->putDownForMaintenance([
                    'message' => 'This domain has been stopped by the administrator and is currently offline.',
                ]);
                Domain::where('tenant_id', $tenant->id)->update(['is_active' => false]);
                $isDisable = true;
            }

            // Sync with e-Health if configured
            try {
                $orgCode = $tenant->domains[0]->ehealth_organization_code ?? null;
                if (!empty($orgCode)) {
                    $payload = [
                        'organisation_code' => $orgCode,
                        'is_disable' => $isDisable,
                    ];
                    $encData = aes_encryption(json_encode($payload));
                    Http::ehealth()->timeout(3)->post('/api/bms/v1/organisation/update-status', $encData);
                }
            } catch (\Throwable $e) {
                info('eHealth status update notice: ' . $e->getMessage());
            }

            return Redirect::route('domain.index');
        }
        return Redirect::route('domain.index')->withErrors(['error' => 'Opp! Something went wrong.']);
    }

    public function Backup()
    {
        $domain_id = Request::all()['domain_id'];
        $domain = $this->domain->find($domain_id);
        if($domain instanceof Domain){
            $this->runBackup($domain);
            return Redirect::route('domain.index');
        }
        return Redirect::route('domain.index')->withErrors(['error' => 'Opp! Wrong domain.']);
    }

    public function updateDomainNameApp($domain_name,$ehealth_organization_code,$company_name)
    {
        $data=[
            'english_name'     => $company_name,
            'organisation_code'=> $ehealth_organization_code,
            'domain' => \Request::getScheme().'://'.$domain_name. '.' . config('tenancy.tenancy_domains')[0]
        ];
        $data = json_encode($data);
        $datas = aes_encryption($data);
        $response = Http::ehealth()
                        ->post('/api/bms/v1/organisation/update',$datas);

        $datas = $response->getBody();
        $ehealth = json_decode($datas,true);
        return $ehealth;

    }
}