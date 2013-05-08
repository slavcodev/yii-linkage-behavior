<?php
/**
 * LinkageTestProfile class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestProfile
 *
 * Attributes:
 * @property int $user_id
 * @property string $name
 *
 * Core:
 * @method LinkageTestProfile with()
 * @method LinkageTestProfile find()
 * @method LinkageTestProfile findByPk($pk)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestProfile extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestProfile
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user_profile';
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'LinkageTestUser', 'user_id'),
		);
	}
}