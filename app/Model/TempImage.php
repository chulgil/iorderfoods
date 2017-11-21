<?php
/**
 * Created by PhpStorm.
 * User: codenavi
 * Date: 8/28/17
 * Time: 2:38 PM
 */
App::uses('AppModel','Model');
class TempImage extends AppModel
{
    var $name = 'TempImage';
    var $primaryKey = 'id';
    var $useDbConfig = 'default';

}