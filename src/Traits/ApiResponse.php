<?php

namespace GustavoSantarosa\LaravelToolPack\Traits;

use GustavoSantarosa\LaravelToolPack\Exceptions\ApiResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    public function okResponse($data = [], bool $index = false, ?string $message = null, ?array $include = [], array $arrayToAppend = []): JsonResponse
    {
        return response()->json(
            $this->customResponse(
                success: true,
                message: $message ?? __('messages.successfully.show'),
                data: $data,
                arrayToAppend: $arrayToAppend,
                include: $include,
                index: $index
            ),
            Response::HTTP_OK,
        );
    }

    public function notFoundResponse(?string $message = null, array $data = [], array $arrayToAppend = []): void
    {
        $this->exceptionResponse(
            $message,
            Response::HTTP_NOT_FOUND,
            $this->customResponse(
                success: false,
                message: $message ?? __('messages.errors.notfound'),
                data: $data,
                arrayToAppend: $arrayToAppend
            )
        );
    }

    public function unprocessableEntityResponse(?string $message = null, array $data = [], array $arrayToAppend = []): object
    {
        return (object) [
            'code'    => Response::HTTP_UNPROCESSABLE_ENTITY,
            'content' => $this->customResponse(
                success: false,
                message: $message ?? __('messages.errors.notfound'),
                data: $data,
                arrayToAppend: $arrayToAppend
            ),
        ];
    }

    public function internalServerErrorResponse(?string $message = null, array $data = [], array $arrayToAppend = []): object
    {
        return (object) [
            'code'    => Response::HTTP_INTERNAL_SERVER_ERROR,
            'content' => $this->customResponse(
                success: false,
                message: $message ?? __('A API está temporariamente em manutenção, tente novamente mais tarde!'),
                data: $data,
                arrayToAppend: $arrayToAppend
            ),
        ];
    }

    public function customResponse(bool $success, string $message = null, $data, array $arrayToAppend, ?array $include = [], bool $index = false)
    {
        $content = [
            'success' => $success,
            'message' => $message,
        ] + $arrayToAppend;

        if ($include) {
            $content = $content + [
                'include' => $include,
            ];
        }

        if ($data) {
            if ($index) {
                $content = $data;
            } else {
                $content = $content + [
                    'data' => $data,
                ];
            }
        }

        return $content;
    }

    public function exceptionResponse(string $message, int $code, array $content): void
    {
        throw new ApiResponseException(message: $message, code: $code, apiResponse: $content);
    }
}
