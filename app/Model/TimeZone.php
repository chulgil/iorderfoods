<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class TimeZone extends AppModel {
     var $name = 'TimeZone';
     
     
     
      public $hasMany = array(
        'Store' => array(
            'className' => 'Store',
            'foreginKey'=>'time_zone_id',
        )
      );
      
      
      public function getTimezoneId($differenceInSeconds=null){                                
            $result = $this->find('first',array('conditions'=>array('TimeZone.difference_in_seconds'=>$differenceInSeconds)));
            return $result;
     }
     
     
}