PHP class to set all existing mailboxes on the server to have unlimited quotas.

The class uses the WHM external API to call the internal cPanel API functions.

Requires root access to the server.

Use

    <?php

    require 'cPanel_unlimited_mailquotas.class.php';

    $password = '';
    $server = '';

    $quotas = new cPanel_unlimited_mailquotas($server,$password);

    $quotas->run();

Copyright (C) 2014 Craig Parker <craig@paragon.net.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; If not, see <http:www.gnu.org/licenses/>.
