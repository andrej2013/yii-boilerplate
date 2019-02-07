<?php

namespace andrej2013\yiiboilerplate\components;


use Yii;

/**
 * Class User.
 *
 * Custom user class with additional checks and implementation of a 'root' user, who
 * has all permissions (`can()` always return true)
 */
class WebUser extends \yii\web\User
{
    const PUBLIC_ROLE = 'Public';

    /**
     * @var array Users with all permissions
     */
    public $rootUsers = [];

    /**
     * @var array Roles with all permissions
     */
    public $rootRoles = [];

    /**
     * Extended permission check with `Guest` role and `route`.
     *
     * @param string    $permissionName
     * @param array     $params
     * @param bool|true $allowCaching
     *
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        switch (true) {
            // root users have all permissions
            case Yii::$app->user->identity && (in_array(Yii::$app->user->identity->username, $this->rootUsers) || Yii::$app->user->identity->isAdmin):
                return true;
                break;
            case !empty($params['route']):
                Yii::trace("Checking route permissions for '{$permissionName}'", __METHOD__);

                return $this->checkAccessRoute($permissionName, $params, $allowCaching);
                break;
            default:
                return parent::can($permissionName, $params, $allowCaching);
        }
    }

    /**
     * Checks permissions from guest role, when no user is logged in.
     *
     * @param $permissionName
     * @param $params
     * @param $allowCaching
     *
     * @return bool
     */
    private function canGuest($permissionName, $params, $allowCaching)
    {
        $guestPermissions = $this->getAuthManager()->getPermissionsByRole(self::PUBLIC_ROLE);

        return array_key_exists($permissionName, $guestPermissions);
    }

    /**
     * Checks route permissions.
     *
     * Splits `permissionName` by underscore and match parts against more global rule
     * eg. a permission `app_site` will match, `app_site_foo`
     *
     * @param $permissionName
     * @param $params
     * @param $allowCaching
     *
     * @return bool
     */
    private function checkAccessRoute($permissionName, $params, $allowCaching)
    {
        $route = explode('_', $permissionName);
        $routePermission = '';
        foreach ($route as $part) {
            $routePermission .= $part;
            if (Yii::$app->user->id) {
                $canRoute = parent::can($routePermission, $params, $allowCaching);
            } else {
                $canRoute = $this->canGuest($routePermission, $params, $allowCaching);

                if (!$canRoute) {
                    $this->setLoginUrl();
                }
            }
            if ($canRoute) {
                return true;
            }
            $routePermission .= '_';
        }

        return false;
    }

    /**
     * Update the login url based on the cookie
     */
    protected function setLoginUrl()
    {
        $userModule = Yii::$app->getModule('user');
        if ($userModule->enableRememberLoginPage) {
            $cookieName = $userModule->originCookieName;
            if (Yii::$app->getRequest()->cookies[$cookieName]) {
                $origin = Yii::$app->getRequest()->cookies->getValue($cookieName);
                $this->loginUrl = base64_decode($origin);
            }
        }
    }
}
