<?php 
namespace Scy\Core\FileManager\Middleware;

class BtvAuth
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('btv_user')) {
            return redirect('/btv/scy/filemanager/login');
        }

        return $next($request);
    }
}