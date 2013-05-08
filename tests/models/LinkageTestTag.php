<?php
/**
 * LinkageTestTag class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class LinkageTestTag
 *
 * Attributes:
 * @property int $id
 * @property string $name
 *
 * Core:
 * @method LinkageTestTag with()
 * @method LinkageTestTag find()
 * @method LinkageTestTag findByPk($pk)
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LinkageTestTag extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return LinkageTestTag
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'tag';
	}
}