<?php

namespace App\Debugbars;

use App\Auth\Auth;
use Core\Utilities\Session;
use Tracy\IBarPanel;

class AuthDebugbar implements IBarPanel
{
    public function getTab()
    {
        return '
            <span>
                <span class="tracy-label">Skeleton Auth</span>
            </span>
        ';
    }

    public function getPanel()
    {
        $tmpl = "<h1>Skeleton Auth</h1>";
        $tmpl .= $this->configuration();
        $tmpl .= $this->authSession();

        return $tmpl;
    }

    private function configuration()
    {
        $auth = config('auth');

        $tmpl = '<div class="tracy-inner">
                    <div class="tracy-inner-container">
                        <table>
                            <caption>Configuration</caption>
                            <thead>
                                <tr>
                                    <th>Modules</th>
                                    <th>Settings</th>
                                    <th>Values</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach ($auth as $module => $settings_values) {
            $setting = key($settings_values);
            $value = is_bool($settings_values[$setting]) ?
                        ($settings_values[$setting] ? "true" : "false") :
                        $settings_values[$setting];

            $tmpl .= '
                <tr>
                    <td rowspan="'.count($settings_values).'">'.$module.'</td>
                    <td>'.$setting.'</td>
                    <td>'.$value.'</td>
                </tr>
            ';

            array_shift($settings_values);

            foreach ($settings_values as $setting => $value) {
                $value = is_bool($value) ?
                            ($value ? "true" : "false") :
                            $value;

                $tmpl .= '
                    <tr>
                        <td>'.$setting.'</td>
                        <td>'.$value.'</td>
                    </tr>
                ';
            }
        }

        $tmpl .= '
                        </tbody>
                    </table>
                <div>
            <div>
        ';

        return $tmpl;
    }

    private function authSession()
    {
        $tmpl = '<div class="tracy-inner">
                    <div class="tracy-inner-container">
                        <table>
                            <caption>Auth Session</caption>
                            <thead>
                                <tr>
                                    <th>Keys</th>
                                    <th>Values</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Auth User Id</td>
                                    <td>'.(Session::isExist('user_auth_id') ? Session::get('user_auth_id') : "N/A").'</td>
                                </tr>
                                <tr>
                                    <td>Logged In Token</td>
                                    <td>'.(Session::isExist('user_logged_in_token') ? Session::get('user_logged_in_token') : "N/A").'</td>
                                </tr>
                                <tr>
                                    <td>Logged In Time</td>
                                    <td>'.(Session::isExist('user_logged_in_time') ? Session::get('user_logged_in_time') : "N/A").'</td>
                                </tr>
                                <tr>
                                    <td>Authenticated User</td>
                                    <td>'.(Auth::check() ? Auth::user()->getFullName() : "N/A").'</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        ';

        return $tmpl;
    }
}
