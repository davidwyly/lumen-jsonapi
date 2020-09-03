<?php

declare(strict_types=1);

namespace LumenToolkit\Http\Controllers\Traits;

use LumenToolkit\Http\Status;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LumenToolkit\Models\DataModel;
use LumenToolkit\Models\User;

trait CognitoAuth
{
    protected string $cognito_user_group_with_auth_bypass = 'admin';

    /**
     * @param Request $request
     * @param         $user_id
     *
     * @return bool
     * @throws Exception
     */
    protected function isUser(Request $request, $user_id)
    {
        if (!is_numeric($user_id)) {
            throw new Exception("User id must be numeric", Status::BAD_REQUEST);
        }

        if ($this->isAdmin($request)) {
            return true;
        }

        $user_exists = User::whereKey($user_id)->exists();

        if (!$user_exists) {
            return false;
        }

        $authorized_user_id = (string)$user_id;
        $request_user_id    = $this->getUserId($request);

        if ($authorized_user_id !== $request_user_id) {
            return false;
        }

        return true;
    }

    /**
     * @param Request          $request
     * @param string|DataModel $data_model_class
     * @param                  $record_id
     *
     * @return bool
     * @throws Exception
     */
    protected function isResourceAuthorized(
        Request $request,
        string $data_model_class,
        $record_id
    ): bool {

        if (getenv('APP_ENV') == "local"
            || $this->isAdmin($request)
        ) {
            return true;
        }

        if (!class_exists($data_model_class)) {
            throw new Exception("Class '$data_model_class' does not exist'", Status::INTERNAL_SERVER_ERROR);
        };

        if (empty($data_model_class::$authorization_paths)) {
            throw new Exception('No authorization paths', Status::UNAUTHORIZED);
        }

        foreach ($data_model_class::$authorization_paths as $authorization_path) {
            $user = User::whereHas($authorization_path, function (Builder $query) use ($record_id) {
                $query->whereKey($record_id);
            })->first();
            if (!empty($user)) {
                break;
            }
        }

        if (empty($user)) {
            throw new Exception("Problem authorizing users to " . get_called_class(),
                Status::INTERNAL_SERVER_ERROR);
        }

        $authorized_user_id = $user->id;
        $request_user_id    = $this->getUserId($request);

        if ($authorized_user_id != $request_user_id) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @param array   $authorization_paths
     * @param         $record_id
     * @param bool    $allow_banned
     *
     * @return bool
     * @throws Exception
     */
    protected function isUserAuthorized(
        Request $request,
        array $authorization_paths,
        $record_id,
        bool $allow_banned = false
    ): bool {

        if (getenv('APP_ENV') == "local"
            || $this->isAdmin($request)
        ) {
            return true;
        }

        $request_user_id = $this->getUserId($request);

        foreach ($authorization_paths as $authorization_path) {

            if (is_array($authorization_path)) {
                $authorization_path = implode('.', $authorization_path);
            }

            /** @var User[] $authorized_users */
            $authorized_users = User::whereHas($authorization_path, function (Builder $query) use ($record_id) {
                $query->whereKey($record_id);
            })->get();

            foreach ($authorized_users as $authorized_user) {
                if ($authorized_user->id == $request_user_id) {
                    if (!$allow_banned
                        && !empty($authorized_user->banned_at)
                    ) {
                        return false;
                    }
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throws Exception
     */
    protected function getCognitoUserId(Request $request): string
    {
        if (empty($id = $request->request->get('payload')->sub)) {
            throw new Exception("Sub id missing". Status::UNAUTHORIZED);
        }

        return $id;
    }

    /**
     * @param Request $request
     *
     * @return string
     * @throws Exception
     */
    protected function getUserId(Request $request): string
    {
        if (empty($user_id = $request->request->get('user_id'))) {
            throw new Exception('User missing', Status::UNAUTHORIZED);
        }

        return (string)$user_id;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isAdmin(Request $request): bool
    {
        if (property_exists((object)$request->request->get('payload'), 'groups')
            && in_array($this->cognito_user_group_with_auth_bypass, (array)$request->request->get('payload')->groups)
        ) {
            return true;
        }

        return false;
    }
}
