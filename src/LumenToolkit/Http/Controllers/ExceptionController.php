<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{
    /**
     * @param Request   $request
     * @param Exception $exception
     *
     * @return Response
     */
    public function render(Request $request, Exception $exception): Response
    {
        return $this->renderFail($request, $exception);
    }
}
