<?php 

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use \Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait ReadFullPublic
{
    use ReadFull;

    /**
     * @param Request $request
     * @param         $record_id
     *
     * @return Response
     */
    public function readFullPublic(Request $request, $record_id): Response
    {
        return $this->readFull($request, $record_id, true);
    }
}

