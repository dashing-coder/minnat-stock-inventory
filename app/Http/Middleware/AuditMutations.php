<?php
namespace App\Http\Middleware;
use App\Models\ActivityLog;use Closure;use Illuminate\Http\Request;use Symfony\Component\HttpFoundation\Response;
class AuditMutations
{
    public function handle(Request $request, Closure $next): Response
    {
        $response=$next($request);
        if($request->user() && !in_array($request->method(),['GET','HEAD','OPTIONS'],true)){
            $payload=$request->except(['password','password_confirmation','_token']);
            array_walk_recursive($payload,function(&$v){if(is_string($v)&&strlen($v)>500)$v=substr($v,0,500).'…';});
            ActivityLog::create(['user_id'=>$request->user()->id,'action'=>strtolower($request->method()),'route_name'=>$request->route()?->getName(),'method'=>$request->method(),'url'=>$request->fullUrl(),'payload'=>$payload,'ip_address'=>$request->ip(),'user_agent'=>substr((string)$request->userAgent(),0,1000)]);
        }
        return $response;
    }
}
