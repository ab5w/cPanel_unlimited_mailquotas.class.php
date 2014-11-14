<?php

//
// Class to set all existing mailbox quotas to unlimited on cPanel.
//
//
// Copyright (C) 2014 Craig Parker <craig@paragon.net.uk>
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; If not, see <http://www.gnu.org/licenses/>.
//
//

class cPanel_unlimited_mailquotas {

    private $whmusername = 'root';
    private $whmpassword;
    private $server;

    public function __construct($srv, $pwd) {

        $this->server = $srv;
        $this->whmpassword = $pwd;

    }

    private function WHM_api_curl($query) {

        $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($curl, CURLOPT_HEADER,0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
            $header[0] = "Authorization: Basic " . base64_encode($this->whmusername . ":" . $this->whmpassword) . "\n\r";
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_URL, $query);

        $result = curl_exec($curl);

        $result = json_decode($result,true);

        curl_close($curl);

        return $result;

    }

    private function WHM_user_list() {

        $query = ("https://" . $this->server . ":2087/json-api/listaccts");

        $results = $this->WHM_api_curl($query);

        foreach ($results['acct'] as $result) {

            $userlist[] = $result['user'];

        }

        return $userlist;

    }

    private function cPanel_list_emailaccounts($user) {

        $query = ("https://" . $this->server . ":2087/json-api/cpanel?user=" . $user . "&cpanel_jsonapi_module=Email&cpanel_jsonapi_func=listpopswithdisk&cpanel_jsonapi_version=2");

        $result = $this->WHM_api_curl($query);

        $accounts['domain'] = $result['cpanelresult']['data'][0]['domain'];

        $emailaccounts = $result['cpanelresult']['data'];

        foreach ($emailaccounts as $emailaccount) {

            $emailaccount = explode('@', $emailaccount['email']);
            $accounts['mailboxes'][] = $emailaccount[0];

        }

        return $accounts;

    }

    private function cPanel_set_mailboxquota_unlimited($user) {

        $accounts = $this->cPanel_list_emailaccounts($user);

        $domain = $accounts['domain'];

        $mailboxes = $accounts['mailboxes'];

        foreach ($mailboxes as $mailbox) {

            $query = ("https://" . $this->server . ":2087/json-api/cpanel?user=" . $user . "&cpanel_jsonapi_module=Email&cpanel_jsonapi_func=editquota&cpanel_jsonapi_version=2&domain=" . $domain . "&email=" . $mailbox . "&quota=0");
            $quota = $this->WHM_api_curl($query);

        }

    }

    public function run() {

        error_reporting(E_ERROR | E_PARSE);

        $userlist = $this->WHM_user_list();

        foreach ($userlist as $user) {

            echo "Doing " . $user . "\n";
            $this->cPanel_set_mailboxquota_unlimited($user);

        }

    }

}