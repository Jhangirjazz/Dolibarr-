<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024 Your Name <your.email@example.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/recruitment/core/modules/recruitment/modules_recruitment.php
 *  \ingroup    recruitment
 *  \brief      File with parent class for generating Recruitment documents
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commondocgenerator.class.php';

/**
 *  Parent class for Recruitment documents
 */
abstract class ModelePDFRecruitment extends CommonDocGenerator
{
    // Put here features you want to include in all generated documents
    public $posxpiece;    // Position X of the numbering
    public $posxdesc;     // Position X of the description
    public $posxtva;      // Position X of the VAT
    public $posxup;       // Position X of the unit price
    public $posxqty;      // Position X of the quantity
    public $posxunit;     // Position X of the unit
    public $posxdiscount; // Position X of the discount
    public $posxtotalht;  // Position X of the total HT
    public $posxphoto;    // Position X of the photo

    /**
     * @var string Numbering module reference
     */
    public $num_ref;

    /**
     * @var string Error code (or message)
     */
    public $error = '';

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     * Return list of active generation modules
     *
     * @param DoliDB $db Database handler
     * @param integer $maxfilenamelength Max length of value to show
     * @return array                        List of templates
     */
    public static function liste_modeles($db, $maxfilenamelength = 0)
    {
        global $conf;

        $liste = array();
        $liste['standard'] = 'Standard';

        return $liste;
    }
    // phpcs:enable
}