<?php
/**
 * LinkageTestUser class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestUser
 *
 * Attributes:
 * @property int $id
 *
 * Behaviors:
 * @property Linkage $linkage
 * @method LinkageTestUser link($name, $model, array $extraColumns = array())
 * @method LinkageTestUser unlink($name, $model, $delete = false)
 *
 * Core:
 * @method LinkageTestUser with()
 * @method LinkageTestUser find()
 * @method LinkageTestUser findByPk($pk)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestUser extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestUser
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user';
	}

	public function behaviors()
	{
		return array(
			'linkage' => array(
				'class' => 'Linkage',
			),
		);
	}

	public function relations()
	{
		return array(
			'profile' => array(self::HAS_ONE, 'LinkageTestProfile', 'user_id'),
			'posts' => array(self::HAS_MANY, 'LinkageTestPost', 'user_id'),
		);
	}
}