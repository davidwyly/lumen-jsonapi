<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use \Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait ReadPublic
{
    use Read;

    /**
     * @param Request $request
     * @param         $record_id
     *
     * @return Response
     */
    public function readPublic(Request $request, $record_id): Response
    {
        return $this->read($request, $record_id, true);
    }
}

